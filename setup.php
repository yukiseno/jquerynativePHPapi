<?php

// Database setup script for jQuery Native PHP API
require_once __DIR__ . '/backend/classes/Database.php';

// Load .env file to check DB_TYPE
$envFile = __DIR__ . '/backend/.env';
$dbType = 'sqlite';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos($line, 'DB_TYPE=') === 0) {
            $dbType = trim(explode('=', $line)[1]);
            break;
        }
    }
}

// For MySQL, create database first if it doesn't exist
if ($dbType === 'mysql') {
    try {
        $host = getenv('DB_HOST') ?: '127.0.0.1';
        $user = getenv('DB_USER') ?: 'root';
        $pass = getenv('DB_PASS') ?: '';
        $dbname = getenv('DB_NAME') ?: 'ecommerce';

        $pdo = new PDO("mysql:host=$host", $user, $pass);
        $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbname`");
        echo "✓ Database '$dbname' created or already exists\n";
    } catch (Exception $e) {
        echo "Error creating database: " . $e->getMessage() . "\n";
        exit(1);
    }
}

$db = Database::getInstance();

try {
    // Check if using MySQL or SQLite and create tables accordingly
    if ($dbType === 'mysql') {
        // MySQL compatible syntax
        $db->exec('
            CREATE TABLE IF NOT EXISTS users (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(255) NOT NULL,
                email VARCHAR(255) NOT NULL UNIQUE,
                password VARCHAR(255) NOT NULL,
                address VARCHAR(255),
                city VARCHAR(255),
                country VARCHAR(255),
                zip_code VARCHAR(20),
                phone_number VARCHAR(20),
                profile_image VARCHAR(255),
                profile_completed INT DEFAULT 0,
                two_factor_enabled INT DEFAULT 0,
                two_factor_secret VARCHAR(255),
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            )
        ');

        $db->exec('
            CREATE TABLE IF NOT EXISTS personal_access_tokens (
                id INT AUTO_INCREMENT PRIMARY KEY,
                tokenable_id INT NOT NULL,
                tokenable_type VARCHAR(255) NOT NULL,
                name VARCHAR(255) NOT NULL,
                token VARCHAR(255) NOT NULL UNIQUE,
                abilities TEXT,
                last_used_at DATETIME,
                expires_at DATETIME,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                FOREIGN KEY (tokenable_id) REFERENCES users(id) ON DELETE CASCADE
            )
        ');

        $db->exec('
            CREATE TABLE IF NOT EXISTS products (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(255) NOT NULL,
                description TEXT,
                slug VARCHAR(255) NOT NULL UNIQUE,
                thumbnail VARCHAR(255),
                price INT NOT NULL,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            )
        ');

        $db->exec('
            CREATE TABLE IF NOT EXISTS colors (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(255) NOT NULL UNIQUE,
                hex_code VARCHAR(7),
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            )
        ');

        $db->exec('
            CREATE TABLE IF NOT EXISTS sizes (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(255) NOT NULL UNIQUE,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            )
        ');

        $db->exec('
            CREATE TABLE IF NOT EXISTS color_product (
                id INT AUTO_INCREMENT PRIMARY KEY,
                product_id INT NOT NULL,
                color_id INT NOT NULL,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
                FOREIGN KEY (color_id) REFERENCES colors(id) ON DELETE CASCADE
            )
        ');

        $db->exec('
            CREATE TABLE IF NOT EXISTS product_size (
                id INT AUTO_INCREMENT PRIMARY KEY,
                product_id INT NOT NULL,
                size_id INT NOT NULL,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
                FOREIGN KEY (size_id) REFERENCES sizes(id) ON DELETE CASCADE
            )
        ');

        $db->exec('
            CREATE TABLE IF NOT EXISTS coupons (
                id INT AUTO_INCREMENT PRIMARY KEY,
                code VARCHAR(255) NOT NULL UNIQUE,
                discount INT NOT NULL,
                usage_limit INT DEFAULT 999999,
                times_used INT DEFAULT 0,
                expires_at DATETIME,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            )
        ');

        $db->exec('
            CREATE TABLE IF NOT EXISTS orders (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                status VARCHAR(255) DEFAULT "pending",
                total INT NOT NULL,
                coupon_id INT,
                discount INT DEFAULT 0,
                notes TEXT,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
                FOREIGN KEY (coupon_id) REFERENCES coupons(id) ON DELETE SET NULL
            )
        ');

        $db->exec('
            CREATE TABLE IF NOT EXISTS order_items (
                id INT AUTO_INCREMENT PRIMARY KEY,
                order_id INT NOT NULL,
                product_id INT NOT NULL,
                quantity INT NOT NULL,
                price INT NOT NULL,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
                FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
            )
        ');
    } else {
        // SQLite compatible syntax
        $db->exec('
            CREATE TABLE IF NOT EXISTS users (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name TEXT NOT NULL,
                email TEXT NOT NULL UNIQUE,
                password TEXT NOT NULL,
                address TEXT,
                city TEXT,
                country TEXT,
                zip_code TEXT,
                phone_number TEXT,
                profile_image TEXT,
                profile_completed INTEGER DEFAULT 0,
                two_factor_enabled INTEGER DEFAULT 0,
                two_factor_secret TEXT,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
            )
        ');

        $db->exec('
            CREATE TABLE IF NOT EXISTS personal_access_tokens (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                tokenable_id INTEGER NOT NULL,
                tokenable_type TEXT NOT NULL,
                name TEXT NOT NULL,
                token TEXT NOT NULL UNIQUE,
                abilities TEXT,
                last_used_at DATETIME,
                expires_at DATETIME,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (tokenable_id) REFERENCES users(id) ON DELETE CASCADE
            )
        ');

        $db->exec('
            CREATE TABLE IF NOT EXISTS products (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name TEXT NOT NULL,
                description TEXT,
                slug TEXT NOT NULL UNIQUE,
                thumbnail TEXT,
                price INTEGER NOT NULL,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
            )
        ');

        $db->exec('
            CREATE TABLE IF NOT EXISTS colors (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name TEXT NOT NULL UNIQUE,
                hex_code TEXT,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
            )
        ');

        $db->exec('
            CREATE TABLE IF NOT EXISTS sizes (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name TEXT NOT NULL UNIQUE,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
            )
        ');

        $db->exec('
            CREATE TABLE IF NOT EXISTS color_product (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                product_id INTEGER NOT NULL,
                color_id INTEGER NOT NULL,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
                FOREIGN KEY (color_id) REFERENCES colors(id) ON DELETE CASCADE
            )
        ');

        $db->exec('
            CREATE TABLE IF NOT EXISTS product_size (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                product_id INTEGER NOT NULL,
                size_id INTEGER NOT NULL,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
                FOREIGN KEY (size_id) REFERENCES sizes(id) ON DELETE CASCADE
            )
        ');

        $db->exec('
            CREATE TABLE IF NOT EXISTS coupons (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                code TEXT NOT NULL UNIQUE,
                discount INTEGER NOT NULL,
                usage_limit INTEGER DEFAULT 999999,
                times_used INTEGER DEFAULT 0,
                expires_at DATETIME,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
            )
        ');

        $db->exec('
            CREATE TABLE IF NOT EXISTS orders (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                user_id INTEGER NOT NULL,
                status TEXT DEFAULT "pending",
                total INTEGER NOT NULL,
                coupon_id INTEGER,
                discount INTEGER DEFAULT 0,
                notes TEXT,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
                FOREIGN KEY (coupon_id) REFERENCES coupons(id) ON DELETE SET NULL
            )
        ');

        $db->exec('
            CREATE TABLE IF NOT EXISTS order_items (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                order_id INTEGER NOT NULL,
                product_id INTEGER NOT NULL,
                quantity INTEGER NOT NULL,
                price INTEGER NOT NULL,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
                FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
            )
        ');
    }

    echo "✓ Tables created successfully\n";
    require_once __DIR__ . '/backend/seeder.php';
} catch (Exception $e) {
    echo "Error creating tables: " . $e->getMessage() . "\n";
    exit(1);
}
