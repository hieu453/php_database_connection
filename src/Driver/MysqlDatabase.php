<?php

namespace Database\Driver;

use Database\Database;
use Database\Contract\DatabaseInterface;

class MysqlDatabase extends Database implements DatabaseInterface
{
    public function connect()
    {
        try {
            $this->pdo = new \PDO("mysql:host=$this->servername;dbname=$this->dbname", $this->user, $this->password);
            $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            return $this->pdo;
        } catch (\PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
            return null;
        }
    }
}