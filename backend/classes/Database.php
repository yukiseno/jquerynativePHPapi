<?php

class Database
{
    private static $instance = null;
    private $connection;

    private function __construct()
    {
        $dbType = getenv('DB_TYPE') ?: 'sqlite';

        if ($dbType === 'sqlite') {
            $dbPath = getenv('SQLITE_PATH') ?: __DIR__ . '/../database/database.sqlite';
            $this->connection = new PDO('sqlite:' . $dbPath);
        } else {
            $host = getenv('DB_HOST') ?: 'localhost';
            $dbname = getenv('DB_NAME') ?: 'ecommerce';
            $user = getenv('DB_USER') ?: 'root';
            $pass = getenv('DB_PASS') ?: '';

            $this->connection = new PDO(
                "mysql:host=$host;dbname=$dbname;charset=utf8",
                $user,
                $pass
            );
        }

        $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance->connection;
    }
}
