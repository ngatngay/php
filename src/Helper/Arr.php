<?php

namespace NgatNgay\Helper;

// array helper pro max

class Arr
{
    public static function multipleUploadToSimple(array $fileUpload): array
    {
        $files = [];

        foreach ($fileUpload as $k => $l) {
            foreach ($l as $i => $v) {
                if (!array_key_exists($i, $files)) {
                    $files[$i] = [];
                }

                $files[$i][$k] = $v;
            }
        }

        return $files;
    }

    public static function getFromPage(array $data, int $page, int $perPage = 10): array
    {
        $result = [];
        $total = count($data);
        $start = ($page - 1) * $perPage;
        $end = $start + $perPage;

        if ($start < 0) {
            $start = 0;
        }

        if ($end > $total) {
            $end = $total;
        }

        for ($start; $start < $end; $start++) {
            $result[] = $data[$start];
        }

        return $result;
    }

    public static function toFile(string $filename, array $arr) {
        return file_put_contents(
            $filename,
            '<?php return ' . var_export($arr, true) . ';'
        );
    }
}
