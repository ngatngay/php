<?php

namespace ngatngay;

use Exception;
use FilesystemIterator;
use RecursiveCallbackFilterIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;

// file system
class fs
{
    /*
     * file, file1, file2...
     */
    /**
     * @param string $file_name_body
     * @param string $file_ext
     * @return string
     */
    public function name_increment($file_name_body, $file_ext)
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

    /**
     * @param string $name
     * @return string
     */
    public static function get_extension($name)
    {
        return (new SplFileInfo($name))->getExtension();
    }

    /**
     * @param string $path
     * @return int
     */
    public static function size($path)
    {
        if (!is_dir($path)) {
            return filesize($path);
        }

        $size = 0;

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($path, FilesystemIterator::SKIP_DOTS | FilesystemIterator::FOLLOW_SYMLINKS),
            RecursiveIteratorIterator::LEAVES_ONLY
        );

        foreach ($iterator as $file) {
            if ($file->isFile()) {
                $size += $file->getSize();
            }
        }

        return $size;
    }

    /**
     * @param int $fileSize
     * @return string
     */
    public static function readable_size($fileSize)
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

    /**
     * @param string $path
     * @return bool
     */
    public static function remove($path)
    {
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

        if (!file_exists($path)) {
            return true;
        }

        throw new Exception('remove error, not match file type');
    }

    /**
     * @param string $path
     * @param array $excludes
     * @return RecursiveIteratorIterator
     */
    public static function read_full_dir($path, $excludes = [])
    {
        $directory = new RecursiveDirectoryIterator(
            $path,
            FilesystemIterator::UNIX_PATHS
            | FilesystemIterator::SKIP_DOTS
        );

        $filter = new RecursiveCallbackFilterIterator($directory, function ($current, $key, $iterator) use ($path, $excludes) {
            $relativePath = str::replace_first($path, '', $current->getPathname());

            foreach ($excludes as $exclude) {
                if (empty($exclude)) {
                    continue;
                }
                //var_dump($relativePath);
                //var_dump($exclude);

                $exclude = trim($exclude);
                $exclude = trim($exclude, '/');
                $relativePath = trim($relativePath, '/');

                if (str_ends_with($relativePath, $exclude)) {
                    return false;
                }
            }

            return true;
        });

        return new RecursiveIteratorIterator(
            $filter,
            RecursiveIteratorIterator::SELF_FIRST
        );
    }

    /**
     * @param string ...$paths
     * @return string
     */
    public static function join_path(...$paths)
    {
        return preg_replace('#/+#', '/', implode('/', $paths));
    }

    /**
         * Lấy tên chủ sở hữu file theo tên file
         * @param string $filename
         * @return string
         */
    public static function get_owner_name($filename)
    {
        $owner_id = @fileowner($filename);
        if ($owner_id === false) {
            return '';
        }
        return self::get_owner_name_by_id($owner_id);
    }

    /**
     * Lấy tên chủ sở hữu theo user ID
     * @param int $id
     * @return string
     */
    public static function get_owner_name_by_id($id)
    {
        $info = @posix_getpwuid($id);
        return $info['name'] ?? '';
    }
}
