<?php

namespace ngatngay;

use Symfony\Component\Yaml\Yaml as Yaml2;

class yaml
{
    public static function parse($data) {
        return Yaml2::parse($data);
    }
    
    public static function dump($data) {
        return Yaml2::dump($data);
    }
}
