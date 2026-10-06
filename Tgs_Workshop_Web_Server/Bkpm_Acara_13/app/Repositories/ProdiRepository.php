<?php

require_once __DIR__ . '/../Models/BaseModel.php';

// Repository khusus tabel prodi.
// Sebelumnya daftarProdi() ada di MahasiswaRepository; sekarang dipisah
// supaya setiap Repository hanya mengurus satu tabel.
class ProdiRepository extends BaseModel
{
    // Daftar prodi untuk pilihan di form
    public function all(): array
    {
        return $this->pdo->query("SELECT id, kode, nama FROM prodi ORDER BY kode")->fetchAll();
    }

    // Dipakai Service untuk memastikan prodi yang dipilih benar-benar ada
    public function exists(int $id): bool
    {
        $stmt = $this->pdo->prepare("SELECT 1 FROM prodi WHERE id = :id");
        $stmt->execute(['id' => $id]);

        return (bool) $stmt->fetchColumn();
    }
}
