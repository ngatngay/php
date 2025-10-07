<?php

namespace ngatngay\database;

use ArrayObject;

class row extends ArrayObject
{
    public function __get(string $name): mixed
    {
        return $this[$name];
    }

    public function __set(string $name, mixed $val): void
    {
        $this[$name] = $val;
    }
}
