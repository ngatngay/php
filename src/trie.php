<?php

namespace nightmare;

class trie
{
    /* key => [
        child
        data
    ] */
    public $tree;

    public function __construct() {
        $this->tree = [
            'data' => '',
            'child' => []
        ];
    }

    public function add($str, $data = '') {
        $str = trim($str);

        if (empty($str)) {
            return false;
        }

        $length = mb_strlen($str);
        $tree = &$this->tree;

        for ($i = 0; $i < $length; $i++) {
            $char = $str[$i];
            $is_end = $i === ($length - 1);

            if (!isset($tree['child'][$char])) {
                $tree['child'][$char] = [
                    'data' => '',
                    'child' => []
                ];
            }

            if ($is_end) {
                $tree['child'][$char]['data'] = (string) $data;
            }

            $tree = &$tree['child'][$char];
        }
    }

    // array
    public function search($str) {
    }

    // false = no, string
    public function search_data($str) {
        $str = trim($str);

        if (empty($str)) {
            return false;
        }

        $length = mb_strlen($str);
        $tree = &$this->tree;

        for ($i = 0; $i < $length; $i++) {
            $char = $str[$i];
            $is_end = $i === ($length - 1);

            if (!isset($tree['child'][$char])) {
                return false;
            }

            if ($is_end) {
                return $tree['child'][$char]['data'] ?? '';
            }

            $tree = &$tree['child'][$char];
        }
    }
}
