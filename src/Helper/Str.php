<?php

namespace NgatNgay\Helper;

// best string helper

class Str
{
    /**
     * @param string $string
     * @return bool
     */
    public static function empty($string)
    {
        return strlen($string) === 0;
    }

    public static function wordCut(string $string, int $words = 35, string $end = '...'): string
    {
        preg_match('/^\s*+(?:\S++\s*+){1,' . $words . '}/u', $string, $matches);

        if (!isset($matches[0]) || self::length($string) === self::length($matches[0])) {
            return $string;
        }

        return rtrim($matches[0]) . $end;
    }

    public static function nl2br(string $str): string
    {
        return str_replace(PHP_EOL, '<br />', $str);
    }

    public static function br2nl(string $str): string
    {
        return preg_replace('#<br\s*/?>#i', PHP_EOL, $str);
    }

    public static function length(string $str): int
    {
        return mb_strlen($str);
    }

    /**
     * Chuyển đổi tiếng Việt sang tiếng Anh
     * @param string $str
     * @return string
     */
    public static function vn2en($str)
    {
        $unicode = [
            'a' => '/á|à|ả|ã|ạ|ă|ắ|ặ|ằ|ẳ|ẵ|â|ấ|ầ|ẩ|ẫ|ậ/',
            'd' => '/đ/',
            'e' => '/é|è|ẻ|ẽ|ẹ|ê|ế|ề|ể|ễ|ệ/',
            'i' => '/í|ì|ỉ|ĩ|ị/',
            'o' => '/ó|ò|ỏ|õ|ọ|ô|ố|ồ|ổ|ỗ|ộ|ơ|ớ|ờ|ở|ỡ|ợ/',
            'u' => '/ú|ù|ủ|ũ|ụ|ư|ứ|ừ|ử|ữ|ự/',
            'y' => '/ý|ỳ|ỷ|ỹ|ỵ/',
            'A' => '/Á|À|Ả|Ã|Ạ|Ă|Ắ|Ặ|Ằ|Ằ|Ẵ|Â|Ấ|Ầ|Ẩ|Ẫ|Ậ/',
            'D' => '/Đ/',
            'E' => '/É|È|Ẻ|Ẽ|Ẹ|Ê|Ế|Ề|Ể|Ễ|Ệ/',
            'I' => '/Í|Ì|Ỉ|Ĩ|Ị/',
            'O' => '/Ó|Ò|Ỏ|Õ|Ọ|Ô|Ố|Ồ|Ổ|Ỗ|Ộ|Ơ|Ớ|Ờ|Ở|Ỡ|Ợ/',
            'U' => '/Ú|Ù|Ủ|Ũ|Ụ|Ư|Ứ|Ừ|Ử|Ữ|Ự/',
            'Y' => '/Ý|Ỳ|Ỷ|Ỹ|Ỵ/'
        ];

        return preg_replace(array_values($unicode), array_keys($unicode), $str);
    }
}
