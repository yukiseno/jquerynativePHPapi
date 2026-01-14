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

    // Create products table if it doesn't exist
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

    // Create colors table if it doesn't exist
    $db->exec('
        CREATE TABLE IF NOT EXISTS colors (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL UNIQUE,
            hex_code TEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )
    ');

    // Create sizes table if it doesn't exist
    $db->exec('
        CREATE TABLE IF NOT EXISTS sizes (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL UNIQUE,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )
    ');

    // Create color_product pivot table if it doesn't exist
    $db->exec('
        CREATE TABLE IF NOT EXISTS color_product (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            color_id INTEGER NOT NULL,
            product_id INTEGER NOT NULL,
            FOREIGN KEY (color_id) REFERENCES colors(id) ON DELETE CASCADE,
            FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
        )
    ');

    // Create product_size pivot table if it doesn't exist
    $db->exec('
        CREATE TABLE IF NOT EXISTS product_size (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            product_id INTEGER NOT NULL,
            size_id INTEGER NOT NULL,
            FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
            FOREIGN KEY (size_id) REFERENCES sizes(id) ON DELETE CASCADE
        )
    ');

    // Create coupons table if it doesn't exist
    $db->exec('
        CREATE TABLE IF NOT EXISTS coupons (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL UNIQUE,
            discount INTEGER NOT NULL,
            valid_until TEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )
    ');

    // Create orders table if it doesn't exist
    $db->exec('
        CREATE TABLE IF NOT EXISTS orders (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            user_id INTEGER NOT NULL,
            coupon_id INTEGER,
            subtotal INTEGER NOT NULL,
            discount_total INTEGER DEFAULT 0,
            total INTEGER NOT NULL,
            status TEXT DEFAULT "pending",
            payment_intent_id TEXT UNIQUE,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY (coupon_id) REFERENCES coupons(id) ON DELETE SET NULL
        )
    ');

    // Create order_items table if it doesn't exist
    $db->exec('
        CREATE TABLE IF NOT EXISTS order_items (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            order_id INTEGER NOT NULL,
            product_id INTEGER,
            product_name TEXT,
            color_id INTEGER,
            color_name TEXT,
            size_id INTEGER,
            size_name TEXT,
            qty INTEGER,
            price INTEGER,
            subtotal INTEGER,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
        )
    ');

    // Create reviews table if it doesn't exist
    $db->exec('
        CREATE TABLE IF NOT EXISTS reviews (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            product_id INTEGER NOT NULL,
            user_id INTEGER NOT NULL,
            rating INTEGER,
            comment TEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
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
