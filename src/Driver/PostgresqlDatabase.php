<?php

namespace Database\Driver;

use Database\Database;
use Database\Contract\DatabaseInterface;

class PostgresqlDatabase extends Database implements DatabaseInterface
{
    public $stmt;

    public function connect()
    {
        $dsn = "pgsql:host=$this->servername;port=$this->port;dbname=$this->dbname";

        try {
            $this->pdo = new \PDO($dsn, $this->user, $this->password);
            $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            return $this->pdo;
        } catch (\PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
            return null;
        }
    }

    public function ensureConnected()
    {
        if (! $this->pdo instanceof \PDO) {
            throw new \Exception("PDO connection not established. Call connect() first.");
        }
    }

    public function executeWithConnection($callback)
    {
        $this->ensureConnected();
        return $callback();
    }

    public function query($sql, $params = [])
    {
        return $this->executeWithConnection(function () use ($sql, $params) {
            $this->stmt = $this->pdo->prepare($sql);
            $this->stmt->execute($params);

            return $this;
        });
    }

    public function first()
    {
        return $this->executeWithConnection(fn () => $this->stmt->fetch());
    }

    public function all()
    {
        return $this->executeWithConnection(fn () => $this->stmt->fetchAll());
    }
}
