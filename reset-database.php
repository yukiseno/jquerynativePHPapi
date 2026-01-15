<?php

/**
 * Reset Database Script
 * 
 * This script deletes the current database and recreates it with fresh test data.
 * Useful during development to start with a clean slate.
 * 
 * Usage: php reset-database.php
 */

// Determine database path
$projectRoot = dirname(__FILE__);
$dbPath = $projectRoot . '/backend/database/ecommerce.db';

// Delete existing database file
if (file_exists($dbPath)) {
    if (unlink($dbPath)) {
        echo "✓ Deleted existing database\n";
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

// Run setup script to create tables and seed data
require_once $projectRoot . '/backend/setup.php';

echo "\n✓ Database reset complete!\n";
echo "✓ Tables created with fresh test data\n";
echo "✓ Test user: user@test.com / password\n";
