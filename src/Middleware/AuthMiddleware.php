<?php

namespace App\Middleware;

class AuthMiddleware
{
    public function handle()
    {
        if (!isset($_SESSION['user_logged']) || $_SESSION['user_logged'] !== true) {
            header('Location: /login');
            exit;
        }
    }
}
