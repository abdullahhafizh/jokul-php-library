<?php

namespace DOKU\Service;

use DOKU\Common\PaycodeGenerator;

class PermataVa
{

    public static function generated($config, $params)
    {
        $params['targetPath'] = '/permata-virtual-account/v2/payment-code';
        return PaycodeGenerator::post($config, $params);
    }
}
