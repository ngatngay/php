<?php

namespace ngatngay;

use Symfony\Component\Yaml\Yaml as Yaml2;

class yaml
{
    public static function parse(string $data) {
        return Yaml2::parse($data);
    }
    
    public static function dump(mixed $data, int $inline = 2, int $indent = 4, int $flags = 0): string {
        return Yaml2::dump($data, $inline, $indent, $flags);
    }

    public static function dump_file(string $filename, mixed $data, int $inline = 2, int $indent = 4, int $flags = 0): void {
        file_put_contents($filename, Yaml2::dump($data, $inline, $indent, $flags));
    }

    public static function parse_file(string $filename) {
        return Yaml2::parseFile($filename);
    }
}
