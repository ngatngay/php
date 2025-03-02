<?php

namespace ngatngay;

// file system
class fs
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
        return (new \SplFileInfo($name))->getExtension();
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

    public static function remove(string $path): bool
    {        
        if (!file_exists($path)) {
            return true;
        }

        if (is_link($path)) {
            return unlink($path);
        }

        if (is_file($path)) {
            return unlink($path);
        }

        if (is_dir($path)) {
            $files = array_diff(scandir($path), ['.', '..']);
            foreach ($files as $file) {
                $filePath = $path . DIRECTORY_SEPARATOR . $file;
                if (!self::remove($filePath)) {
                    return false;
                }
            }
            return rmdir($path);
        }

        throw new \Exception('remove error, not match file type');
    }
}
