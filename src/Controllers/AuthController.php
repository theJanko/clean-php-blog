<?php

namespace App\Controllers;

class AuthController extends BaseController
{
    public function loginPage(): string
    {
        return $this->render('auth/login.twig', [
            'error' => $_SESSION['login_error'] ?? null
        ]);
    }

    public function login(): void
    {
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';

        if ($this->authenticate($username, $password)) {
            $_SESSION['user_logged'] = true;
            $_SESSION['username'] = 'admin';
            $this->redirect('/admin');
        }

        $_SESSION['login_error'] = 'Wrong Login Data!';
        $this->redirect('/login');
    }

    public function logout(): void
    {
        session_destroy();
        $this->redirect('/login');
    }

    private function authenticate(string $username, string $password): bool
    {
        return $username === 'admin' && $password === 'test';
    }
}
