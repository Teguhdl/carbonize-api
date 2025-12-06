<?php

namespace App\Helpers;

class CustomToken
{
    public static function create($data)
    {
        $header = base64_encode(json_encode([
            'alg' => 'HS256',
            'typ' => 'CUST'
        ]));

        $payload = base64_encode(json_encode($data));

        $secret = env('CUSTOM_TOKEN_SECRET', 'mysecretkey');

        $signature = hash_hmac('sha256', "$header.$payload", $secret);

        return "$header.$payload.$signature";
    }

    public static function validate($token)
    {
        $parts = explode('.', $token);

        if (count($parts) !== 3) return false;

        list($header, $payload, $signature) = $parts;

        $secret = env('CUSTOM_TOKEN_SECRET');
        $validSignature = hash_hmac('sha256', "$header.$payload", $secret);

        if (!hash_equals($validSignature, $signature)) {
            return false; 
        }

        $payloadData = json_decode(base64_decode($payload), true);

        if (isset($payloadData['exp']) && time() > $payloadData['exp']) {
            return false; 
        }

        return $payloadData;
    }
}
