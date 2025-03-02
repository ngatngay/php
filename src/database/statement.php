<?php

namespace ngatngay\database;

class statement extends \PDOStatement
{
    protected function __construct()
    {
        $this->setFetchMode(\PDO::FETCH_CLASS, row::class);
    }
}
