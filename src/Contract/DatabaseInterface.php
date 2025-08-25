<?php

namespace Database\Contract;

interface DatabaseInterface
{
    public function connect();
    public function createDatabase(string $databaseName);
    public function dropDatabase(string $databaseName);
    public function createTable(string $tableName, array $columns);
    public function insertData(string $tableName, array $data);
    public function updateData(string $tableName, $id, array $data);
}