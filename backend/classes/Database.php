<?php

require_once __DIR__ . '/DatabaseAdapter.php';
require_once __DIR__ . '/MySQLDatabase.php';
require_once __DIR__ . '/SQLiteDatabase.php';

class Database
{
    private static $instance = null;
    private $adapter;

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
            $dbPath = getenv('SQLITE_PATH') ?: 'database/database.sqlite';

            // Resolve relative paths relative to backend directory
            // Check if path is absolute (works on both Windows and Unix)
            $isAbsolute = (strpos($dbPath, '/') === 0) || (strlen($dbPath) > 1 && $dbPath[1] === ':');
            if (!$isAbsolute) {
                $dbPath = __DIR__ . '/../' . ltrim($dbPath, '/\\.');
            }

            // Ensure the database directory exists (critical for Windows)
            $dbDir = dirname($dbPath);
            if (!is_dir($dbDir)) {
                mkdir($dbDir, 0755, true);
            }
            $connection = new PDO('sqlite:' . $dbPath);
            $this->adapter = new SQLiteDatabase($connection);
        } else {
            $host = getenv('DB_HOST') ?: 'localhost';
            $dbname = getenv('DB_NAME') ?: 'ecommerce';
            $user = getenv('DB_USER') ?: 'root';
            $pass = getenv('DB_PASS') ?: '';

            $connection = new PDO(
                "mysql:host=$host;dbname=$dbname;charset=utf8",
                $user,
                $pass
            );
            $this->adapter = new MySQLDatabase($connection);
        }
    }

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance->adapter;
    }
}
