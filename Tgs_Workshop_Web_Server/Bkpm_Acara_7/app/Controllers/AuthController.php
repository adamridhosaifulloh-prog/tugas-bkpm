<?php

class AuthController
{
    public function loginForm()
    {
        require_once __DIR__ . '/../Views/auth/login.php';
    }

    public function login()
    {
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';

        // Login sementara menggunakan hardcode
        if ($username === 'admin' && $password === '12345') {

            $_SESSION['user'] = [
                'username' => 'admin',
                'nama' => 'Admin'
            ];

            // Flash message
            $_SESSION['flash'] = 'Selamat datang, Admin';

            header('Location: ' . BASE_URL . '/dashboard');
            exit;
        }

        $_SESSION['flash_error'] = 'Username atau password salah';

        header('Location: ' . BASE_URL . '/login');
        exit;
    }

    public function logout()
    {
        session_unset();
        session_destroy();

        session_start();

        // Flash message setelah logout
        $_SESSION['flash'] = 'Anda telah logout';

        header('Location: ' . BASE_URL . '/login');
        exit;
    }
}