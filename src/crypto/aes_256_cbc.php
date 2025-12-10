<?php

namespace nightmare\crypto;

class aes_256_cbc
{
    public static function get_key($input_key)
    {
        return hash('sha256', $input_key, true);
    }

    public static function encrypt($plaintext, $input_key)
    {
        $key = self::get_key($input_key);
        $iv = openssl_random_pseudo_bytes(16);

        $ciphertext = openssl_encrypt(
            $plaintext,
            'AES-256-CBC',
            $key,
            OPENSSL_RAW_DATA,
            $iv
        );

        $result = base64_encode($iv . $ciphertext);

        return $result;
    }

    public static function decrypt($encrypted_data, $input_key)
    {
        $key = self::get_key($input_key);
        $raw = base64_decode($encrypted_data);
        $iv = substr($raw, 0, 16);
        $ciphertext = substr($raw, 16);

        $decrypted = openssl_decrypt(
            $ciphertext,
            'AES-256-CBC',
            $key,
            OPENSSL_RAW_DATA,
            $iv
        );

        return $decrypted;
    }
}
