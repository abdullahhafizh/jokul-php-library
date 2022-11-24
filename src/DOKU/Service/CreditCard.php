<?php

namespace DOKU\Service;

use DOKU\Common\Config;

use DOKU\Common\Utils;

class CreditCard
{

    public static function generated($config, $params)
    {
        $data = [
            'order' => [
                "amount" => (int)$params['amount'],
                "invoice_number" => $params['invoiceNumber'],
                'auto_redirect' => (bool)($params['auto_redirect'] ?? false),
            ],
            "additional_info" => array(
                "integration" => array(
                    "name" => "php-library",
                    "version" => "2.1.0"
                )
            )
        ];


        if (!empty($params['tenor'])) $data['override_configuration']['allow_tenor'] = $params['tenor'];
        
        if (!empty($params['language'])) $data['override_configuration']['themes']['language'] = $params['language'];

        if (!empty($params['background_color'])) $data['override_configuration']['themes']['background_color'] = $params['background_color'];
        
        if (!empty($params['font_color'])) $data['override_configuration']['themes']['font_color'] = $params['font_color'];
        
        if (!empty($params['button_background_color'])) $data['override_configuration']['themes']['button_background_color'] = $params['button_background_color'];
        
        if (!empty($params['button_font_color'])) $data['override_configuration']['themes']['button_font_color'] = $params['button_font_color'];

        if (!empty($params['id'])) $data['customer']['id'] = $params['id'];

        if (!empty($params['name'])) $data['customer']['name'] = $params['name'];
        
        if (!empty($params['email'])) $data['customer']['email'] = $params['email'];
        
        if (!empty($params['phone'])) $data['customer']['phone'] = $params['phone'];
        
        if (!empty($params['address'])) $data['customer']['address'] = $params['address'];

        if (!empty($params['country'])) $data['customer']['country'] = $params['country'];

        if (!empty($params['callback_url'])) $data['order']['callback_url'] = $params['callback_url'];

        if (!empty($params['failed_url'])) $data['order']['failed_url'] = $params['failed_url'];

        if (!empty($params['token'])) $data['card']['token'] = $params['token'];

        if (!empty($params['save'])) $data['card']['save'] = (bool)$params['save'];

        if (!empty($params['line_items'])) {
            foreach ($params['line_items'] as $key => $value) {
                $data['order']['line_items'][] = [
                    'name' => $value['name'],
                    'price' => (int)$value['price'],
                    'quantity' => (int)$value['quantity']
                ];
            }
        }

        if (!empty($params['promo'])) {
            foreach ($params['promo'] as $key => $value) {
                $data['override_configuration']['promo'][] = [
                    'bin' => $value['bin'],
                    'discount_amount' => (int)$value['discount_amount']
                ];
            }
        }

        $getUrl = Config::getBaseUrl($config['environment']);

        $targetPath = '/credit-card/v1/payment-page';
        $url = $getUrl . $targetPath;

        $request_id = time() . rand(1,1000);

        $header['Request-Timestamp'] = Utils::generateUTC();
        $header['Client-Id'] = $config['client_id'];
        $header['Request-Id'] = $request_id;
        $signature = Utils::generateSignature($header, $targetPath, json_encode($data), $config['shared_key']);

        try {
        \Log::info('Card Not Present Request: ' . json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
        } catch(\Exception $e) {

        }

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
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

        curl_close($ch);

        if (is_string($responseJson) && $httpcode == 200) {
            \Log::info('Card Not Present Response: ' . json_encode(json_decode($responseJson), JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
            return json_decode($responseJson);
        } else {
            try {
                \Log::info($responseJson);
            } catch (\Exception $e) {
            }
            return null;
        }
    }
}
