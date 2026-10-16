<?php

namespace Nightmare;

class Trie
{
    /** @var array */
    public $tree;

    public function __construct() {
        $this->tree = [
            'value' => '',
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
            $char = mb_ord($char);

            if (!isset($tree['child'][$char])) {
                $tree['child'][$char] = [
                    'value' => '',
                    'child' => []
                ];
            }

            if ($is_end) {
                $tree['child'][$char]['value'] = (string) $data;
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
            $char = mb_ord($char);

            if (!isset($tree['child'][$char])) {
                return false;
            }

            if ($is_end) {
                return [
                    'value' => $tree['child'][$char]['value'] ?? '',
                    'is_end' => count($tree['child'][$char]['child']) === 0
                ];
            }

            $tree = &$tree['child'][$char];
        }
    }
}
