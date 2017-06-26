<?php

namespace Riky\Exceptions;

use Riky\Exceptions\ErrorCode\RoError;

/**
 * Class RoException
 */
class RoException extends BaseException
{
    public $attribute;

    protected $lang_filename = 'ro_error.';



    public function __construct($code, array $value = [], $attribute = '', \Exception $exception = null)
    {
        $this->attribute = $attribute;
        $notice = ['NOTICE' => ''];
	    $notice['NOTICE'] = ','.$value['field'].RoError::RO_ERROR_NOTICE[$code].(empty($value['value']) ? '' : $value['value']);
        parent::__construct($code, $notice);
    }

    public function getAttribute()
    {
        return $this->attribute;
    }
}