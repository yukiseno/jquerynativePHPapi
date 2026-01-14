<?php

class HomeController
{
    public function index()
    {
        // Fetch all products from API
        $productData = Product::getAll();

        if (!$productData) {
            return ['error' => 'Failed to load products'];
        }

        return [
            'success' => true,
            'products' => [],
            'colors' => $productData['colors'] ?? [],
            'sizes' => $productData['sizes'] ?? []
        ];
    }
}
