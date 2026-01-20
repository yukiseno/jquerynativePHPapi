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
 * Send standardized error response
 */
function apiError($message, $code = 400)
{
    http_response_code($code);
    echo json_encode(['error' => $message]);
    exit;
}

/**
 * Send standardized success response
 */
function apiSuccess($data = null, $message = null, $code = 200)
{
    http_response_code($code);
    $response = ['success' => true];

    if ($message) {
        $response['message'] = $message;
    }

    if ($data !== null) {
        $response['data'] = $data;
    }

    echo json_encode($response);
    exit;
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
        apiError('Unauthorized', 401);
    }

    try {
        $userObj = new User();
        $user = $userObj->verifyToken($token);

        if (!$user) {
            apiError('Unauthorized', 401);
        }

        return array('userObj' => $userObj, 'user' => $user);
    } catch (Exception $e) {
        apiError('Unauthorized', 401);
    }
}
