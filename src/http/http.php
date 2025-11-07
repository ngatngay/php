<?php

namespace nightmare\http;

class http
{
    /**
     * @param mixed $data
     * @param int $status
     * @param array $headers
     * @return response
     */
    public static function response($data = null, $status = 200, $headers = [])
    {
        return new response($data, $status, $headers);
    }

    /**
     * @param string $url
     * @param int $status
     * @return void
     */
    public static function redirect($url, $status = 301)
    {
        @ob_end_clean();
        http_response_code($status);
        header('Location: ' . $url);
        exit;
    }

    /**
     * @return void
     */
    public static function refresh()
    {
        @ob_end_clean();
        header('Refresh: 0');
        exit;
    }
}
