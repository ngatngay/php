<?php

namespace nightmare;

class str
{
    /**
     * @param string $string
     * @return bool
     */
    public static function empty($string)
    {
        return strlen($string) === 0;
    }

    /**
     * @param string $string
     * @param int $words
     * @param string $end
     * @return string
     */
    public static function word_cut($string, $words = 35, $end = '...')
    {
        preg_match('/^\s*+(?:\S++\s*+){1,' . $words . '}/u', $string, $matches);

        if (!isset($matches[0]) || self::length($string) === self::length($matches[0])) {
            return $string;
        }

        return rtrim($matches[0]) . $end;
    }

    /**
     * @param string $str
     * @return string
     */
    public static function br2nl($str)
    {
        return preg_replace('#<br\s*/?>#i', PHP_EOL, $str);
    }

    /**
     * @param string $str
     * @return int
     */
    public static function length($str)
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

    /**
     * @param string $needle
     * @param string $replace
     * @param string $haystack
     * @return string
     */
    public static function replace_first($needle, $replace, $haystack)
    {
        $pos = strpos($haystack, $needle);

        if ($pos !== false) {
            return substr_replace($haystack, $replace, $pos, strlen($needle));
        }

        return $haystack;
    }
    
    /**
     * @param string $text
     * @return string
     */
    public static function to_unix_newline($text)
    {
        return str_replace([
            "\r\n", // windows
            "\r" // mac old
        ], "\n", $text);
    }

    /**
     * @param string $str
     * @return string
     */
    public function to_url($str) {
        $str = trim($str);
        $str = strtolower($str);
        $str = self::vn2en($str);

        $str = html_entity_decode($str, ENT_QUOTES, 'UTF-8');

        $str = str_replace('&', '-and-', $str);
        $str = str_replace(' ', '-', $str);

        $str = preg_replace('#[^a-z0-9\-]#', '', $str);
        $str = preg_replace('#[-]{2,}#', '-', $str);
        $str = trim($str, '-');

        return $str;
    }
}
