<?php

namespace DOKU\Service;

use DOKU\Common\PaycodeGenerator;

class BniVa
{

    public static function generated($config, $params)
    {
        $params['targetPath'] = '/bni-virtual-account/v2/payment-code';
        return PaycodeGenerator::post($config, $params);
    }
}
