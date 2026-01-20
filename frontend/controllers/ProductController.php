<?php

class ProductController
{
    public function index($slug)
    {
        if (!$slug) {
            return ['error' => 'Product not found'];
        }

        // Look up product by slug
        $product = Product::findBySlug($slug);

        if (!$product) {
            return ['error' => 'Product not found'];
        }

        // Product::findBySlug returns the API's 'data' which contains {data: {...}, colors: [...], sizes: [...]}
        return [
            'success' => true,
            'product' => $product['data']['data'] ?? null,
            'colors' => $product['data']['data']['colors'] ?? [],
            'sizes' => $product['data']['data']['sizes'] ?? []
        ];
    }
}
