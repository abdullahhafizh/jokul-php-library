<?php

namespace DOKU\Service;

use DOKU\Common\Config;

use DOKU\Common\Utils;

class CreditCard
{

    public static function generated($config, $params)
    {
        $data = array(
            "order" => array(
                "amount" => $params['amount'],
                "invoice_number" => $params['invoiceNumber'],
            ),
            "customer" => array(
                (!empty($params['customerEmail']) ? "email" : "phone") => (!empty($params['customerEmail']) ? $params['customerEmail'] : $params['customerPhone'])
            ),
            "additional_info" => array(
                "integration" => array(
                    "name" => "php-library",
                    "version" => "2.1.0"
                )
            )
        );

        $getUrl = Config::getBaseUrl($config['environment']);

        $targetPath = '/credit-card/v1/payment-page';
        $url = $getUrl . $targetPath;

        $request_id = time() . rand(1,1000);

        $dateTime = gmdate("Y-m-d H:i:s");
        $dateTime = date(DATE_ISO8601, strtotime($dateTime));
        $dateTimeFinal = substr($dateTime, 0, 19) . "Z";
        $header['Request-Timestamp'] = $dateTimeFinal;
        $header['Client-Id'] = $config['client_id'];
        $header['Request-Id'] = $request_id;
        $signature = Utils::generateSignature($header, $targetPath, json_encode($data), $config['shared_key']);

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Signature:' . $signature,
            'Request-Id:' . $request_id,
            'Client-Id:' . $config['client_id'],
            'Request-Timestamp:' . $dateTimeFinal,
            'Request-Target:' . $targetPath,
        ));

        $responseJson = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);

        if (is_string($responseJson) && $httpcode == 200) {
            return json_decode($responseJson);
        } else {
            echo $responseJson;
            return null;
        }
    }
}
