<?php

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// HTTP Status Code Constants
const HTTP_OK = 200;
const HTTP_CREATED = 201;
const HTTP_BAD_REQUEST = 400;
const HTTP_UNAUTHORIZED = 401;
const HTTP_FORBIDDEN = 403;
const HTTP_NOT_FOUND = 404;
const HTTP_SERVER_ERROR = 500;

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(HTTP_OK);
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
        apiError('Name, email, and password are required', HTTP_BAD_REQUEST);
    }

    $user = new User();
    $result = $user->register($name, $email, $password);

    if ($result['success']) {
        apiSuccess(null, $result['message'], HTTP_CREATED);
    } else {
        apiError($result['message'], HTTP_BAD_REQUEST);
    }
}

// USER LOGIN
if ($method === 'POST' && $path === 'user/login') {
    $body = getJsonBody();
    $email = $body['email'] ?? null;
    $password = $body['password'] ?? null;

    if (!$email || !$password) {
        apiError('Email and password are required', HTTP_BAD_REQUEST);
    }

    $user = new User();
    $result = $user->login($email, $password);

    if ($result['success']) {
        apiSuccess([
            'user' => $result['user'],
            'access_token' => $result['access_token']
        ], null, HTTP_OK);
    } else {
        apiError($result['message'], HTTP_UNAUTHORIZED);
    }
}

// USER LOGOUT
if ($method === 'POST' && $path === 'user/logout') {
    $token = getBearerToken();

    if (!$token) {
        apiError('Unauthorized', HTTP_UNAUTHORIZED);
    }

    $user = new User();
    $result = $user->logout($token);

    if ($result['success']) {
        apiSuccess(null, $result['message'], HTTP_OK);
    } else {
        apiError($result['message'], HTTP_BAD_REQUEST);
    }
}

// API Routes
if ($method === 'POST' && $path === 'apply/coupon') {
    $body = getJsonBody();
    $couponCode = $body['coupon_code'] ?? $body['name'] ?? null;

    if (!$couponCode) {
        apiError('Coupon code is required', HTTP_BAD_REQUEST);
    }

    try {
        $couponObj = new Coupon();
        $coupon = $couponObj->findByName($couponCode);

        if (!$coupon) {
            apiError('Invalid or expired coupon', HTTP_BAD_REQUEST);
        }

        if (!$coupon->isValid()) {
            apiError('Invalid or expired coupon', HTTP_BAD_REQUEST);
        }

        apiSuccess($coupon->toApiArray(), 'Coupon applied successfully', HTTP_OK);
    } catch (Exception $e) {
        apiError('Server error: ' . $e->getMessage(), HTTP_SERVER_ERROR);
    }
}

// GET all products
if ($method === 'GET' && $path === 'products') {
    try {
        $product = new Product();
        $result = $product->getAll();
        apiSuccess($result, null, HTTP_OK);
    } catch (Exception $e) {
        apiError('Server error: ' . $e->getMessage(), HTTP_SERVER_ERROR);
    }
}

// GET products by color
if ($method === 'GET' && preg_match('/^products\/(\d+)\/color$/', $path, $matches)) {
    $colorId = $matches[1];
    try {
        $product = new Product();
        $result = $product->filterByColor($colorId);
        apiSuccess($result, null, HTTP_OK);
    } catch (Exception $e) {
        apiError('Server error: ' . $e->getMessage(), HTTP_SERVER_ERROR);
    }
}

// GET products by size
if ($method === 'GET' && preg_match('/^products\/(\d+)\/size$/', $path, $matches)) {
    $sizeId = $matches[1];
    try {
        $product = new Product();
        $result = $product->filterBySize($sizeId);
        apiSuccess($result, null, HTTP_OK);
    } catch (Exception $e) {
        apiError('Server error: ' . $e->getMessage(), HTTP_SERVER_ERROR);
    }
}

// GET products by search term
if ($method === 'GET' && preg_match('/^products\/(.+)\/find$/', $path, $matches)) {
    $searchTerm = urldecode($matches[1]);
    try {
        $product = new Product();
        $result = $product->findByTerm($searchTerm);
        apiSuccess($result, null, HTTP_OK);
    } catch (Exception $e) {
        apiError('Server error: ' . $e->getMessage(), HTTP_SERVER_ERROR);
    }
}

// GET single product
if ($method === 'GET' && preg_match('/^product\/(\d+)\/show$/', $path, $matches)) {
    $productId = $matches[1];
    try {
        $product = new Product();
        $result = $product->findById($productId);
        if (!$result) {
            apiError('Product not found', HTTP_NOT_FOUND);
        }
        apiSuccess($result, null, HTTP_OK);
    } catch (Exception $e) {
        apiError('Server error: ' . $e->getMessage(), HTTP_SERVER_ERROR);
    }
}

// GET single product by slug
if ($method === 'GET' && preg_match('/^product\/(.+)\/slug$/', $path, $matches)) {
    $slug = $matches[1];
    try {
        $product = new Product();
        $result = $product->findBySlug($slug);
        if (!$result) {
            apiError('Product not found', HTTP_NOT_FOUND);
        }
        apiSuccess($result, null, HTTP_OK);
    } catch (Exception $e) {
        apiError('Server error: ' . $e->getMessage(), HTTP_SERVER_ERROR);
    }
}

// POST create order
if ($method === 'POST' && $path === 'orders/store') {
    $auth = verifyUserToken();
    $user = $auth['user'];

    $data = json_decode(file_get_contents('php://input'), true);

    try {
        // Validate input
        if (empty($data['cartItems']) || !is_array($data['cartItems'])) {
            apiError('Invalid cart items', HTTP_BAD_REQUEST);
        }

        // Validate each cart item has required fields
        foreach ($data['cartItems'] as $item) {
            if (empty($item['id']) || empty($item['colorId']) || empty($item['sizeId'])) {
                apiError('Cart items must have id, colorId, and sizeId', HTTP_BAD_REQUEST);
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

        apiSuccess($order, 'Order placed successfully', HTTP_CREATED);
    } catch (Exception $e) {
        apiError('Server error: ' . $e->getMessage(), HTTP_SERVER_ERROR);
    }
}

// GET user orders
if ($method === 'GET' && $path === 'user/orders') {
    $auth = verifyUserToken();
    $user = $auth['user'];

    try {
        $orderObj = new Order();
        $orders = $orderObj->getUserOrders($user['id']);
        apiSuccess($orders, null, HTTP_OK);
    } catch (Exception $e) {
        apiError('Server error: ' . $e->getMessage(), HTTP_SERVER_ERROR);
    }
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
            apiError('Order not found', HTTP_NOT_FOUND);
        }

        // Verify order belongs to user
        if ($order['order']['user_id'] !== $user['id']) {
            apiError('Forbidden', HTTP_FORBIDDEN);
        }

        apiSuccess(array_merge($order['order'], ['items' => $order['items']]), null, HTTP_OK);
    } catch (Exception $e) {
        apiError('Server error: ' . $e->getMessage(), HTTP_SERVER_ERROR);
    }
}

// GET user profile
if ($method === 'GET' && $path === 'user/profile') {
    $auth = verifyUserToken();
    $userObj = $auth['userObj'];
    $user = $auth['user'];

    try {
        // Fetch latest user info from database - reuse existing instance
        $updatedUser = $userObj->findById($user['id']);

        apiSuccess($updatedUser, null, HTTP_OK);
    } catch (Exception $e) {
        apiError('Server error: ' . $e->getMessage(), HTTP_SERVER_ERROR);
    }
}

// Update user profile
if ($method === 'PATCH' && $path === 'user/profile/update') {
    $auth = verifyUserToken();
    $userObj = $auth['userObj'];
    $user = $auth['user'];

    $data = json_decode(file_get_contents('php://input'), true);

    try {
        // Reuse existing instance
        $updatedUser = $userObj->updateProfile($user['id'], $data);

        apiSuccess($updatedUser, null, HTTP_OK);
    } catch (Exception $e) {
        apiError('Server error: ' . $e->getMessage(), HTTP_SERVER_ERROR);
    }
}

// Generate 2FA secret
if ($method === 'GET' && $path === 'user/2fa/setup') {
    $auth = verifyUserToken();
    $user = $auth['user'];
    $userObj = $auth['userObj'];

    try {
        $result = $userObj->generate2FASecret($user['email']);
        apiSuccess($result, null, HTTP_OK);
    } catch (Exception $e) {
        apiError('Server error: ' . $e->getMessage(), HTTP_SERVER_ERROR);
    }
}

// Enable 2FA
if ($method === 'POST' && $path === 'user/2fa/enable') {
    $auth = verifyUserToken();
    $user = $auth['user'];
    $userObj = $auth['userObj'];

    $data = json_decode(file_get_contents('php://input'), true);
    $secret = $data['secret'] ?? null;
    $verificationCode = $data['code'] ?? null;

    if (!$secret || !$verificationCode) {
        apiError('Secret and code are required', HTTP_BAD_REQUEST);
    }

    try {
        $result = $userObj->enable2FA($user['id'], $secret, $verificationCode);

        if ($result['success']) {
            apiSuccess(null, $result['message'], HTTP_OK);
        } else {
            apiError($result['message'], HTTP_BAD_REQUEST);
        }
    } catch (Exception $e) {
        apiError('Server error: ' . $e->getMessage(), HTTP_SERVER_ERROR);
    }
}

// Disable 2FA
if ($method === 'POST' && $path === 'user/2fa/disable') {
    $auth = verifyUserToken();
    $user = $auth['user'];
    $userObj = $auth['userObj'];

    try {
        $result = $userObj->disable2FA($user['id']);
        apiSuccess(null, $result['message'], HTTP_OK);
    } catch (Exception $e) {
        apiError('Server error: ' . $e->getMessage(), HTTP_SERVER_ERROR);
    }
}

// Verify 2FA code (used during login)
if ($method === 'POST' && $path === 'user/verify-2fa') {
    $data = json_decode(file_get_contents('php://input'), true);
    $userId = $data['user_id'] ?? null;
    $code = $data['code'] ?? null;

    if (!$userId || !$code) {
        apiError('User ID and code are required', HTTP_BAD_REQUEST);
    }

    try {
        $userObj = new User();

        if ($userObj->verify2FACode($userId, $code)) {
            // Generate temporary token for 2FA verification
            $tempToken = bin2hex(random_bytes(32));
            apiSuccess(['verified' => true], '2FA verification successful', HTTP_OK);
        } else {
            apiError('Invalid 2FA code', HTTP_UNAUTHORIZED);
        }
    } catch (Exception $e) {
        apiError('Server error: ' . $e->getMessage(), HTTP_SERVER_ERROR);
    }
}
