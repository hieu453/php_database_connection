<?php

namespace Database\Driver;

use Database\Database;
use Database\Contract\DatabaseInterface;

class MysqlDatabase extends Database implements DatabaseInterface
{
    public $statement;

    public function connect()
    {
        try {
            $dsn = "mysql:host=$this->servername:$this->port;dbname=$this->dbname";
            $this->pdo = new \PDO($dsn, $this->user, $this->password, [
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
            ]);
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

    public function query($sql, $params = [])
    {
        return $this->executeWithConnection(function () use ($sql, $params) {
            $this->statement = $this->pdo->prepare($sql);
            $this->statement->execute($params);

            return $this;
        });
    }

    public function first()
    {
        return $this->executeWithConnection(fn () => $this->statement->fetch());
    }

    public function all()
    {
        return $this->executeWithConnection(fn () => $this->statement->fetchAll());
    }
}