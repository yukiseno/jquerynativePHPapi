<?php

class Product
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Get all products with colors and sizes - Laravel format
     */
    public static function getAll()
    {
        $db = Database::getInstance();

        $products = $db->query("SELECT * FROM products ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
        $productData = [];

        foreach ($products as $product) {
            $productData[] = self::formatProduct($product);
        }

        $colors = $db->query("SELECT id, name, created_at, updated_at FROM colors ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);
        $sizes = $db->query("SELECT id, name, created_at, updated_at FROM sizes ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);

        return [
            'data' => $productData,
            'colors' => $colors,
            'sizes' => $sizes
        ];
    }

    /**
     * Filter products by color
     */
    public static function filterByColor($colorId)
    {
        $db = Database::getInstance();

        $stmt = $db->prepare("
            SELECT DISTINCT p.* FROM products p
            JOIN color_product pc ON p.id = pc.product_id
            WHERE pc.color_id = ? ORDER BY p.id DESC
        ");
        $stmt->execute([$colorId]);
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $productData = [];
        foreach ($products as $product) {
            $productData[] = self::formatProduct($product);
        }

        $colors = $db->query("SELECT id, name, created_at, updated_at FROM colors ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);
        $sizes = $db->query("SELECT id, name, created_at, updated_at FROM sizes ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);

        return [
            'data' => $productData,
            'colors' => $colors,
            'sizes' => $sizes
        ];
    }

    /**
     * Filter products by size
     */
    public static function filterBySize($sizeId)
    {
        $db = Database::getInstance();

        $stmt = $db->prepare("
            SELECT DISTINCT p.* FROM products p
            JOIN product_size ps ON p.id = ps.product_id
            WHERE ps.size_id = ? ORDER BY p.id DESC
        ");
        $stmt->execute([$sizeId]);
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $productData = [];
        foreach ($products as $product) {
            $productData[] = self::formatProduct($product);
        }

        $colors = $db->query("SELECT id, name, created_at, updated_at FROM colors ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);
        $sizes = $db->query("SELECT id, name, created_at, updated_at FROM sizes ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);

        return [
            'data' => $productData,
            'colors' => $colors,
            'sizes' => $sizes
        ];
    }

    /**
     * Search products by term
     */
    public static function findByTerm($searchTerm)
    {
        $db = Database::getInstance();

        $stmt = $db->prepare("SELECT * FROM products WHERE name LIKE ? OR description LIKE ? ORDER BY id DESC");
        $stmt->execute(['%' . $searchTerm . '%', '%' . $searchTerm . '%']);
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $productData = [];
        foreach ($products as $product) {
            $productData[] = self::formatProduct($product);
        }

        $colors = $db->query("SELECT id, name, created_at, updated_at FROM colors ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);
        $sizes = $db->query("SELECT id, name, created_at, updated_at FROM sizes ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);

        return [
            'data' => $productData,
            'colors' => $colors,
            'sizes' => $sizes
        ];
    }

    /**
     * Get single product by ID
     */
    public static function findById($id)
    {
        $db = Database::getInstance();

        $stmt = $db->prepare("SELECT * FROM products WHERE id = ? LIMIT 1");
        $stmt->execute([$id]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$product) {
            return null;
        }

        $colors = $db->query("SELECT id, name, created_at, updated_at FROM colors ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);
        $sizes = $db->query("SELECT id, name, created_at, updated_at FROM sizes ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);

        return [
            'data' => self::formatProduct($product),
            'colors' => $colors,
            'sizes' => $sizes
        ];
    }

    /**
     * Get single product by slug
     */
    public static function findBySlug($slug)
    {
        $db = Database::getInstance();

        $stmt = $db->prepare("SELECT * FROM products WHERE slug = ? LIMIT 1");
        $stmt->execute([$slug]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$product) {
            return null;
        }

        $colors = $db->query("SELECT id, name, created_at, updated_at FROM colors ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);
        $sizes = $db->query("SELECT id, name, created_at, updated_at FROM sizes ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);

        return [
            'data' => self::formatProduct($product),
            'colors' => $colors,
            'sizes' => $sizes
        ];
    }

    /**
     * Format product with colors, sizes, and reviews
     */
    private static function formatProduct($product)
    {
        $db = Database::getInstance();
        $productId = $product['id'];

        // Get colors with pivot data
        $colorStmt = $db->prepare("
            SELECT c.id, c.name, c.created_at, c.updated_at,
                   pc.product_id, pc.color_id
            FROM colors c
            JOIN color_product pc ON c.id = pc.color_id
            WHERE pc.product_id = ?
        ");
        $colorStmt->execute([$productId]);
        $colors = $colorStmt->fetchAll(PDO::FETCH_ASSOC);

        $colorData = [];
        foreach ($colors as $color) {
            $colorData[] = [
                'id' => $color['id'],
                'name' => $color['name'],
                'created_at' => $color['created_at'],
                'updated_at' => $color['updated_at'],
                'pivot' => [
                    'product_id' => $color['product_id'],
                    'color_id' => $color['color_id']
                ]
            ];
        }

        // Get sizes with pivot data
        $sizeStmt = $db->prepare("
            SELECT s.id, s.name, s.created_at, s.updated_at,
                   ps.product_id, ps.size_id
            FROM sizes s
            JOIN product_size ps ON s.id = ps.size_id
            WHERE ps.product_id = ?
        ");
        $sizeStmt->execute([$productId]);
        $sizes = $sizeStmt->fetchAll(PDO::FETCH_ASSOC);

        $sizeData = [];
        foreach ($sizes as $size) {
            $sizeData[] = [
                'id' => $size['id'],
                'name' => $size['name'],
                'created_at' => $size['created_at'],
                'updated_at' => $size['updated_at'],
                'pivot' => [
                    'product_id' => $size['product_id'],
                    'size_id' => $size['size_id']
                ]
            ];
        }

        // Get image path - extract filename only
        $thumbnailFile = $product['thumbnail'] ?? 'placeholder.png';
        // If it contains full path, extract just the filename
        if (strpos($thumbnailFile, '/') !== false) {
            $thumbnailFile = basename($thumbnailFile);
        }
        $thumbnail = 'http://localhost:3001/images/products/' . $thumbnailFile;

        return [
            'id' => $product['id'],
            'name' => $product['name'],
            'slug' => $product['slug'] ?? str_replace(' ', '-', strtolower($product['name'])),
            'desc' => $product['description'] ?? '',
            'qty' => $product['quantity'] ?? 0,
            'price' => $product['price'],
            'colors' => $colorData,
            'sizes' => $sizeData,
            'reviews' => [],
            'status' => $product['status'] ?? 1,
            'thumbnail' => $thumbnail,
            'first_image' => null,
            'second_image' => null,
            'third_image' => null
        ];
    }
}
