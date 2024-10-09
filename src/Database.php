<?php
namespace Src;

use PDO;
use PDOException;

class Database
{
    private PDO $pdo;

    public function __construct($testMode = false)
    {
        $dbName = $testMode ? $_ENV['MYSQL_TEST_DATABASE'] : $_ENV['MYSQL_DATABASE'];
        $username = $testMode ? $_ENV['MYSQL_TEST_USER'] : $_ENV['MYSQL_USER'];
        $password = $testMode ? $_ENV['MYSQL_TEST_PASSWORD'] : $_ENV['MYSQL_PASSWORD'];

        $dsn = 'mysql:host=db;dbname=' . $dbName;

        try {
            $this->pdo = new PDO($dsn, $username, $password);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die('Database connection failed: '   . $e->getMessage());
        }
    }

    public function getPDO(): PDO
    {
        return $this->pdo;
    }
}
