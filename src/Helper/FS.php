<?php

namespace NgatNgay\Helper;

// file system
class FS
{
    /*
     * file, file1, file2...
     */
    function nameIncrement(string $file_name_body, string $file_ext): string
    {
        $i = 1;
        $file_exists = true;

        do {
            $file_save = $file_name_body . $i . '.' . $file_ext;

            if (!file_exists($file_save)) {
                $file_exists = false;
            }

            $i++;
        } while ($file_exists);

        return $file_save;
    }

    public static function getExtension(string $name): string
    {
        $name = strval($name);

        preg_match('/\.([^\.]*$)/', $name, $extension);

        if (is_array($extension) && sizeof($extension) > 0) {
            return strtolower($extension[1]);
        }

        return '';
    }

    /**
     * @param $fileSize string
     * @return string
     */
    public static function readableSize($fileSize)
    {
        $size = floatval($fileSize);

        if ($size < 1024) {
            $s = $size . ' B';
        } elseif ($size < 1048576) {
            $s = round($size / 1024, 2) . ' KB';
        } elseif ($size < 1073741824) {
            $s = round($size / 1048576, 2) . ' MB';
        } elseif ($size < 1099511627776) {
            $s = round($size / 1073741824, 2) . ' GB';
        } elseif ($size < 1125899906842624) {
            $s = round($size / 1099511627776, 2) . ' TB';
        } elseif ($size < 1152921504606846976) {
            $s = round($size / 1125899906842624, 2) . ' PB';
        } elseif ($size < 1.1805916207174E+21) {
            $s = round($size / 1152921504606846976, 2) . ' EB';
        } elseif ($size < 1.2089258196146E+24) {
            $s = round($size / 1.1805916207174E+21, 2) . ' ZB';
        } else {
            $s = round($size / 1.2089258196146E+24, 2) . ' YB';
        }

        return $s;
    }

    public static function remove($path)
    {
        if (file_exists($path)) {
            unlink($path);
        }
    }

    public static function removeDir($dir, $remove_this = true)
    {
        if ($objs = glob($dir . "/*")) {
            foreach ($objs as $obj) {
                is_dir($obj) ? $this->removeDir($obj) : $this->remove($obj);
            }
        }

        if ($remove_this) {
            rmdir($dir);
        }
    }
}
