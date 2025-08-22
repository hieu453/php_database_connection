<?php

namespace Database;

class Database
{
    protected $pdo;

    protected $servername;
    protected $user;
    protected $password;
    protected $dbname;

    public function __construct()
    {
        $this->servername = $_ENV["DB_SERVERNAME"];
        $this->user = $_ENV["DB_USER"];
        $this->password = $_ENV["DB_PASSWORD"];
        $this->dbname = $_ENV["DB_DBNAME"];
    }
}
