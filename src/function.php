<?php

namespace ngatngay;

use ngatngay\http\response;

function response($data = null, $status = 200, $headers = []): response
{
    return new response($data, $status, $headers);
}

function redirect(string $url, int $status = 301)
{
    @ob_end_clean();
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
