<?php

// json5

namespace ngatngay;

class json
{
    public static function encode(...$args) {
        return json_encode(...$args);
    }

    public static function decode(string $data, ...$args) {
        $assoc = true;
        if (count($args) > 0) {
            $assoc = $args[0];
        }
        return json5_decode($data, $assoc, ...array_slice($args, 1));
    }

    public static function encode_file(string $file, ...$args) {
        return file_put_contents($file, json_encode(...$args));
    }

    public static function decode_file(string $file, ...$args) {
        $assoc = true;
        if (count($args) > 0) {
            $assoc = $args[0];
        }
        return json5_decode(file_get_contents($file), $assoc, ...array_slice($args, 1));
    }
}
