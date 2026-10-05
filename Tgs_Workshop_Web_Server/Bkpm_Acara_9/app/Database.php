<?php

// Class yang bertugas mengelola koneksi ke database (PDO).
// Objek Database ini nanti "disuntikkan" ke Repository lewat constructor.
class Database
{
    private ?PDO $pdo = null;

    // Koneksi baru dibuat saat pertama kali dibutuhkan
    public function getConnection(): PDO
    {
        if ($this->pdo === null) {
            $config = require __DIR__ . '/../config/database.php';

            $dsn = "mysql:host={$config['host']};dbname={$config['dbname']};charset={$config['charset']}";

            $this->pdo = new PDO($dsn, $config['username'], $config['password'], [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
        }

        return $this->pdo;
    }
}
