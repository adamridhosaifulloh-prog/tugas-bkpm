<?php

// Model dasar: menyediakan koneksi database (PDO) untuk semua Model turunan.
// Class turunan (mis. MahasiswaModel) cukup memakai $this->db.
abstract class Model
{
    protected PDO $db;

    public function __construct()
    {
        $config = require __DIR__ . '/../../config/database.php';

        $dsn = "mysql:host={$config['host']};dbname={$config['dbname']};charset={$config['charset']}";

        $this->db = new PDO($dsn, $config['username'], $config['password'], [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,   // error jadi exception
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,         // hasil berupa array asosiatif
        ]);
    }
}
