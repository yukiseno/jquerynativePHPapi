<?php

class User
{
    private static $api;

    public static function setApi($api)
    {
        self::$api = $api;
    }

    /**
     * Register a new user
     */
    public static function register($name, $email, $password)
    {
        $response = self::$api->post('/user/register', [
            'name' => $name,
            'email' => $email,
            'password' => $password
        ]);

        if (!$response['success']) {
            return [
                'success' => false,
                'error' => $response['data']['error'] ?? 'Registration failed'
            ];
        }

        return [
            'success' => true,
            'message' => $response['data']['message'] ?? 'Registration successful'
        ];
    }

    /**
     * Login user
     */
    public static function login($email, $password)
    {
        $response = self::$api->post('/user/login', [
            'email' => $email,
            'password' => $password
        ]);

        if (!$response['success']) {
            return [
                'success' => false,
                'error' => $response['data']['error'] ?? 'Login failed'
            ];
        }

        return [
            'success' => true,
            'token' => $response['data']['access_token'] ?? null,
            'user' => $response['data']['user'] ?? null
        ];
    }

    /**
     * Logout user
     */
    public static function logout($token)
    {
        $response = self::$api->post('/user/logout', [], $token);

        if (!$response['success']) {
            return [
                'success' => false,
                'error' => $response['data']['error'] ?? 'Logout failed'
            ];
        }

        return [
            'success' => true,
            'message' => $response['data']['message'] ?? 'Logout successful'
        ];
    }

    /**
     * Get user orders
     */
    public static function getOrders($token)
    {
        $response = self::$api->get('/user/orders', $token);

        if (!$response['success']) {
            return null;
        }

        return $response['data'];
    }

    /**
     * Update user profile
     */
    public static function updateProfile($token, $data)
    {
        $response = self::$api->post('/user/profile/update', $data, $token);

        if (!$response['success']) {
            return [
                'success' => false,
                'error' => $response['data']['error'] ?? 'Update failed'
            ];
        }

        return [
            'success' => true,
            'message' => $response['data']['message'] ?? 'Profile updated'
        ];
    }
}
