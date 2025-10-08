<?php

namespace ngatngay\database;

use PDO;

class database
{
    private PDO $pdo;

    public function __construct(?PDO $pdo = null)
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


    public function update_or_insert(string $table, array $con, array $arr): void
    {
        $whereConditions = [];
        $whereParams = [];

        foreach ($con as $column => $value) {
            $whereConditions[] = sprintf('"%s" = ?', $column);
            $whereParams[] = $value;
        }

        $whereClause = implode(' AND ', $whereConditions);
        $checkSql = sprintf('SELECT COUNT(*) FROM "%s" WHERE %s', $table, $whereClause);
        $count = $this->fetch_column($checkSql, $whereParams);

        if ($count > 0) {
            $updateParts = [];
            $updateParams = [];

            foreach ($arr as $column => $value) {
                $updateParts[] = sprintf('"%s" = ?', $column);
                $updateParams[] = $value;
            }

            $updateClause = implode(', ', $updateParts);
            $updateSql = sprintf('UPDATE "%s" SET %s WHERE %s', $table, $updateClause, $whereClause);

            $this->query($updateSql, array_merge($updateParams, $whereParams));
        } else {
            $this->insert($table, array_merge($con, $arr));
        }
    }

    public function update(string $sql, ?array $params = null)
    {
        $stmt = $this->query($sql, $params);

        return $stmt->rowCount();
    }

    public function fetch(string $sql, ?array $params = null)
    {
        return $this->query($sql, $params)
            ->fetch();
    }

    public function fetchAll(string $sql, ?array $params = null)
    {
        return $this->query($sql, $params)
            ->fetchAll();
    }
    public function fetch_all(string $sql, ?array $params = null)
    {
        return $this->query($sql, $params)
            ->fetchAll();
    }

    public function fetchColumn(string $sql, ?array $params = null, int $column = 0)
    {
        $stmt = $this->query($sql, $params);
        return $stmt->fetchColumn($column);
    }
    public function fetch_column(string $sql, ?array $params = null, int $column = 0)
    {
        $stmt = $this->query($sql, $params);
        return $stmt->fetchColumn($column);
    }

    public function getOffset(int $page, int $perPage): int
    {
        return $page * $perPage - $perPage;
    }
    public function get_offset(int $page, int $perPage): int
    {
        return $page * $perPage - $perPage;
    }

    public function quote(string $str): string
    {
        return $this->pdo->quote($str);
    }

    public function buildName(array $arr): array
    {
        return array_map(function ($item) {
            return '"' . $item . '"';
        }, $arr);
    }
    public function build_name(array $arr): array
    {
        return array_map(function ($item) {
            return '"' . $item . '"';
        }, $arr);
    }
}
