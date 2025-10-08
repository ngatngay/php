<?php

namespace ngatngay\database;

use ArrayObject;

class row extends ArrayObject
{
    public function __get(string $name)
    {
        return $this[$name];
    }

    public function __set(string $name, $val): void
    {
        $this[$name] = $val;
    }
}
