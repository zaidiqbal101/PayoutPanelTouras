<?php

namespace App\Helpers;

class AESHelper
{
    public static function encrypt($text, $key, $iv = "0123456789abcdef", $size = 16)
    {
        $pad = $size - (strlen($text) % $size);
        $padtext = $text . str_repeat(chr($pad), $pad);
        $encrypted = openssl_encrypt(
            $padtext,
            "AES-256-CBC",
            base64_decode($key),
            OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING,
            $iv
        );

        return base64_encode($encrypted);
    }

    public static function decrypt($encryptedText, $key, $iv = "0123456789abcdef")
    {
        $crypt = base64_decode($encryptedText);
        $padtext = openssl_decrypt(
            $crypt,
            "AES-256-CBC",
            base64_decode($key),
            OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING,
            $iv
        );

        $pad = ord($padtext[strlen($padtext) - 1]);

        if ($pad > strlen($padtext)) {
            return false;
        }

        if (strspn($padtext, $padtext[strlen($padtext) - 1], strlen($padtext) - $pad) != $pad) {
            return "Error";
        }

        return substr($padtext, 0, -$pad);
    }
}
