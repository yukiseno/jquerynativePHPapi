<?php

class LoginController
{
    public function index()
    {
        $page = 'login';
        require __DIR__ . '/../views/layout.php';
    }
}
