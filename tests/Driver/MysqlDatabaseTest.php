<?php

use Database\DatabaseFactory;
use PHPUnit\Framework\TestCase;

class MysqlDatabaseTest extends TestCase
{
    public function testConnection()
    {
        $d = DatabaseFactory::create();
        $this->assertInstanceOf(\PDO::class, $d->connect());
    }
}