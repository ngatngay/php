<?php

namespace ngatngay;

class arr
{
    public static function get_by_page(int $page, int $per_page, array $data): array
    {
        $offset = ($page - 1) * $per_page;
        return array_slice($data, $offset, $per_page);
    }

    public static function to_file(string $filename, array $arr): int|false {
        return file_put_contents(
            $filename,
            '<?php return ' . var_export($arr, true) . ';'
        );
    }
}
