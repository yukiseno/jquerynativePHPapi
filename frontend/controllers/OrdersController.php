<?php

class OrdersController
{
    public function index()
    {
        // Orders page requires authentication
        if (!isLoggedIn()) {
            redirect('/login');
        }

        $user = getCurrentUser();
        $data = [
            'user' => $user
        ];

        $page = 'orders';
        require __DIR__ . '/../views/layout.php';
    }
}
