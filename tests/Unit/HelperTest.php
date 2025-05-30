<?php

use ngatngay\arr;
use ngatngay\str;
use ngatngay\fs;

test('array test', function () {
    $arr = [];
    for ($i = 0; $i < 95; $i++) {
        $arr[] = $i;
    }

    expect(arr::get_by_page(10, 10, $arr))
        ->toEqual([90, 91, 92, 93, 94]);
});

test('strings test', function () {
    expect(Str::word_cut('Đụ má mày chửi thề con cặc', 3))
        ->toEqual('Đụ má mày...');

    expect(Str::word_cut('Đụ má mày', 3))
        ->toEqual('Đụ má mày');

    expect(Str::vn2en('Xin chào'))
        ->toEqual('Xin chao');

    expect(Str::vn2en('Chênh lỆch Áp suất'))
        ->toEqual('Chenh lEch Ap suat');

    expect(Str::empty(''))->toBeTrue;
    expect(Str::empty('1'))->toBeFalse();
});

test('file test', function () {
    expect(fs::readable_size(213))
        ->toEqual('213 B');

    expect(fs::readable_size(1024))
        ->toEqual('1 KB');
});
