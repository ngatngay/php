<?php

use NgatNgay\Helper\Arr;
use NgatNgay\Helper\Str;
use NgatNgay\Helper\FS;

test('array test', function () {
    $arr = [];
    for ($i = 0; $i < 95; $i++) {
        $arr[] = $i;
    }

    expect(Arr::getFromPage($arr, 10))
        ->toEqual([90, 91, 92, 93, 94]);
});

test('strings test', function () {
    expect(Str::wordCut('Đụ má mày chửi thề con cặc', 3))
        ->toEqual('Đụ má mày...');

    expect(Str::wordCut('Đụ má mày', 3))
        ->toEqual('Đụ má mày');

    expect(Str::vn2en('Xin chào'))
        ->toEqual('Xin chao');

    expect(Str::vn2en('Chênh lỆch Áp suất'))
        ->toEqual('Chenh lEch Ap suat');

    expect(Str::empty(''))->toBeTrue;
    expect(Str::empty('1'))->toBeFalse();
});

test('file test', function () {
    expect(FS::readableSize(213))
        ->toEqual('213 B');

    expect(FS::readableSize(1024))
        ->toEqual('1 KB');
});
