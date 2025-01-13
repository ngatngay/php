<?php

namespace NgatNgay;

use NgatNgay\Http\Request;
use NgatNgay\Http\Response;

function request(): Request
{
    static $instance = null;

    if ($instance === null) {
        $instance = new Request();
    }

    return $instance;
}

function response($data = null, $status = 200, $headers = []): Response
{
    return new Response($data, $status, $headers);
}

function redirect(string $url, int $status = 301)
{
    ob_end_clean();
    http_response_code($status);
    header('Location: ' . $url);
    exit;
}

function refresh()
{
    ob_end_clean();
    header('Refresh:0');
    exit;
}
