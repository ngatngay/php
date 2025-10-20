<?php

namespace ngatngay\database;

use ArrayObject;

class row extends ArrayObject
{
    /**
     * @param string $name
     * @return mixed
     */
    public function __get($name)
    {
        return $this[$name];
    }

    /**
     * @param string $name
     * @param mixed $val
     * @return void
     */
    public function __set($name, $val)
    {
        $this[$name] = $val;
    }
}
