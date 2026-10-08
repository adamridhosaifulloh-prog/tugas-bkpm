<?php

// Escape output agar aman dari XSS
function e($value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

// Buat URL dari path, contoh: url('/mahasiswa')
function url(string $path = ''): string
{
    return BASE_PATH . $path;
}

// Pindah halaman, pesan (opsional) disimpan di session
function redirect(string $path, string $message = '', string $type = 'success'): void
{
    if ($message !== '') {
        $_SESSION['alert'] = ['type' => $type, 'message' => $message];
    }
    header('Location: ' . url($path));
    exit;
}

// Halaman yang butuh login: kalau belum login, arahkan ke /login
function cekLogin(): void
{
    if (empty($_SESSION['logged_in'])) {
        redirect('/login');
    }
}
