<?php

namespace Database\Driver;

use Database\Database;
use Database\Contract\DatabaseInterface;

class MysqlDatabase extends Database implements DatabaseInterface
{
    public function connect($useDbName = false)
    {
        try {
            $dsn = $useDbName
                ? "mysql:host=$this->servername;dbname=$this->dbname"
                : "mysql:host=$this->servername";
            $this->pdo = new \PDO($dsn, $this->user, $this->password);
            $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            return $this->pdo;
        } catch (\PDOException $e) {
            throw new \Exception("Connection failed: " . $e->getMessage());
        }
    }

    public function ensureConnected()
    {
        if (! $this->pdo instanceof \PDO) {
            throw new \Exception("PDO connection not established. Call connect() first.");
        }
    }

    public function executeWithConnection(callable $callback)
    {
        $this->ensureConnected();
        return $callback();
    }

    public function createDatabase($databaseName)
    {
        return $this->executeWithConnection(function () use ($databaseName) {
            try {
                $sql = "CREATE DATABASE IF NOT EXISTS $databaseName";
                $this->pdo->exec($sql);

                // reconnect with dbname
                $this->dbname = $databaseName;
                $this->connect(true);

                return $this->pdo;
            } catch (\PDOException $e) {
                throw new \Exception("Create database $databaseName failed: " . $e->getMessage());
            }
        });
    }

    public function dropDatabase($databaseName)
    {
        return $this->executeWithConnection(function () use ($databaseName) {
            try {
                $sql = "DROP DATABASE IF EXISTS $databaseName";
                $this->pdo->exec($sql);
                return $this->pdo;
            } catch (\PDOException $e) {
                throw new \Exception("Drop database $databaseName failed: ". $e->getMessage());
            }
        });
    }

    /**
     * Create a table with name
     *
     * @param string $tableName
     * @param array $columns
     * @return mixed
     */
    public function createTable($tableName, array $columns)
    {
        return $this->executeWithConnection(function () use ($tableName, $columns) {
            $table = "`" . str_replace("`", "", $tableName) . "`";
            $cols = [];
    
            foreach ($columns as $column) {
                $colName = "`" . str_replace("`", "", $column["name"]) . "`";
                $type = $column["type"];
                $options = isset($column["options"]) ? $column["options"] : '';
                $cols[] = "$colName $type $options";
            }
    
            $columnSql = implode(", ", $cols);
            $sql = "CREATE TABLE IF NOT EXISTS $table ($columnSql)";
    
            try {
                $this->pdo->exec($sql);
                return $this->pdo;
            } catch (\PDOException $e) {
                throw new \Exception("Create table $tableName failed: " . $e->getMessage());
            }
        });
    }

    public function insertData($tableName, array $data)
    {
        // Process data
        $table = "`" . str_replace("`", "", $tableName) . "`";
        $columns = array_keys($data);
        $placeholders = array_map(function ($col) {
            return ":" . $col;
        }, $columns);
        $columnsSql = implode(", ", $columns);
        $placeholdersSql = implode(", ", $placeholders);

        // Prepare SQL
        $sql = "INSERT INTO $table ($columnsSql) VALUES ($placeholdersSql)";
        $stmt = $this->pdo->prepare($sql);

        // Bind value
        foreach ($data as $key => $value) {
            $stmt->bindValue(":" . $key, $value);
        }

        // Execute
        $stmt->execute();

        return $this->pdo;
    }

    public function updateData($tableName, $id,array $data)
    {
        // Process data
        $table = "`" . str_replace("`", "", $tableName) . "`";
        $columns = array_keys($data);
        $columnsAndPlaceholders = array_map(function ($col) {
            return "$col=:$col";
        }, $columns);
        $columnsAndPlaceholdersSql = implode(", ", $columnsAndPlaceholders);
        
        // Prepare SQL
        $sql = "UPDATE $table SET $columnsAndPlaceholdersSql WHERE id=:id";
        $stmt = $this->pdo->prepare($sql);

        // Bind value
        foreach ($data as $key => $value) {
            $stmt->bindValue(":" . $key, $value);
        }
        $stmt->bindValue(":id", $id);

        // Execute
        $stmt->execute();

        return $this->pdo;
    }
}