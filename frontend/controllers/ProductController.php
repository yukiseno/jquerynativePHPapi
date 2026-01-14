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

        return [
            'success' => true,
            'product' => $product['data'] ?? null,
            'colors' => $product['data']['colors'] ?? [],
            'sizes' => $product['data']['sizes'] ?? []
        ];
    }
}
