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

        $header['Request-Timestamp'] = Utils::generateUTC();
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
            'Request-Timestamp:' . Utils::generateUTC(),
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
