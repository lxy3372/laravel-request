<?php

namespace Riky\Requests;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\MessageBag;
use ReflectionClass;
use ReflectionProperty;
use Riky\Utils\VerifyRoUtil;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;

/**
 * Request Object
 *
 * Class BaseRO [Request Object]
 */
Class BaseRo
{
	/**
	 * eg: 平台
	 *
	 * @required
	 * @integer
	 * @min|1
	 */
	public $plat_form;


	/**
	 * 字段映射
	 */
	protected $maps = [];

	/**
	 * 请求对象，方便扩展
	 */
	private $request_handler = null;

	private $error_handle = null;

	/**
	 * BaseRo constructor.
	 *
	 * @param array $request custom request params
	 */
	function __construct($request = array())
	{
		//support for laravel
		if (function_exists('app') && app()->request) {
			$this->request_handler = app()->request;
			$params = app()->request->all();
		}else {
			$request_obj = new SymfonyRequest();
			$request_handler = $request_obj->createFromGlobals();
			$params = array_merge($request_handler->query->all(), $request_handler->request->all());
		}
		$request = array_merge($params, $request);
		$this->inject($request);
		$this->before();
		$this->checkAttr();
		$this->after();
	}

	/**
	 *  inject attribute
	 *
	 * @param array $request
	 */
	public function inject($request)
	{
		// 处理字段映射
		foreach ($this->maps as $key => $field) {
			if (array_key_exists($key, $request)) {
				$this->{$field} = $request[$key];
			}
		}

		foreach ($this as $key => &$item) {
			if (array_key_exists($key, $request)) {
				$item = $request[$key];
			}
		}
	}

	/**
	 * Object to Array
	 *
	 * @param bool $unsetNull
	 *
	 * @return array
	 */
	public function toArray($unsetNull = false)
	{
		$arr = [];
		foreach ($this as $key => $item) {
			if($unsetNull && !$item) continue;
			if(in_array($key, ['request_handler', 'error_handler'])) continue;
			$arr[$key] = $item;
		}

		return $arr;
	}


	/**
	 * hook
	 */
	protected function before() {

	}

	/**
	 * validator attribute
	 */
	protected function checkAttr()
	{
		$class = new ReflectionClass($this);

		$properties = $class->getProperties(ReflectionProperty::IS_PUBLIC);
		$params_arr = [];
		$data_arr = [];
		$method_arr = ['accepted', 'active_url', 'after', 'alpha', 'alpha_dash', 'alpha_numeric', 'array', 'before (date)', 'between', 'boolean', 'confirmed', 'date', 'date_format', 'different', 'digits', 'digits_between', 'email', 'exists (database)', 'image (file)', 'in', 'integer', 'ip', 'json', 'max', 'mimes', 'min', 'not_in', 'numeric', 'regular', 'required', 'required_if', 'required_unless', 'required_with', 'required_with_all', 'required_without', 'required_without_all', 'same', 'size', 'string', 'timezone', 'unique', 'url'
		];
		foreach($properties as &$property) {
			$docblock = $property->getDocComment();
			$name = $property->getName();
			$data_arr[$name] = $this->{$name};
			$doclines = explode("\n", str_replace(array("\r\n","\r"),"\n", $docblock));
			$rules = '';
			foreach($doclines as $line) {
				$line_arr = explode('@', $line);
				if (count($line_arr) < 2) continue;
				$match = explode(" ", $line_arr[1])[0];
				@list($method, $param) = explode("|", $match);
				if(!in_array($method, $method_arr)) {
					continue;
				}
				$rules .= ($rules ? '|':'').$method;
				if($param) {
					$rules .=':'.$param;
				}

			}
			$params_arr[$name] = $rules;
		}
		$ret = Validator::make($data_arr, $params_arr);
		if($ret->fails()){
			$this->handleException($ret->errors());
		}
	}

	/**
	 * @param MessageBag $error
	 */
	protected function handleException(MessageBag $error)
	{
		$this->error_handle = $error;
	}

	/**
	 * 判断结果
	 *
	 * @return MessageBag | bool
	 */
	public function fails()
	{
		if($this->error_handle) {
			return $this->error_handle;
		} else {
			return false;
		}
	}

	/**
	 * hook
	 */
	protected function after()
	{

	}

}