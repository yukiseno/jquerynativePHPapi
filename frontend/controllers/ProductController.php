<?php

class ProductController
{
    public function index($slug)
    {
        if (!$slug) {
            return ['error' => 'Product not found'];
        }

        // If slug is numeric, treat as ID, otherwise it's a slug
        if (is_numeric($slug)) {
            $product = Product::findById($slug);
        } else {
            // For now, we'll use ID since the API expects ID
            // In a real app, you'd need a way to look up product by slug
            return ['error' => 'Product lookup by slug not yet implemented in API'];
        }

        if (!$product) {
            return ['error' => 'Product not found'];
        }

        return [
            'success' => true,
            'product' => $product['data'] ?? null,
            'colors' => $product['colors'] ?? [],
            'sizes' => $product['sizes'] ?? []
        ];
    }
}
