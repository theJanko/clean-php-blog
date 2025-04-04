<?php

namespace App\Middleware;

final class AuthMiddleware
{
    public function handle(): void
    {
        if (!isset($_SESSION['user_logged']) || $_SESSION['user_logged'] !== true) {
            header('Location: /login');
            exit;
        }
    }
}
