<?php

namespace nightmare;

use nightmare\http\response;

/**
 * @param mixed $data
 * @param int $status
 * @param array $headers
 * @return response
 */
function response($data = null, $status = 200, $headers = [])
{
    return new response($data, $status, $headers);
}

/**
 * @param string $url
 * @param int $status
 * @return void
 */
function redirect($url, $status = 301)
{
    @ob_end_clean();
    http_response_code($status);
    header('Location: ' . $url);
    exit;
}

/**
 * @return void
 */
function refresh()
{
    ob_end_clean();
    header('Refresh:0');
    exit;
}
