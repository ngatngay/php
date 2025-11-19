<?php

namespace nightmare\database;

use PDO;
use PDOStatement;

class database
{
    /**
     * @var PDO
     */
    private $pdo;

    public function __construct(
        $dsn,
        $username = null,
        $password = null,
        $options = null
    ) {
        if ($dsn instanceof PDO) {
            $this->pdo = $dsn;
        } else {
            $this->pdo = new PDO(
                $dsn,
                $username,
                $password,
                array_merge([
                    PDO::MYSQL_ATTR_INIT_COMMAND, 'SET sql_mode="ANSI,TRADITIONAL"'
                ], (array) $options)
            );
        }

        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_CLASS);
        $this->pdo->setAttribute(PDO::ATTR_STATEMENT_CLASS, [statement::class]);

        $this->pdo->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, true);
    }

    /**
     * @param string $sql
     * @param array|null $params
     * @return PDOStatement|false
     */
    public function query($sql, $params = null)
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt;
    }

    /**
     * @param string $table
     * @param array $params
     * @return string
     */
    public function insert($table, $params)
    {
        $sql = 'insert into "' . $table . '"'
            . ' (' . implode(',', $this->buildName(array_keys($params))) . ')'
            . ' values (' . implode(',', array_fill(0, count($params), '?')) . ')';

        $this->query($sql, array_values($params));

        return $this->pdo->lastInsertId();
    }


    /**
     * @param string $table
     * @param array $con
     * @param array $arr
     * @return void
     */
    public function update_or_insert($table, $con, $arr)
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

    /**
     * @param string $sql
     * @param array|null $params
     * @return int
     */
    public function update($sql, $params = null)
    {
        $stmt = $this->query($sql, $params);

        return $stmt->rowCount();
    }

    /**
     * @param string $sql
     * @param array|null $params
     * @return mixed
     */
    public function fetch($sql, $params = null)
    {
        return $this->query($sql, $params)
            ->fetch();
    }
    
    public function exec($sql)
    {
        return $this->pdo->exec($sql);
    }

    /**
     * @param string $sql
     * @param array|null $params
     * @return array
     */
    public function fetchAll($sql, $params = null)
    {
        return $this->query($sql, $params)
            ->fetchAll();
    }
    /**
     * @param string $sql
     * @param array|null $params
     * @return array
     */
    public function fetch_all($sql, $params = null)
    {
        return $this->query($sql, $params)
            ->fetchAll();
    }

    /**
     * @param string $sql
     * @param array|null $params
     * @param int $column
     * @return mixed
     */
    public function fetchColumn($sql, $params = null, $column = 0)
    {
        $stmt = $this->query($sql, $params);
        return $stmt->fetchColumn($column);
    }
    /**
     * @param string $sql
     * @param array|null $params
     * @param int $column
     * @return mixed
     */
    public function fetch_column($sql, $params = null, $column = 0)
    {
        $stmt = $this->query($sql, $params);
        return $stmt->fetchColumn($column);
    }

    /**
     * @param int $page
     * @param int $perPage
     * @return int
     */
    public function getOffset($page, $perPage)
    {
        return $page * $perPage - $perPage;
    }
    /**
     * @param int $page
     * @param int $perPage
     * @return int
     */
    public function get_offset($page, $perPage)
    {
        return $page * $perPage - $perPage;
    }

    /**
     * @param string $str
     * @return string
     */
    public function quote($str)
    {
        return $this->pdo->quote($str);
    }

    /**
     * @param array $arr
     * @return array
     */
    public function buildName($arr)
    {
        return array_map(function ($item) {
            return '"' . $item . '"';
        }, $arr);
    }
    /**
     * @param array $arr
     * @return array
     */
    public function build_name($arr)
    {
        return array_map(function ($item) {
            return '"' . $item . '"';
        }, $arr);
    }
}
