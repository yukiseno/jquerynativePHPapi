<?php

class CheckoutController
{
    public function index()
    {
        // Checkout page requires authentication
        if (!isLoggedIn()) {
            redirect('/login');
        }

        $user = getCurrentUser();
        $data = [
            'user' => $user
        ];

        $page = 'checkout';
        require __DIR__ . '/../views/layout.php';
    }
}
