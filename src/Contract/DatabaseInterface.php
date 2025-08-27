<?php

namespace Database\Contract;

interface DatabaseInterface
{
    public function connect();
    public function query($sql, $params = []);
    public function first();
    public function all();
}