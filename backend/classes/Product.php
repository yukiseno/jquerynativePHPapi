<?php

class Product
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Get all products with colors and sizes
     */
    public function getAll()
    {
        $products = $this->db->query("SELECT * FROM products ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);

        [$productData, $allColors, $allSizes] = $this->enrichProductsWithRelations($products);

        return [
            'data' => $productData,
            'colors' => $allColors,
            'sizes' => $allSizes
        ];
    }

    /**
     * Filter products by color
     */
    public function filterByColor($colorId)
    {
        $stmt = $this->db->prepare("
            SELECT DISTINCT p.* FROM products p
            JOIN color_product pc ON p.id = pc.product_id
            WHERE pc.color_id = ? ORDER BY p.id DESC
        ");
        $stmt->execute([$colorId]);
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

        [$productData, $allColors, $allSizes] = $this->enrichProductsWithRelations($products);

        return [
            'data' => $productData,
            'colors' => $allColors,
            'sizes' => $allSizes
        ];
    }

    /**
     * Filter products by size
     */
    public function filterBySize($sizeId)
    {
        $stmt = $this->db->prepare("
            SELECT DISTINCT p.* FROM products p
            JOIN product_size ps ON p.id = ps.product_id
            WHERE ps.size_id = ? ORDER BY p.id DESC
        ");
        $stmt->execute([$sizeId]);
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

        [$productData, $allColors, $allSizes] = $this->enrichProductsWithRelations($products);

        return [
            'data' => $productData,
            'colors' => $allColors,
            'sizes' => $allSizes
        ];
    }

    /**
     * Search products by term
     */
    public function findByTerm($searchTerm)
    {
        $stmt = $this->db->prepare("SELECT * FROM products WHERE name LIKE ? ORDER BY id DESC");
        $stmt->execute(['%' . $searchTerm . '%']);
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

        [$productData, $allColors, $allSizes] = $this->enrichProductsWithRelations($products);

        return [
            'data' => $productData,
            'colors' => $allColors,
            'sizes' => $allSizes
        ];
    }

    /**
     * Get single product by ID
     */
    public function findById($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM products WHERE id = ? LIMIT 1");
        $stmt->execute([$id]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$product) {
            return null;
        }

        [$productData, $allColors, $allSizes] = $this->enrichProductsWithRelations([$product]);

        return [
            'data' => $productData[0],
            'colors' => $allColors,
            'sizes' => $allSizes
        ];
    }

    /**
     * Get single product by slug
     */
    public function findBySlug($slug)
    {
        $stmt = $this->db->prepare("SELECT * FROM products WHERE slug = ? LIMIT 1");
        $stmt->execute([$slug]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$product) {
            return null;
        }

        [$productData, $allColors, $allSizes] = $this->enrichProductsWithRelations([$product]);

        return [
            'data' => $productData[0],
            'colors' => $allColors,
            'sizes' => $allSizes
        ];
    }

    /**
     * Enrich products with their colors and sizes
     * Returns [productData, allColors, allSizes]
     */
    private function enrichProductsWithRelations($products)
    {
        $productIds = array_map(fn($p) => $p['id'], $products);
        $productColors = [];
        $productSizes = [];

        if (!empty($productIds)) {
            $placeholders = implode(',', $productIds);

            $colorResults = $this->db->query("
                SELECT c.id, c.name, c.created_at, c.updated_at, cp.product_id
                FROM colors c
                JOIN color_product cp ON c.id = cp.color_id
                WHERE cp.product_id IN ($placeholders)
                ORDER BY c.name
            ")->fetchAll(PDO::FETCH_ASSOC);

            $sizeResults = $this->db->query("
                SELECT s.id, s.name, s.created_at, s.updated_at, ps.product_id
                FROM sizes s
                JOIN product_size ps ON s.id = ps.size_id
                WHERE ps.product_id IN ($placeholders)
                ORDER BY s.name
            ")->fetchAll(PDO::FETCH_ASSOC);

            foreach ($colorResults as $cr) {
                $pid = $cr['product_id'];
                if (!isset($productColors[$pid])) {
                    $productColors[$pid] = [];
                }
                $productColors[$pid][$cr['id']] = [
                    'id' => $cr['id'],
                    'name' => $cr['name'],
                    'created_at' => $cr['created_at'],
                    'updated_at' => $cr['updated_at']
                ];
            }

            foreach ($sizeResults as $sr) {
                $pid = $sr['product_id'];
                if (!isset($productSizes[$pid])) {
                    $productSizes[$pid] = [];
                }
                $productSizes[$pid][$sr['id']] = [
                    'id' => $sr['id'],
                    'name' => $sr['name'],
                    'created_at' => $sr['created_at'],
                    'updated_at' => $sr['updated_at']
                ];
            }

            foreach ($productColors as $pid => $colors) {
                $productColors[$pid] = array_values($colors);
            }
            foreach ($productSizes as $pid => $sizes) {
                $productSizes[$pid] = array_values($sizes);
            }
        }

        $productData = [];
        foreach ($products as $product) {
            $pid = $product['id'];
            $colors = $productColors[$pid] ?? [];
            $sizes = $productSizes[$pid] ?? [];
            $productData[] = $this->formatProduct($product, $colors, $sizes);
        }

        // Get only colors that are actually used by products
        $allColors = $this->db->query("
            SELECT DISTINCT c.id, c.name, c.created_at, c.updated_at 
            FROM colors c
            JOIN color_product cp ON c.id = cp.color_id
            ORDER BY c.name
        ")->fetchAll(PDO::FETCH_ASSOC);

        // Get only sizes that are actually used by products
        $allSizes = $this->db->query("
            SELECT DISTINCT s.id, s.name, s.created_at, s.updated_at 
            FROM sizes s
            JOIN product_size ps ON s.id = ps.size_id
            ORDER BY s.name
        ")->fetchAll(PDO::FETCH_ASSOC);

        return [$productData, $allColors, $allSizes];
    }

    /**
     * Format product with colors and sizes (no additional queries)
     * Accepts pre-fetched colors and sizes arrays
     */
    private function formatProduct($product, $colors = [], $sizes = [])
    {
        // Format colors for this product
        $colorData = [];
        foreach ($colors as $color) {
            $colorData[] = [
                'id' => $color['id'],
                'name' => $color['name'],
                'created_at' => $color['created_at'],
                'updated_at' => $color['updated_at'],
            ];
        }

        // Format sizes for this product
        $sizeData = [];
        foreach ($sizes as $size) {
            $sizeData[] = [
                'id' => $size['id'],
                'name' => $size['name'],
                'created_at' => $size['created_at'],
                'updated_at' => $size['updated_at'],
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
