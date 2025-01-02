<?php

use NgatNgay\Helper\Request;

test('test', function () {
    $r = new Request();
    
    expect($r->isCLI())->tobeTrue();
    expect($r->isCLIServer())->tobeFalse();
    expect($r->getIp())->toBe('127.0.0.1');
    expect($r->getUserAgent())->toBe('');
    expect($r->getBaseUrl())->toBe('http://localhost');
});
