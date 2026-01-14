<?php

class Coupon
{
    private static $api;

    public static function setApi($api)
    {
        self::$api = $api;
    }

    /**
     * Apply coupon code
     */
    public static function apply($code)
    {
        $response = self::$api->post('/apply/coupon', [
            'coupon_code' => $code
        ]);

        if (!$response['success']) {
            return [
                'success' => false,
                'error' => $response['data']['error'] ?? 'Invalid coupon'
            ];
        }

        return [
            'success' => true,
            'data' => $response['data']['data'] ?? null
        ];
    }
}
