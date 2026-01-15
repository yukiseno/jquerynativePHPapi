<?php

class Order
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Get the correct datetime function for the current database type
     */
    private static function getDatetimeFunction()
    {
        $dbType = getenv('DB_TYPE') ?: 'sqlite';
        return $dbType === 'sqlite' ? "datetime('now')" : 'NOW()';
    }

    /**
     * Create order and order items
     * $data = [
     *   'user_id' => int,
     *   'cartItems' => [
     *     ['id' => int, 'name' => string, 'price' => int (cents), 'quantity' => int, 'colorId' => int, 'colorName' => string, 'sizeId' => int, 'sizeName' => string],
     *     ...
     *   ],
     *   'address' => ['phoneNumber' => string, 'address' => string, 'city' => string, 'country' => string, 'zip' => string],
     *   'couponId' => int (optional)
     * ]
     */
    public static function createOrder($data)
    {
        $db = Database::getInstance();

        try {
            $db->beginTransaction();

            // Calculate totals
            $subtotal = 0;
            foreach ($data['cartItems'] as $item) {
                $subtotal += $item['price'] * $item['quantity'];
            }

            // Apply coupon discount if provided
            $discountTotal = 0;
            $couponId = null;

            if (!empty($data['couponId'])) {
                $coupon = self::getCoupon($data['couponId']);
                if ($coupon) {
                    $discountTotal = (int)round($subtotal * ($coupon['discount'] / 100));
                    $couponId = $coupon['id'];
                }
            }

            $total = $subtotal - $discountTotal;

            // Generate payment intent ID (random)
            $paymentIntentId = 'pi_' . bin2hex(random_bytes(16));

            // Create order
            $datetimeFunc = self::getDatetimeFunction();
            $stmt = $db->prepare("
                INSERT INTO orders (user_id, coupon_id, subtotal, discount_total, total, status, payment_intent_id, created_at, updated_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, {$datetimeFunc}, {$datetimeFunc})
            ");

            $stmt->execute([
                $data['user_id'],
                $couponId,
                $subtotal,
                $discountTotal,
                $total,
                'paid', // We'll use 'paid' since there's no payment processing
                $paymentIntentId
            ]);

            $orderId = $db->lastInsertId();

            // Create order items
            foreach ($data['cartItems'] as $item) {
                $datetimeFunc = self::getDatetimeFunction();
                $itemStmt = $db->prepare("
                    INSERT INTO order_items (order_id, product_id, product_name, color_id, size_id, color_name, size_name, qty, price, subtotal, created_at, updated_at)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, {$datetimeFunc}, {$datetimeFunc})
                ");

                $itemSubtotal = $item['price'] * $item['quantity'];

                $itemStmt->execute([
                    $orderId,
                    $item['id'] ?? null,
                    $item['name'] ?? null,
                    $item['colorId'] ?? null,
                    $item['sizeId'] ?? null,
                    $item['colorName'] ?? null,
                    $item['sizeName'] ?? null,
                    $item['quantity'] ?? 0,
                    $item['price'] ?? 0,
                    $itemSubtotal
                ]);
            }

            // Update user address if provided
            if (!empty($data['address'])) {
                $dbType = getenv('DB_TYPE') ?: 'sqlite';
                if ($dbType === 'mysql') {
                    // For MySQL, we don't need to explicitly set updated_at as it has ON UPDATE CURRENT_TIMESTAMP
                    $addressStmt = $db->prepare("
                        UPDATE users 
                        SET phone_number = ?, address = ?, city = ?, country = ?, zip_code = ?
                        WHERE id = ?
                    ");
                } else {
                    // For SQLite, we need to explicitly set the updated_at
                    $addressStmt = $db->prepare("
                        UPDATE users 
                        SET phone_number = ?, address = ?, city = ?, country = ?, zip_code = ?, updated_at = datetime('now')
                        WHERE id = ?
                    ");
                }

                $addressStmt->execute([
                    $data['address']['phoneNumber'] ?? '',
                    $data['address']['address'] ?? '',
                    $data['address']['city'] ?? '',
                    $data['address']['country'] ?? '',
                    $data['address']['zip'] ?? '',
                    $data['user_id']
                ]);
            }

            $db->commit();

            // Fetch and return the created order with items
            return self::getOrderById($orderId);
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }

    /**
     * Get order by ID with items
     */
    public static function getOrderById($orderId)
    {
        $db = Database::getInstance();

        $stmt = $db->prepare("
            SELECT o.*, u.name as user_name, u.email as user_email, c.name as coupon_name, c.discount as coupon_discount
            FROM orders o
            LEFT JOIN users u ON o.user_id = u.id
            LEFT JOIN coupons c ON o.coupon_id = c.id
            WHERE o.id = ?
            LIMIT 1
        ");

        $stmt->execute([$orderId]);
        $order = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$order) {
            return null;
        }

        // Fetch order items
        $itemStmt = $db->prepare("
            SELECT * FROM order_items WHERE order_id = ?
        ");
        $itemStmt->execute([$orderId]);
        $items = $itemStmt->fetchAll(PDO::FETCH_ASSOC);

        return [
            'order' => [
                'id' => (int)$order['id'],
                'user_id' => (int)$order['user_id'],
                'user_name' => $order['user_name'],
                'user_email' => $order['user_email'],
                'coupon_id' => $order['coupon_id'] ? (int)$order['coupon_id'] : null,
                'coupon_name' => $order['coupon_name'],
                'coupon_discount' => $order['coupon_discount'],
                'subtotal' => (int)$order['subtotal'],
                'discount_total' => (int)$order['discount_total'],
                'total' => (int)$order['total'],
                'status' => $order['status'],
                'payment_intent_id' => $order['payment_intent_id'],
                'created_at' => $order['created_at'],
                'updated_at' => $order['updated_at']
            ],
            'items' => array_map(function ($item) {
                return [
                    'id' => (int)$item['id'],
                    'order_id' => (int)$item['order_id'],
                    'product_id' => (int)$item['product_id'],
                    'product_name' => $item['product_name'],
                    'color_id' => (int)$item['color_id'],
                    'size_id' => (int)$item['size_id'],
                    'color_name' => $item['color_name'],
                    'size_name' => $item['size_name'],
                    'qty' => (int)$item['qty'],
                    'price' => (int)$item['price'],
                    'subtotal' => (int)$item['subtotal'],
                    'created_at' => $item['created_at']
                ];
            }, $items)
        ];
    }

    /**
     * Get coupon by ID and check if valid
     */
    private static function getCoupon($couponId)
    {
        $db = Database::getInstance();

        $stmt = $db->prepare("
            SELECT * FROM coupons WHERE id = ? LIMIT 1
        ");
        $stmt->execute([$couponId]);
        $coupon = $stmt->fetch(PDO::FETCH_ASSOC);

        return $coupon;
    }

    /**
     * Get user's orders
     */
    public static function getUserOrders($userId)
    {
        $db = Database::getInstance();

        $stmt = $db->prepare("
            SELECT o.*, c.name as coupon_name
            FROM orders o
            LEFT JOIN coupons c ON o.coupon_id = c.id
            WHERE o.user_id = ?
            ORDER BY o.created_at DESC
        ");

        $stmt->execute([$userId]);
        $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map(function ($order) {
            return [
                'id' => (int)$order['id'],
                'coupon_name' => $order['coupon_name'],
                'subtotal' => (int)$order['subtotal'],
                'discount_total' => (int)$order['discount_total'],
                'total' => (int)$order['total'],
                'status' => $order['status'],
                'payment_intent_id' => $order['payment_intent_id'],
                'created_at' => $order['created_at']
            ];
        }, $orders);
    }
}
