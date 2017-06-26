<?php

namespace Riky\Exceptions\ErrorCode;

/**
 * Class RoError template
 *
 * @package App\Exceptions\ErrorCode
 */
class RoError
{
    const INT_MIN = 50001001;
    const INT_MAX = 50001002;
    const INT_REQUIRED = 50001003;
    const FIELD_REQUIRED = 50001004;
    const OUT_OF_ENUM = 50001005;
    const NUMBER_REQUIRED = 50001006;

    const RO_ERROR_NOTICE = [
        self::INT_MIN => '不能小于最小值:',
        self::INT_MAX => '不能大于最大值:',
        self::INT_REQUIRED => '需要整型',
        self::FIELD_REQUIRED => '不能为空',
        self::OUT_OF_ENUM => '不在指定范围',
        self::NUMBER_REQUIRED => '必须是数字',
    ];
}