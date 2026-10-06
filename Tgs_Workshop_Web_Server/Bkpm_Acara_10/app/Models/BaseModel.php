<?php

require_once __DIR__ . '/../Database.php';

// Induk semua class yang butuh akses database (Inheritance).
// Koneksi PDO diambil dari objek Database yang disuntikkan lewat constructor,
// sehingga class turunan cukup memakai $this->pdo.
abstract class BaseModel
{
    protected PDO $pdo;

    public function __construct(Database $db)
    {
        $this->pdo = $db->getConnection();
    }
}
