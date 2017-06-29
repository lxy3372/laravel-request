<?php

namespace Riky\Utils;

Use Riky\Exceptions\RoException;
Use Riky\Exceptions\ErrorCode\RoError;


/**
 * 验证Request Object Util
 * 
 * Class VerifyRoUtil
 *
 */
class VerifyRoUtil
{
    /**
     * 整型验证
     *
     * @param     $name
     * @param     $value
     * @param int $min
     * @param int $max
     * @return bool
     * @throws RoException
     */
    public static function int($name, $value, $min = 0, $max = 4294967295)
    {
        if (is_null($value)) {
            return true;
        }

        if ($value == '') {
            $error_value = ['field' => $name];
            throw new RoException(RoError::INT_REQUIRED, $error_value, $name);
        }

        if (is_string($value)) {
            if ($value[0] == '-' && !ctype_digit(substr($value, 1))) {
                $error_value = ['field' => $name];
                throw new RoException(RoError::INT_REQUIRED, $error_value, $name);
            } else if (!ctype_digit($value)) {
                $error_value = ['field' => $name];
                throw new RoException(RoError::INT_REQUIRED, $error_value, $name);
            }
        }

        if ($value < $min) {
            $error_value = ['field' => $name, 'value' => $min];
            throw new RoException(RoError::INT_MIN, $error_value, $name);
        }

        if ($value > $max) {
            $error_value = ['field' => $name, 'value' => $max];
            throw new RoException(RoError::INT_MAX, $error_value, $name);
        }
    }

    /**
     *
     * 枚举验证
     * @param $name
     * @param $value
     * @return bool
     * @throws RoException
     */
    public static function enum($name, $value)
    {
        $prams = func_get_args();
        if (is_null($value)) {
            return true;
        }

        $eum = array_slice($prams, 2);
        if (!in_array($value, $eum)) {
            throw new RoException(RoError::OUT_OF_ENUM, self::formatErrorValue($name));
        }
    }

    /**
     * 数字验证
     *
     * @param     $name
     * @param     $value
     * @param int $min
     * @param int $max
     * @return bool
     * @throws RoException
     */
    public static function number($name, $value, $min = 0, $max = 4294967295)
    {
        if (is_null($value)) {
            return true;
        }

        if (!is_numeric($value)) {
            $error_value = ['field' => $name];
            throw new RoException(RoError::NUMBER_REQUIRED, $error_value, $name);
        }

        if ($value < $min) {
            $error_value = ['field' => $name, 'value' => $min];
            throw new RoException(RoError::INT_MIN, $error_value, $name);
        }

        if ($value > $max) {
            $error_value = ['field' => $name, 'value' => $max];
            throw new RoException(RoError::INT_MAX, $error_value, $name);
        }
    }

    /**
     * 认证参数必填
     *
     * @param $name
     * @param $value
     *
     * @throws RoException
     */
    public static function required($name, $value)
    {
        if (is_null($value) || $value === '') {
            $error_value = ['field' => $name];
            throw new RoException(RoError::FIELD_REQUIRED, $error_value, $name);
        }
    }

    /**
     * 格式化错误返回
     * @param        $filed
     * @param string $value
     * @return array
     */
    private static function formatErrorValue($filed, $value = '')
    {
        return [
            'field' => $filed,
            'value' => $value
        ];
    }

}