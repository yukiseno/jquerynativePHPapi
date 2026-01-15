<?php

require_once __DIR__ . '/config/app.php';

// Handle AJAX request to set session
if (isset($_POST['action']) && $_POST['action'] === 'set_session') {
    $token = $_POST['token'] ?? null;
    $user = json_decode($_POST['user'] ?? '{}', true);

    if ($token && $user) {
        setAuth($token, $user);
        echo json_encode(['success' => true, 'message' => 'Session set']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid data']);
    }
    exit;
}

// Handle AJAX request to clear session
if (isset($_POST['action']) && $_POST['action'] === 'clear_session') {
    $token = $_POST['token'] ?? null;

    if (!$token) {
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'Unauthorized']);
        exit;
    }

    clearAuth();
    echo json_encode(['success' => true, 'message' => 'Session cleared']);
    exit;
}
