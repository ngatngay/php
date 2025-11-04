<?php

namespace nightmare;

class arr
{
    /**
     * @param int $page
     * @param int $per_page
     * @param array $data
     * @return array
     */
    public static function get_by_page($page, $per_page, $data)
    {
        $offset = ($page - 1) * $per_page;
        return array_slice($data, $offset, $per_page);
    }

    /**
     * @param string $filename
     * @param array $arr
     * @return int|false
     */
    public static function to_file($filename, $arr) {
        return file_put_contents(
            $filename,
            '<?php return ' . var_export($arr, true) . ';'
        );
    }
}
