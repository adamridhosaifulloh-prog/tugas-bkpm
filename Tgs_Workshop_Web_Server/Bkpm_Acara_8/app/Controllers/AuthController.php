<?php

class AuthController
{
    public function loginForm(): void
    {
        // Kalau sudah login, tidak perlu lihat form login lagi
        if (!empty($_SESSION['logged_in'])) {
            redirect('/mahasiswa');
        }

        require __DIR__ . '/../Views/auth/login.php';
    }

    public function login(): void
    {
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';

        // Login sementara menggunakan data tetap (hardcode)
        if ($username === 'admin' && $password === '12345') {
            session_regenerate_id(true);
            $_SESSION['logged_in'] = true;
            $_SESSION['user_name'] = 'Admin';

            redirect('/mahasiswa', 'Selamat datang, Admin.');
        }

        redirect('/login', 'Username atau password salah.', 'danger');
    }

    public function logout(): void
    {
        unset($_SESSION['logged_in'], $_SESSION['user_name']);

        redirect('/login', 'Anda telah logout.', 'info');
    }
}
