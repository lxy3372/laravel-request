<?php

require './../src/Requests/BaseRo.php';
require './../vendor/autoload.php';

use Riky\Requests\BaseRo;

var_dump((new BaseRo())->toArray());
