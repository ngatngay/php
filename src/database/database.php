<?php

namespace ngatngay\database;

use PDO;

class database
{
    private PDO $pdo;
    
    public function __construct($pdo = null)
    {
        $this->pdo = $pdo;
        $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_CLASS);
        $this->pdo->setAttribute(PDO::ATTR_STATEMENT_CLASS, [statement::class]);
    }

    public function query(string $sql, ?array $params = null)
    {
        $stmt = $this->pdo->prepare($sql);   
        $stmt->execute($params);

        return $stmt;
    }

    public function insert(string $table, array $params)
    {
        $sql = 'insert into "' . $table . '"'
            . ' (' . implode(',', $this->buildName(array_keys($params))) . ')'
            . ' values (' . implode(',', array_fill(0, count($params), '?')) . ')';
        
        $this->query($sql, array_values($params));

        return $this->pdo->lastInsertId();
    }

    public function update(string $sql, $params = null)
    {
        $stmt = $this->query($sql, $params);

        return $stmt->rowCount();
    }

    public function fetch(string $sql, $params = null)
    {
        return $this->query($sql, $params)
            ->fetch();
    }

    public function fetchAll(string $sql, $params = null)
    {
        return $this->query($sql, $params)
            ->fetchAll();
    }

    public function fetchColumn(string $sql, $params = null, $column = 0)
    {
        $stmt = $this->query($sql, $params);
        return $stmt->fetchColumn($column);
    }
    
    public function getOffset(int $page, int $perPage)
    {
        return $page * $perPage - $perPage;
    }

    public function quote(string $str)
    {
        return $this->pdo->quote($str);
    }

    public function buildName($arr)
    {
        return array_map(function ($item) {
            return '"' . $item . '"';
        }, $arr);
    }
}
