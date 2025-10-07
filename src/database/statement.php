<?php

namespace ngatngay\database;

use PDOStatement;
use PDO;

class statement extends PDOStatement
{
    protected function __construct()
    {
        $this->setFetchMode(PDO::FETCH_CLASS, row::class);
    }
}
