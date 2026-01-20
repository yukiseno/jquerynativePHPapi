<?php

/**
 * Middleware and Helper Functions
 * Handles authentication, request parsing, and common utilities
 */

/**
 * Extract Bearer token from Authorization header
 */
function getBearerToken()
{
    $headers = getallheaders();
    if (isset($headers['Authorization'])) {
        if (preg_match('/Bearer\s+(\S+)/', $headers['Authorization'], $matches)) {
            return $matches[1];
        }
    }
    return null;
}

/**
 * Parse JSON request body
 */
function getJsonBody()
{
    return json_decode(file_get_contents('php://input'), true) ?? [];
}

/**
 * Verify Bearer token and return authenticated user
 * Returns array with 'userObj' and 'user' keys
 * Exits with 401 error if token is invalid
 */
function verifyUserToken()
{
    $token = getBearerToken();

    if (!$token) {
        http_response_code(401);
        echo json_encode(['error' => 'Unauthorized']);
        exit;
    }

    try {
        $userObj = new User();
        $user = $userObj->verifyToken($token);

        if (!$user) {
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }

        return array('userObj' => $userObj, 'user' => $user);
    } catch (Exception $e) {
        http_response_code(401);
        echo json_encode(['error' => 'Unauthorized']);
        exit;
    }
}
