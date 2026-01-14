<?php

class Product
{
    private static $api;

    public static function setApi($api)
    {
        self::$api = $api;
    }

    /**
     * Get all products with filters
     */
    public static function getAll()
    {
        $response = self::$api->get('/products');

        if (!$response['success']) {
            return null;
        }

        return $response['data'];
    }

    /**
     * Get single product by ID
     */
    public static function findById($id)
    {
        $response = self::$api->get('/product/' . intval($id) . '/show');

        if (!$response['success']) {
            return null;
        }

        return $response['data'];
    }

    /**
     * Get single product by slug
     */
    public static function findBySlug($slug)
    {
        $response = self::$api->get('/product/' . urlencode($slug) . '/slug');

        if (!$response['success']) {
            return null;
        }

        return $response['data'];
    }

    /**
     * Filter products by color
     */
    public static function filterByColor($colorId)
    {
        $response = self::$api->get('/products/' . intval($colorId) . '/color');

        if (!$response['success']) {
            return null;
        }

        return $response['data'];
    }

    /**
     * Filter products by size
     */
    public static function filterBySize($sizeId)
    {
        $response = self::$api->get('/products/' . intval($sizeId) . '/size');

        if (!$response['success']) {
            return null;
        }

        return $response['data'];
    }

    /**
     * Search products by term
     */
    public static function search($term)
    {
        $response = self::$api->get('/products/' . urlencode($term) . '/find');

        if (!$response['success']) {
            return null;
        }

        return $response['data'];
    }
}
