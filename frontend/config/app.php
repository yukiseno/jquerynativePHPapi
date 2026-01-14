<?php

// Application Configuration
define('BASE_URL', 'http://localhost:3000');
define('API_URL', 'http://localhost:3001/api');
define('APP_NAME', 'ShopHub');
define('APP_DEBUG', true);

// Session Configuration
session_start();

// Helper function to get current page
function getCurrentPage()
{
    $path = $_GET['page'] ?? 'home';
    return preg_replace('/[^a-z0-9\-_]/', '', $path);
}

// Helper function to redirect
function redirect($page = 'home', $params = [])
{
    // If it starts with '/', treat it as a clean URL path
    if (strpos($page, '/') === 0) {
        $url = BASE_URL . $page;
    } else {
        // Legacy query string based routing
        $url = BASE_URL . '/?page=' . $page;
    }

    if (!empty($params)) {
        $url .= (strpos($url, '?') !== false ? '&' : '?') . http_build_query($params);
    }
    header('Location: ' . $url);
    exit;
}

// Helper function to check if user is logged in
function isLoggedIn()
{
    return isset($_SESSION['auth_token']) && isset($_SESSION['user']);
}

// Helper function to get current user
function getCurrentUser()
{
    return $_SESSION['user'] ?? null;
}

// Helper function to get auth token
function getAuthToken()
{
    return $_SESSION['auth_token'] ?? null;
}

// Set auth session
function setAuth($token, $user)
{
    $_SESSION['auth_token'] = $token;
    $_SESSION['user'] = $user;
}

// Clear auth session
function clearAuth()
{
    unset($_SESSION['auth_token']);
    unset($_SESSION['user']);
    session_destroy();
}
