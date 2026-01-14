<?php

require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/models/ApiClient.php';
require_once __DIR__ . '/models/Product.php';
require_once __DIR__ . '/models/User.php';
require_once __DIR__ . '/models/Coupon.php';

// Initialize API client
$api = new ApiClient(API_URL);
Product::setApi($api);
User::setApi($api);
Coupon::setApi($api);

// Parse URL for clean routes
$path = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
$basePath = 'jquerynativePHPapi/frontend/'; // Adjust based on your setup
if (strpos($path, $basePath) === 0) {
    $path = substr($path, strlen($basePath));
}

// Extract page and parameters from path
$pageParts = explode('/', filter_var($path, FILTER_SANITIZE_URL));
$page = $pageParts[0] ?? '/';
$param = $pageParts[1] ?? null;

// Support both clean URLs and query string
if (isset($_GET['page'])) {
    $page = $_GET['page'];
    $param = $_GET['id'] ?? $_GET['slug'] ?? null;
}

// Handle logout action
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    clearAuth();
    redirect('/');
}

// Define pages that require login
$protectedPages = ['profile', 'orders', 'checkout'];

// Check authentication for protected pages
if (in_array($page, $protectedPages) && !isLoggedIn()) {
    redirect('/login', ['redirect' => $page]);
}

// Redirect logged-in users away from login/register
if (in_array($page, ['login', 'register']) && isLoggedIn()) {
    redirect('/');
}

// Include appropriate controller
$controllerFile = __DIR__ . '/controllers/' . ucfirst($page) . 'Controller.php';

if (file_exists($controllerFile)) {
    require_once $controllerFile;
    $controllerClass = ucfirst($page) . 'Controller';
    if (class_exists($controllerClass)) {
        $controller = new $controllerClass();
        $data = $controller->index($param);
    } else {
        $data = ['error' => 'Controller not found'];
    }
} else {
    // Default to home if page doesn't exist
    require_once __DIR__ . '/controllers/HomeController.php';
    $controller = new HomeController();
    $data = $controller->index();
    $page = 'home';
}

// Load base layout with view
require_once __DIR__ . '/views/layout.php';
