<?php

class CartController
{
    public function index()
    {
        // Cart doesn't require authentication, but show login prompt for checkout
        $page = 'cart';
        require __DIR__ . '/../views/layout.php';
    }
}
