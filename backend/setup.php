<?php

// Database setup script for jQuery Native PHP API
require_once __DIR__ . '/classes/Database.php';

$db = Database::getInstance();

try {
    // Create users table if it doesn't exist
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
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )
    ');

    // Create personal_access_tokens table if it doesn't exist
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

    echo json_encode([
        'success' => true,
        'message' => 'Database tables created successfully'
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
