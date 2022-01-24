<?php

namespace DOKU\Service;

use DOKU\Common\Config;

use DOKU\Common\Utils;

class Cancel
{

    public static function cancelation($config, $params)
    {
        $getUrl = Config::getBaseUrl($config['environment']);

        $baseTargetPath = '/' . $params['base'];
        $targetPath = $baseTargetPath . '/v2/payment-code';
        $url = $getUrl . $targetPath;

        unset($params['base']);

        $request_id = time() . rand(1,1000);

        $dateTime = gmdate("Y-m-d H:i:s");
        $dateTime = date(DATE_ISO8601, strtotime($dateTime));
        $dateTimeFinal = substr($dateTime, 0, 19) . "Z";
        $header['Request-Timestamp'] = $dateTimeFinal;
        $header['Client-Id'] = $config['client_id'];
        $header['Request-Id'] = $request_id;
        $signature = Utils::generateSignature($header, $targetPath, json_encode($params), $config['shared_key']);

        try {
            \Log::info('DOKU URL: ' . $url);
            \Log::info("DOKU VA PAYLOAD: " . json_encode($data, JSON_PRETTY_PRINT));
        } catch (\Exception $e) {
        }


        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
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
        $err = curl_error($ch);

        curl_close($ch);

        if ($err) return $err;

        if (is_string($responseJson) && $httpcode == 200) {
            try {
                \Log::info('GET STATUS LOG');
                \Log::info(json_encode(json_decode($responseJson), JSON_PRETTY_PRINT));
            } catch (\Exception $e) {
            }
        } else {
            try {
                \Log::info($responseJson);
            } catch (\Exception $e) {
            }
        }

        return json_decode($responseJson);
    }
}
