<?php

// json5

namespace ngatngay;

class json
{
    /**
     * @param mixed ...$args
     * @return string|false
     */
    public static function encode(...$args) {
        return json_encode(...$args);
    }

    /**
     * @param string $data
     * @param mixed ...$args
     * @return mixed
     */
    public static function decode($data, ...$args) {
        $assoc = true;
        if (count($args) > 0) {
            $assoc = $args[0];
        }
        return json5_decode($data, $assoc, ...array_slice($args, 1));
    }

    /**
     * @param string $file
     * @param mixed ...$args
     * @return int|false
     */
    public static function encode_file($file, ...$args) {
        return file_put_contents($file, json_encode(...$args));
    }

    /**
     * @param string $file
     * @param mixed ...$args
     * @return mixed
     */
    public static function decode_file($file, ...$args) {
        $assoc = true;
        if (count($args) > 0) {
            $assoc = $args[0];
        }
        return json5_decode(file_get_contents($file), $assoc, ...array_slice($args, 1));
    }
}
