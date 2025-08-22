<?php

namespace Database;

use Database\Driver\MysqlDatabase;
use Database\Driver\PostgresqlDatabase;

class DatabaseFactory
{
    public static function create()
    {
        $driver = $_ENV["DRIVER"];

        switch ($driver) {
            case "mysql":
                return new MysqlDatabase;
            case "pgsql":
                return new PostgresqlDatabase;
            default:
                throw new \Exception("Cannot find any driver!");
        }
    }
}