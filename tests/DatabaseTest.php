<?php

use PHPUnit\Framework\TestCase;
use Src\Database;

class DatabaseTest extends TestCase
{
    public function testDatabaseConnection()
    {
        $db = new Database();
        $this->assertInstanceOf(PDO::class, $db->getPDO());
    }
}
