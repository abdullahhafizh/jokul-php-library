<?php

namespace DOKU\Common;

use DOKU\Common\Config;

use DOKU\Common\Utils;

class PaycodeGenerator
{
    public static function post($config, $params, $type = 'virtual_account')
    {
        $header = array();
        $data = array(
            "order" => array(
                "invoice_number" => $params['invoiceNumber'],
            ),
            $type . "_info" => array(
                "expired_time" => $params['expiryTime'],
                "reusable_status" => $params['reusableStatus'],
            ),
            "customer" => array(
                "name" => trim($params['customerName']),
                "email" => $params['customerEmail'] ?? ""
            ),
            "additional_info" => array(
                "integration" => array(
                    "name" => "php-library",
                    "version" => "2.1.0"
                )
            )
        );

        if (isset($params['amount'])) {
            $data['order']["amount"] = $params['amount'];
        } else {
            $data['order']["min_amount"] = $params['min_amount'];
            $data['order']["max_amount"] = $params['min_amount'];
        }

        if(isset($params['reff'])) {
            $data['virtual_account_info']['merchant_unique_reference'] = $params['reff'];
        }

        if ($type == 'online_to_offline' && isset($params['info'])) {
            $data[$type . '_info']['info'] = $params['info'];
        }

        $requestId = time() . rand(1,1000);
        $dateTime = gmdate("Y-m-d H:i:s");
        $dateTime = date(DATE_ISO8601, strtotime($dateTime) + (int)env('DOKU_SEC', 0));
        $dateTimeFinal = substr($dateTime, 0, 19) . "Z";

        $getUrl = Config::getBaseUrl($config['environment']);

        $targetPath = $params['targetPath'];
        $url = $getUrl . $targetPath;

        $header['Client-Id'] = $config['client_id'];
        $header['Request-Id'] = $requestId;
        $header['Request-Timestamp'] = $dateTimeFinal;
        $signature = Utils::generateSignature($header, $targetPath, json_encode($data), $config['shared_key']);

        try {
            \Log::info('DOKU URL: ' . $url);
            \Log::info("DOKU VA PAYLOAD: " , json_encode($data, JSON_PRETTY_PRINT));
        } catch (\Exception $e) {
        }
        
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Signature:' . $signature,
            'Request-Id:' . $requestId,
            'Client-Id:' . $config['client_id'],
            'Request-Timestamp:' . $dateTimeFinal,
            'Request-Target:' . $targetPath,

        ));

        $responseJson = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);

        if (is_string($responseJson) && $httpcode == 200) {
            try {
                \Log::info('CREATE ORDER LOG');
                \Log::info(json_encode(json_decode($responseJson), JSON_PRETTY_PRINT));
            } catch (\Exception $e) {
            }
            return json_decode($responseJson);
        } else {
            echo $responseJson;
            return null;
        }
    }
}
