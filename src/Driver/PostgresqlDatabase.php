<?php

namespace Database\Driver;

use Database\Database;
use Database\Contract\DatabaseInterface;

class PostgresqlDatabase extends Database implements DatabaseInterface
{
    public function connect()
    {
        try {
            $this->pdo = new \PDO("pgsql:host=$this->servername;port=9001;dbname=$this->dbname", 
                            $this->user, 
                            $this->password
                        );
            $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            return $this->pdo;
        } catch (\PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
            return null;
        }
    }
}
