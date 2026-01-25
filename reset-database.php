<?php

/**
 * Reset Database Script
 * 
 * This script deletes the current database and recreates it with fresh test data.
 * Useful during development to start with a clean slate.
 * 
 * Usage: php reset-database.php
 */

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

// Reset based on database type
if ($dbType === 'mysql') {
    // For MySQL, create database if it doesn't exist, then drop all tables
    echo "🔄 Resetting MySQL database...\n";

    try {
        // First, create database if it doesn't exist (connect without db name)
        $host = getenv('DB_HOST') ?: '127.0.0.1';
        $user = getenv('DB_USER') ?: 'root';
        $pass = getenv('DB_PASS') ?: '';
        $dbname = getenv('DB_NAME') ?: 'ecommerce';

        $pdo = new PDO("mysql:host=$host", $user, $pass);
        $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbname`");
        echo "✓ Database '$dbname' created or already exists\n";

        // Now connect to the database and drop tables
        $db = Database::getInstance();

        // Drop tables in correct order (due to foreign keys)
        $tables = ['order_items', 'orders', 'personal_access_tokens', 'product_size', 'color_product', 'coupons', 'sizes', 'colors', 'products', 'users'];

        foreach ($tables as $table) {
            $db->exec("DROP TABLE IF EXISTS `$table`");
            echo "✓ Dropped table: $table\n";
        }

        echo "\n✓ All tables dropped\n";
    } catch (Exception $e) {
        echo "✗ Failed to reset database: " . $e->getMessage() . "\n";
        exit(1);
    }
} else {
    // For SQLite, delete the database file
    $projectRoot = dirname(__FILE__);
    $dbPath = $projectRoot . '/backend/database/database.sqlite';

    // Delete existing database file
    if (file_exists($dbPath)) {
        if (unlink($dbPath)) {
            echo "✓ Deleted existing SQLite database\n";
        } else {
            echo "✗ Failed to delete existing database\n";
            exit(1);
        }
    } else {
        echo "ℹ No existing database found\n";
    }

    // Ensure database directory exists
    $dbDir = dirname($dbPath);
    if (!is_dir($dbDir)) {
        mkdir($dbDir, 0755, true);
        echo "✓ Created database directory\n";
    }
}

// Run setup script to create tables and seed data
require_once __DIR__ . '/setup.php';

echo "\n✓ Database reset complete!\n";
echo "✓ Tables created with fresh test data\n";
echo "✓ Test user: user@test.com / password1234\n";
