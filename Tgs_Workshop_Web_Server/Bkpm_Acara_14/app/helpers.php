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

// ---------- Flash Message ----------
// Simpan pesan sementara di session. $type = kelas Bootstrap: success / danger / info
function setFlash(string $type, string $message): void
{
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

// Ambil pesan lalu langsung dihapus dari session, jadi hanya tampil satu kali
function getFlash(): ?array
{
    $flash = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);

    return $flash;
}

// Pindah halaman, pesan (opsional) disimpan sebagai flash message
function redirect(string $path, string $message = '', string $type = 'success'): void
{
    if ($message !== '') {
        setFlash($type, $message);
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
