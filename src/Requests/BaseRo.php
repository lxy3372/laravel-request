<?php

namespace Riky\Requests;

use ReflectionClass;
use ReflectionProperty;
use Riky\Utils\VerifyRoUtil;
use Riky\Exception\RoException;
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
     * @var int|1,3
     */
    public $plat_form=1;


	/**
	 * 字段映射
	 */
    protected $maps = [];

	/**
	 * 请求对象，方便扩展
	 */
	private $request_handler = null;
	
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
        } else {
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
	        if($key == 'request_handler') continue;
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
        foreach($properties as &$property) {
            $docblock = $property->getDocComment();
            $name = $property->getName();
            $doclines = explode("\n", str_replace(array("\r\n","\r"),"\n", $docblock));
            foreach($doclines as $line) {
                $line_arr = explode('@', $line);
                if (count($line_arr) < 2) continue;
                $match = explode(" ", $line_arr[1])[0];
                @list($method, $param) = explode("|", $match);
                if (!method_exists(VerifyRoUtil::class, $method)) continue;
                try {
                    if (is_string($param)) {
                        $args = explode(',', $param);
                        array_unshift($args, $name, $this->$name);
                        call_user_func_array([VerifyRoUtil::class, $method], $args);
                    } else {
                        VerifyRoUtil::$method($name, $this->$name);
                    }
                } catch (RoException $e) {
                    $this->handleException($e);
                }

            }
        }
    }

    protected function handleException(RoException $e)
    {
        throw $e;
    }

	/**
	 * hook
	 */
    protected function after()
    {

    }

}
