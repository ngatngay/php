<?php

namespace Nightmare\Database;

use PDO;
use PDOStatement;

class Database
{
    /**
     * @var PDO
     */
    private $driver;

    /**
     * @param PDO $pdo
     */
    public function __construct($pdo)
    {
        $this->driver = $pdo;
    }

    /**
     * @param string $dsn
     * @param string|null $username
     * @param string|null $password
     * @param array|null $options
     * @return self
     */
    public static function create(
        $dsn,
        $username = null,
        $password = null,
        $options = null
    ) {
        $pdo = new PDO($dsn, $username, $password, (array) $options);

        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

        $pdo->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, true);

        return new self($pdo);
    }

    public function driver()
    {
        return $this->driver;
    }

    /**
     * @param string $sql
     * @param array|null $params
     * @return PDOStatement|false
     */
    public function query($sql, $params = null)
    {
        $stmt = $this->driver->prepare($sql);
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
            . ' (' . implode(',', $this->build_name(array_keys($params))) . ')'
            . ' values (' . implode(',', array_fill(0, count($params), '?')) . ')';
        //dd($sql);
        $this->query($sql, array_values($params));

        return $this->driver->lastInsertId();
    }

    /**
     * @param string $table
     * @param array $con
     * @param array $arr
     * @return void
     */
    public function update_or_insert($table, $con, $arr)
    {
        $where_conditions = [];
        $where_params = [];

        foreach ($con as $column => $value) {
            $where_conditions[] = sprintf('"%s" = ?', $column);
            $where_params[] = $value;
        }

        $where_clause = implode(' AND ', $where_conditions);
        $check_sql = sprintf('SELECT COUNT(*) FROM "%s" WHERE %s', $table, $where_clause);
        $count = $this->fetch_column($check_sql, $where_params);

        if ($count > 0) {
            $update_parts = [];
            $update_params = [];

            foreach ($arr as $column => $value) {
                $update_parts[] = sprintf('"%s" = ?', $column);
                $update_params[] = $value;
            }

            $update_clause = implode(', ', $update_parts);
            $update_sql = sprintf('UPDATE "%s" SET %s WHERE %s', $table, $update_clause, $where_clause);

            $this->query($update_sql, array_merge($update_params, $where_params));
        } else {
            $this->insert($table, array_merge($con, $arr));
        }
    }

    /**
     * @param string $sql
     * @param array|null $params
     * @return mixed
     */
    public function fetch($sql, $params = null)
    {
        return $this->query($sql, $params)->fetch();
    }

    public function exec($sql)
    {
        return $this->driver->exec($sql);
    }

    /**
     * @param string $sql
     * @param array|null $params
     * @return array
     */
    public function fetch_all($sql, $params = null)
    {
        return $this->query($sql, $params)->fetchAll();
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
     * @param int $per_page
     * @return int
     */
    public function get_offset($page, $per_page)
    {
        return $page * $per_page - $per_page;
    }

    /**
     * @param string $str
     * @return string
     */
    public function quote($str)
    {
        return $this->driver->quote($str);
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
