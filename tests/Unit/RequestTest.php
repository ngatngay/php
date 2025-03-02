<?php

use ngatngay\http\request;

test('test', function () {
    $r = new request();
    
    expect($r->isCLI())->tobeTrue();
    expect($r->isCLIServer())->tobeFalse();
    expect($r->getClientIp())->toBe('127.0.0.1');
    expect($r->getUserAgent())->toBe('');
    expect($r->getBaseUrl())->toBe('http://localhost');
});
