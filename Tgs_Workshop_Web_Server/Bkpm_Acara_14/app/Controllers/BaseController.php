<?php

// Induk semua Controller (Inheritance).
// Method yang dipakai bersama (view & redirect) ditulis sekali di sini,
// lalu diwarisi oleh controller lain lewat "extends BaseController".
abstract class BaseController
{
    // Tampilkan file view. Isi $data menjadi variabel di view,
    // contoh: ['daftar' => $x] -> di view bisa langsung memakai $daftar
    protected function view(string $view, array $data = []): void
    {
        extract($data);
        require __DIR__ . '/../Views/' . $view . '.php';
    }

    // Pindah halaman, pesan (opsional) disimpan sebagai flash message di session
    protected function redirect(string $path, string $message = '', string $type = 'success'): void
    {
        if ($message !== '') {
            setFlash($type, $message);
        }
        header('Location: ' . url($path));
        exit;
    }
}
