<?php

namespace NgatNgay;

use Symfony\Component\Yaml\Yaml as Yaml2;

class Yaml
{
    public static function dump($data) {
        return Yaml2::dump($data);
    }
}
