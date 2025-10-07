<?php

namespace ngatngay;

use ngatngay\http\response;

function response(mixed $data = null, int $status = 200, array $headers = []): response
{
    return new response($data, $status, $headers);
}

function redirect(string $url, int $status = 301): void
{
    @ob_end_clean();
    http_response_code($status);
    header('Location: ' . $url);
    exit;
}

function refresh(): void
{
    ob_end_clean();
    header('Refresh:0');
    exit;
}
