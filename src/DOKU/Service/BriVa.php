<?php

namespace DOKU\Service;

use DOKU\Common\PaycodeGenerator;

class BriVa
{

    public static function generated($config, $params)
    {
        $params['targetPath'] = '/bri-virtual-account/v2/payment-code';
        return PaycodeGenerator::post($config, $params);
    }
}
