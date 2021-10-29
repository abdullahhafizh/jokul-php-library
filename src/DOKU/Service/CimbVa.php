<?php

namespace DOKU\Service;

use DOKU\Common\PaycodeGenerator;

class CimbVa
{

    public static function generated($config, $params)
    {
        $params['targetPath'] = '/cimb-virtual-account/v2/payment-code';
        return PaycodeGenerator::post($config, $params);
    }
}
