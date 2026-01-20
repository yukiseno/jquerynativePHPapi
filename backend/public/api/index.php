<?php

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Load environment variables
if (file_exists(__DIR__ . '/../../.env')) {
    $env = file_get_contents(__DIR__ . '/../../.env');
    foreach (explode("\n", $env) as $line) {
        if (strpos($line, '=') !== false && strpos($line, '#') !== 0) {
            list($key, $value) = explode('=', $line, 2);
            putenv(trim($key) . '=' . trim($value));
        }
    }
}

// Load classes
require_once __DIR__ . '/../../classes/Database.php';
require_once __DIR__ . '/../../classes/Coupon.php';
require_once __DIR__ . '/../../classes/Product.php';
require_once __DIR__ . '/../../classes/User.php';
require_once __DIR__ . '/../../classes/Order.php';

// Load middleware and helpers
require_once __DIR__ . '/../../middleware.php';

// Parse request
$method = $_SERVER['REQUEST_METHOD'];
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$path = preg_replace('/^\/api/', '', $path);
$path = trim($path, '/');

// USER REGISTER
if ($method === 'POST' && $path === 'user/register') {
    $body = getJsonBody();
    $name = $body['name'] ?? null;
    $email = $body['email'] ?? null;
    $password = $body['password'] ?? null;

    if (!$name || !$email || !$password) {
        http_response_code(400);
        echo json_encode(['error' => 'Name, email, and password are required']);
        exit;
    }

    $user = new User();
    $result = $user->register($name, $email, $password);

    if ($result['success']) {
        http_response_code(201);
        echo json_encode(['message' => $result['message']]);
    } else {
        http_response_code(400);
        echo json_encode(['error' => $result['message']]);
    }
    exit;
}

// USER LOGIN
if ($method === 'POST' && $path === 'user/login') {
    $body = getJsonBody();
    $email = $body['email'] ?? null;
    $password = $body['password'] ?? null;

    if (!$email || !$password) {
        http_response_code(400);
        echo json_encode(['error' => 'Email and password are required']);
        exit;
    }

    $user = new User();
    $result = $user->login($email, $password);

    if ($result['success']) {
        http_response_code(200);
        echo json_encode([
            'user' => $result['user'],
            'access_token' => $result['access_token']
        ]);
    } else {
        http_response_code(401);
        echo json_encode(['error' => $result['message']]);
    }
    exit;
}

// USER LOGOUT
if ($method === 'POST' && $path === 'user/logout') {
    $token = getBearerToken();

    if (!$token) {
        http_response_code(401);
        echo json_encode(['error' => 'Unauthorized']);
        exit;
    }

    $user = new User();
    $result = $user->logout($token);

    if ($result['success']) {
        http_response_code(200);
        echo json_encode(['message' => $result['message']]);
    } else {
        http_response_code(400);
        echo json_encode(['error' => $result['message']]);
    }
    exit;
}

// API Routes
if ($method === 'POST' && $path === 'apply/coupon') {
    $body = getJsonBody();
    $couponCode = $body['coupon_code'] ?? $body['name'] ?? null;

    if (!$couponCode) {
        http_response_code(400);
        echo json_encode(['error' => 'Coupon code is required']);
        exit;
    }

    try {
        $couponObj = new Coupon();
        $coupon = $couponObj->findByName($couponCode);

        if (!$coupon) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid or expired coupon']);
            exit;
        }

        if (!$coupon->isValid()) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid or expired coupon']);
            exit;
        }

        http_response_code(200);
        echo json_encode([
            'success' => true,
            'message' => 'Coupon applied successfully',
            'data' => $coupon->toApiArray()
        ]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Server error: ' . $e->getMessage()]);
    }
    exit;
}

// GET all products
if ($method === 'GET' && $path === 'products') {
    try {
        $product = new Product();
        $result = $product->getAll();
        http_response_code(200);
        echo json_encode($result);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Server error: ' . $e->getMessage()]);
    }
    exit;
}

// GET products by color
if ($method === 'GET' && preg_match('/^products\/(\d+)\/color$/', $path, $matches)) {
    $colorId = $matches[1];
    try {
        $product = new Product();
        $result = $product->filterByColor($colorId);
        http_response_code(200);
        echo json_encode($result);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Server error: ' . $e->getMessage()]);
    }
    exit;
}

// GET products by size
if ($method === 'GET' && preg_match('/^products\/(\d+)\/size$/', $path, $matches)) {
    $sizeId = $matches[1];
    try {
        $product = new Product();
        $result = $product->filterBySize($sizeId);
        http_response_code(200);
        echo json_encode($result);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Server error: ' . $e->getMessage()]);
    }
    exit;
}

// GET products by search term
if ($method === 'GET' && preg_match('/^products\/(.+)\/find$/', $path, $matches)) {
    $searchTerm = urldecode($matches[1]);
    try {
        $product = new Product();
        $result = $product->findByTerm($searchTerm);
        http_response_code(200);
        echo json_encode($result);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Server error: ' . $e->getMessage()]);
    }
    exit;
}

// GET single product
if ($method === 'GET' && preg_match('/^product\/(\d+)\/show$/', $path, $matches)) {
    $productId = $matches[1];
    try {
        $product = new Product();
        $result = $product->findById($productId);
        if (!$result) {
            http_response_code(404);
            echo json_encode(['error' => 'Product not found']);
            exit;
        }
        http_response_code(200);
        echo json_encode($result);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Server error: ' . $e->getMessage()]);
    }
    exit;
}

// GET single product by slug
if ($method === 'GET' && preg_match('/^product\/(.+)\/slug$/', $path, $matches)) {
    $slug = $matches[1];
    try {
        $product = new Product();
        $result = $product->findBySlug($slug);
        if (!$result) {
            http_response_code(404);
            echo json_encode(['error' => 'Product not found']);
            exit;
        }
        http_response_code(200);
        echo json_encode($result);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Server error: ' . $e->getMessage()]);
    }
    exit;
}

// POST create order
if ($method === 'POST' && $path === 'orders/store') {
    $auth = verifyUserToken();
    $user = $auth['user'];

    $data = json_decode(file_get_contents('php://input'), true);

    try {
        // Validate input
        if (empty($data['cartItems']) || !is_array($data['cartItems'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid cart items']);
            exit;
        }

        // Validate each cart item has required fields
        foreach ($data['cartItems'] as $item) {
            if (empty($item['id']) || empty($item['colorId']) || empty($item['sizeId'])) {
                http_response_code(400);
                echo json_encode(['error' => 'Cart items must have id, colorId, and sizeId']);
                exit;
            }
        }

        // Create order
        $order = new Order();
        $result = $order->createOrder([
            'user_id' => $user['id'],
            'cartItems' => $data['cartItems'],
            'address' => $data['address'] ?? [],
            'couponId' => $data['couponId'] ?? null
        ]);

        http_response_code(201);
        echo json_encode([
            'message' => 'Order placed successfully',
            'data' => $order
        ]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Server error: ' . $e->getMessage()]);
    }
    exit;
}

// GET user orders
if ($method === 'GET' && $path === 'user/orders') {
    $auth = verifyUserToken();
    $user = $auth['user'];

    try {
        $orderObj = new Order();
        $orders = $orderObj->getUserOrders($user['id']);
        http_response_code(200);
        echo json_encode([
            'data' => $orders
        ]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Server error: ' . $e->getMessage()]);
    }
    exit;
}

// Get single order by ID
if ($method === 'GET' && preg_match('/^orders\/(\d+)$/', $path, $matches)) {
    $orderId = $matches[1];
    $auth = verifyUserToken();
    $user = $auth['user'];

    try {
        $orderObj = new Order();
        $order = $orderObj->getOrderById($orderId);

        if (!$order) {
            http_response_code(404);
            echo json_encode(['error' => 'Order not found']);
            exit;
        }

        // Verify order belongs to user
        if ($order['order']['user_id'] !== $user['id']) {
            http_response_code(403);
            echo json_encode(['error' => 'Forbidden']);
            exit;
        }

        http_response_code(200);
        echo json_encode([
            'data' => array_merge($order['order'], ['items' => $order['items']])
        ]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Server error: ' . $e->getMessage()]);
    }
    exit;
}

// GET user profile
if ($method === 'GET' && $path === 'user/profile') {
    $auth = verifyUserToken();
    $userObj = $auth['userObj'];
    $user = $auth['user'];

    try {
        // Fetch latest user info from database - reuse existing instance
        $updatedUser = $userObj->findById($user['id']);

        http_response_code(200);
        echo json_encode([
            'data' => $updatedUser
        ]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Server error: ' . $e->getMessage()]);
    }
    exit;
}

// Update user profile
if ($method === 'POST' && $path === 'user/profile/update') {
    $auth = verifyUserToken();
    $userObj = $auth['userObj'];
    $user = $auth['user'];

    $data = json_decode(file_get_contents('php://input'), true);

    try {
        // Reuse existing instance
        $updatedUser = $userObj->updateProfile($user['id'], $data);

        http_response_code(200);
        echo json_encode([
            'data' => $updatedUser
        ]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Server error: ' . $e->getMessage()]);
    }
    exit;
}
