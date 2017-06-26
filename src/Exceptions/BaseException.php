<?php

namespace Riky\Exceptions;

use Exception;

/**
 * 业务自定义异常类
 * 
 * Class BaseException
 *
 */
class BaseException extends Exception
{
    
    protected $lang_filename = 'error.';

    /**
     * @var array  抛出异常需要携带额外附属data
     */
    public $data;

    public function __construct($code, array $value = [], array $data = []) {
        $notice = trans($this->lang_filename.$code, $value);
        $this->data = $data;
        parent::__construct($notice, $code);
    }

    /**
     * 砰
     * 
     * @warning 不要触摸-小心爆炸
     * @param $code
     * 
     * @throws static | BaseException
     */
    public static function touch($code)
    {
        throw new static($code);
    }

}


