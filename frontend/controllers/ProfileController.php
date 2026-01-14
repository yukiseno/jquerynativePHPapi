<?php

class ProfileController
{
    public function index()
    {
        // Profile page requires authentication
        if (!isLoggedIn()) {
            redirect('/login');
        }

        $user = getCurrentUser();
        $data = [
            'user' => $user
        ];

        $page = 'profile';
        require __DIR__ . '/../views/layout.php';
    }
}
