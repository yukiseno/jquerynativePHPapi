<?php

class TwoFactorController
{
    private $user;

    public function __construct()
    {
        $this->user = new User();
    }

    public function index($param)
    {
        $action = $param ?? 'setup';

        if ($action === 'setup') {
            return $this->setup();
        } elseif ($action === 'verify') {
            return $this->verify();
        }

        return ['error' => '2FA action not found'];
    }

    private function setup()
    {
        // Check if user is logged in
        if (!isLoggedIn()) {
            redirect('/login');
        }

        $user = getCurrentUser();
        $has2FA = isset($user['two_factor_enabled']) && $user['two_factor_enabled'];

        return [
            'user' => $user,
            'has2FA' => $has2FA
        ];
    }

    private function verify()
    {
        if (!isLoggedIn()) {
            redirect('/login');
        }

        return [];
    }
}
