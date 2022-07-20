<?php

namespace DOKU\Common;

class Utils
{
    public static function generateSignature($headers, $targetPath, $body = false, $secret)
    {
        $rawSignature = "Client-Id:" . $headers['Client-Id'] . "\n"
        . "Request-Id:" . $headers['Request-Id'] . "\n"
        . "Request-Timestamp:" . $headers['Request-Timestamp'] . "\n"
        . "Request-Target:" . $targetPath;
        if ($body) {
            $digest = base64_encode(hash('sha256', $body, true));
            $rawSignature .=  "\n" . "Digest:" . $digest;
        }

        $signature = base64_encode(hash_hmac('sha256', $rawSignature, $secret, true));
        return 'HMACSHA256=' . $signature;
    }

    public static function generateUTC()
    {
        $dateTime = strtotime(gmdate("Y-m-d H:i:s"));

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => "http://worldtimeapi.org/api/timezone/GMT",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_POSTFIELDS => "",
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if (!$err) {
            $response = json_decode($response);
            if(!empty($response->unixtime)) {
                $dateTime = $response->unixtime;
            }
        }

        $dateTime = date(DATE_ISO8601, $dateTime + (int)env('DOKU_SEC', 0));
        $dateTimeFinal = substr($dateTime, 0, 19) . "Z";
        \Log::info($dateTimeFinal);
        return $dateTimeFinal;
    }
}
