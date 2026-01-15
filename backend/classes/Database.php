<?php

class Database
{
    private static $instance = null;
    private $connection;

    private function __construct()
    {
        // Load .env file
        $envFile = __DIR__ . '/../.env';
        if (file_exists($envFile)) {
            $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                if (strpos($line, '=') !== false && $line[0] !== '#') {
                    list($key, $value) = explode('=', $line, 2);
                    putenv(trim($key) . '=' . trim($value));
                }
            }
        }

        $dbType = getenv('DB_TYPE') ?: 'sqlite';

        if ($dbType === 'sqlite') {
            $dbPath = getenv('SQLITE_PATH') ?: __DIR__ . '/../database/database.sqlite';
            // Resolve relative paths relative to backend directory
            if (!file_exists($dbPath) && !str_starts_with($dbPath, '/')) {
                $dbPath = __DIR__ . '/../' . $dbPath;
            }
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
