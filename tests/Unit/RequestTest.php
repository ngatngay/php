<?php

use ngatngay\http\request;

test('test', function () {
    $r = new request();
    
    expect($r->is_cli())->tobeTrue();
    expect($r->is_cli_server())->tobeFalse();
    expect($r->get_client_ip())->toBe('127.0.0.1');
    expect($r->get_user_agent())->toBe('');
    expect($r->get_base_url())->toBe('http://localhost');
});
