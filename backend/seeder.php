<?php

require_once __DIR__ . '/classes/Database.php';
require_once __DIR__ . '/classes/DatabaseAdapter.php';
require_once __DIR__ . '/classes/MySQLDatabase.php';
require_once __DIR__ . '/classes/SQLiteDatabase.php';

$db = Database::getInstance();

try {
    echo "Seeding database...\n";

    // Seed colors
    echo "\n📍 Seeding colors...";
    $colors = ['Black', 'White', 'Red', 'Blue', 'Green', 'Gray', 'Navy'];
    foreach ($colors as $color) {
        $db->insertIgnore('colors', ['name' => $color], [$color]);
    }
    echo " ✓\n";

    // Seed sizes
    echo "📍 Seeding sizes...";
    $sizes = ['XS', 'S', 'M', 'L', 'XL', 'XXL'];
    foreach ($sizes as $size) {
        $db->insertIgnore('sizes', ['name' => $size], [$size]);
    }
    echo " ✓\n";

    // Seed coupons
    echo "📍 Seeding coupons...";
    $coupons = [
        ['name' => 'WELCOME10', 'discount' => 10, 'valid_until' => date('Y-m-d', strtotime('+1 month'))],
        ['name' => 'SUMMER20', 'discount' => 20, 'valid_until' => date('Y-m-d', strtotime('+2 months'))],
    ];
    foreach ($coupons as $coupon) {
        $db->insertIgnore('coupons', ['name' => null, 'discount' => null, 'valid_until' => null], [$coupon['name'], $coupon['discount'], $coupon['valid_until']]);
    }
    echo " ✓\n";

    // Seed products
    echo "📍 Seeding products...";
    $products = [
        [
            'name' => 'Classic Red T-Shirt',
            'price' => 2500,
            'description' => 'Classic red cotton T-shirt. Soft, breathable, everyday wear.',
            'slug' => 'classic-red-t-shirt',
            'thumbnail' => 'images/products/tshirt-red.png',
            'colors' => ['Red'],
            'sizes' => ['S', 'M', 'L', 'XL'],
        ],
        [
            'name' => 'Classic Green T-Shirt',
            'price' => 2500,
            'description' => 'Classic green cotton T-shirt. Comfortable and durable.',
            'slug' => 'classic-green-t-shirt',
            'thumbnail' => 'images/products/tshirt-green.png',
            'colors' => ['Green'],
            'sizes' => ['M', 'L'],
        ],
        [
            'name' => 'Classic Blue T-Shirt',
            'price' => 2500,
            'description' => 'Classic blue cotton T-shirt. Minimal and clean style.',
            'slug' => 'classic-blue-t-shirt',
            'thumbnail' => 'images/products/tshirt-blue.png',
            'colors' => ['Blue'],
            'sizes' => ['M', 'L'],
        ],
        [
            'name' => 'T-Shirt',
            'price' => 3000,
            'description' => 'White graphic T-shirt with modern design.',
            'slug' => 't-shirt',
            'thumbnail' => 'images/products/t-shirt.png',
            'colors' => ['Blue', 'Red', 'White'],
            'sizes' => ['M', 'L', 'XL'],
        ],
    ];

    foreach ($products as $productData) {
        // Insert product
        $db->insertIgnore('products', ['name' => null, 'description' => null, 'slug' => null, 'thumbnail' => null, 'price' => null], [
            $productData['name'],
            $productData['description'],
            $productData['slug'],
            $productData['thumbnail'],
            $productData['price'],
        ]);

        // Get product ID
        $productStmt = $db->prepare("SELECT id FROM products WHERE slug = ?");
        $productStmt->execute([$productData['slug']]);
        $product = $productStmt->fetch(PDO::FETCH_ASSOC);
        $productId = $product['id'];

        // Attach colors
        foreach ($productData['colors'] as $colorName) {
            $colorStmt = $db->prepare("SELECT id FROM colors WHERE name = ?");
            $colorStmt->execute([$colorName]);
            $color = $colorStmt->fetch(PDO::FETCH_ASSOC);
            if ($color) {
                $db->insertIgnore('color_product', ['product_id' => null, 'color_id' => null], [$productId, $color['id']]);
            }
        }

        // Attach sizes
        foreach ($productData['sizes'] as $sizeName) {
            $sizeStmt = $db->prepare("SELECT id FROM sizes WHERE name = ?");
            $sizeStmt->execute([$sizeName]);
            $size = $sizeStmt->fetch(PDO::FETCH_ASSOC);
            if ($size) {
                $db->insertIgnore('product_size', ['product_id' => null, 'size_id' => null], [$productId, $size['id']]);
            }
        }
    }
    echo " ✓\n";

    // Seed users
    echo "📍 Seeding users...";
    $users = [
        [
            'name' => 'Test User',
            'email' => 'user@test.com',
            'password' => 'password1234',
        ],
    ];

    foreach ($users as $user) {
        $hashedPassword = password_hash($user['password'], PASSWORD_BCRYPT);
        $db->insertIgnore('users', ['name' => null, 'email' => null, 'password' => null], [$user['name'], $user['email'], $hashedPassword]);
    }
    echo " ✓\n";

    echo "\n✅ Database seeded successfully!\n\n";
    echo "📊 Seeded Data:\n";
    echo "   • Colors: " . count($colors) . "\n";
    echo "   • Sizes: " . count($sizes) . "\n";
    echo "   • Coupons: " . count($coupons) . "\n";
    echo "   • Products: " . count($products) . "\n";
    echo "   • Users: " . count($users) . "\n";
    echo "\n👤 Test Account:\n";
    echo "   • Email: user@test.com | Password: password1234\n";
} catch (Exception $e) {
    echo "\n❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}
