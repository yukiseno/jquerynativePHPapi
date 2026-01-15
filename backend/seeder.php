<?php

require_once __DIR__ . '/classes/Database.php';

$db = Database::getInstance();

try {
    echo "Seeding database...\n";

    // Seed colors
    echo "\n📍 Seeding colors...";
    $colors = ['Black', 'White', 'Red', 'Blue', 'Green', 'Gray', 'Navy'];
    foreach ($colors as $color) {
        $db->prepare("INSERT OR IGNORE INTO colors (name) VALUES (?)")->execute([$color]);
    }
    echo " ✓\n";

    // Seed sizes
    echo "📍 Seeding sizes...";
    $sizes = ['XS', 'S', 'M', 'L', 'XL', 'XXL'];
    foreach ($sizes as $size) {
        $db->prepare("INSERT OR IGNORE INTO sizes (name) VALUES (?)")->execute([$size]);
    }
    echo " ✓\n";

    // Seed coupons
    echo "📍 Seeding coupons...";
    $coupons = [
        ['name' => 'WELCOME10', 'discount' => 10, 'valid_until' => date('Y-m-d', strtotime('+1 month'))],
        ['name' => 'SUMMER20', 'discount' => 20, 'valid_until' => date('Y-m-d', strtotime('+2 months'))],
    ];
    foreach ($coupons as $coupon) {
        $stmt = $db->prepare("INSERT OR IGNORE INTO coupons (name, discount, valid_until) VALUES (?, ?, ?)");
        $stmt->execute([$coupon['name'], $coupon['discount'], $coupon['valid_until']]);
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
        $stmt = $db->prepare("INSERT OR IGNORE INTO products (name, description, slug, thumbnail, price) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([
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
                $db->prepare("INSERT OR IGNORE INTO color_product (product_id, color_id) VALUES (?, ?)")
                    ->execute([$productId, $color['id']]);
            }
        }

        // Attach sizes
        foreach ($productData['sizes'] as $sizeName) {
            $sizeStmt = $db->prepare("SELECT id FROM sizes WHERE name = ?");
            $sizeStmt->execute([$sizeName]);
            $size = $sizeStmt->fetch(PDO::FETCH_ASSOC);
            if ($size) {
                $db->prepare("INSERT OR IGNORE INTO product_size (product_id, size_id) VALUES (?, ?)")
                    ->execute([$productId, $size['id']]);
            }
        }
    }
    echo " ✓\n";

    echo "\n✅ Database seeded successfully!\n\n";
    echo "📊 Seeded Data:\n";
    echo "   • Colors: " . count($colors) . "\n";
    echo "   • Sizes: " . count($sizes) . "\n";
    echo "   • Coupons: " . count($coupons) . "\n";
    echo "   • Products: " . count($products) . "\n";
} catch (Exception $e) {
    echo "\n❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}
