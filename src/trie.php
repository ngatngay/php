<?php

namespace nightmare;

class trie
{
    /** @var array */
    public $tree;

    public function __construct() {
        $this->tree = [
            'data' => '',
            'child' => []
        ];
    }

    public function add($str, $data = '') {
        $length = mb_strlen($str);

        if (!$length) {
            return false;
        }

        $tree = &$this->tree;
        $chars = mb_str_split($str);
        $i = 0;

        foreach ($chars as $char) {
            $i++;
            $is_end = $i === $length;

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

    // false, array
    public function search($str) {
        $length = mb_strlen($str);

        if (!$length) {
            return false;
        }
   
        $tree = &$this->tree;
        $chars = mb_str_split($str);
        $i = 0;

        foreach ($chars as $char) {
            $i++;
            $is_end = $i === $length;

            if (!isset($tree['child'][$char])) {
                return false;
            }

            if ($is_end) {
                return [
                    'data' => $tree['child'][$char]['data'] ?? '',
                    'is_end' => count($tree['child'][$char]['child']) === 0
                ];
            }

            $tree = &$tree['child'][$char];
        }
    }
}
