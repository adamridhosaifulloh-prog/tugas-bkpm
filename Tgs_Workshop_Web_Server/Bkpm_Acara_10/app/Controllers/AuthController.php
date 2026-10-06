<?php

require_once __DIR__ . '/BaseController.php';

class AuthController extends BaseController
{
    public function loginForm(): void
    {
        // Kalau sudah login, tidak perlu lihat form login lagi
        if (!empty($_SESSION['logged_in'])) {
            $this->redirect('/mahasiswa');
        }

        $this->view('auth/login');
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

            $this->redirect('/mahasiswa', 'Selamat datang, Admin.');
        }

        $this->redirect('/login', 'Username atau password salah.', 'danger');
    }

    public function logout(): void
    {
        unset($_SESSION['logged_in'], $_SESSION['user_name']);

        $this->redirect('/login', 'Anda telah logout.', 'info');
    }
}
