<?php

namespace NgatNgay\Database;

class Statement extends \PDOStatement
{
    protected function __construct()
    {
        $this->setFetchMode(\PDO::FETCH_CLASS, Row::class);
    }
}
