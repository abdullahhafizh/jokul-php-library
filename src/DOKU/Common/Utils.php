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
}
