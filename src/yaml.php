<?php

namespace nightmare;

use Symfony\Component\Yaml\Yaml as Yaml2;

class yaml
{
    /**
     * @param string $data
     * @return mixed
     */
    public static function parse($data) {
        return Yaml2::parse($data);
    }
    
    /**
     * @param mixed $data
     * @param int $inline
     * @param int $indent
     * @param int $flags
     * @return string
     */
    public static function dump($data, $inline = 2, $indent = 4, $flags = 0) {
        return Yaml2::dump($data, $inline, $indent, $flags);
    }

    /**
     * @param string $filename
     * @param mixed $data
     * @param int $inline
     * @param int $indent
     * @param int $flags
     * @return void
     */
    public static function dump_file($filename, $data, $inline = 2, $indent = 4, $flags = 0) {
        file_put_contents($filename, Yaml2::dump($data, $inline, $indent, $flags));
    }

    /**
     * @param string $filename
     * @return mixed
     */
    public static function parse_file($filename) {
        return Yaml2::parseFile($filename);
    }
}
