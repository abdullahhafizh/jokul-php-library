<?php

namespace DOKU\Service;

use DOKU\Common\Config;

use DOKU\Common\Utils;

class GetStatus
{

    public static function statused($config, $id)
    {
        $getUrl = Config::getBaseUrl($config['environment']);

        $targetPath = '/orders/v1/status/' . $id;
        $url = $getUrl . $targetPath;

        $request_id = time() . rand(1,1000);

        $dateTime = gmdate("Y-m-d H:i:s");
        $dateTime = date(DATE_ISO8601, strtotime($dateTime));
        $dateTimeFinal = substr($dateTime, 0, 19) . "Z";
        $header['Request-Timestamp'] = $dateTimeFinal;
        $header['Client-Id'] = $config['client_id'];
        $header['Request-Id'] = $request_id;
        $signature = Utils::generateSignature($header, $targetPath, false, $config['shared_key']);

        $ch = curl_init($url);
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
