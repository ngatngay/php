<?php

namespace ngatngay\database;

class row extends \ArrayObject
{
    public function __get($name)
    {
        return $this[$name];
    }

    public function __set($name, $val)
    {
        $this[$name] = $val;
    }
}
