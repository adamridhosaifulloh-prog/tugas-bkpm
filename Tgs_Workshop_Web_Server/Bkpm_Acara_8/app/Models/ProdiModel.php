<?php

require_once __DIR__ . '/../Database.php';

class ProdiModel
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function all(): array
    {
        return $this->db->query("SELECT * FROM prodi ORDER BY kode")->fetchAll();
    }

    public function find(int $id)
    {
        $stmt = $this->db->prepare("SELECT * FROM prodi WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    public function create(string $kode, string $nama): void
    {
        $stmt = $this->db->prepare("INSERT INTO prodi (kode, nama) VALUES (:kode, :nama)");
        $stmt->execute(['kode' => $kode, 'nama' => $nama]);
    }

    public function update(int $id, string $kode, string $nama): void
    {
        $stmt = $this->db->prepare("UPDATE prodi SET kode = :kode, nama = :nama WHERE id = :id");
        $stmt->execute(['kode' => $kode, 'nama' => $nama, 'id' => $id]);
    }

    public function delete(int $id): void
    {
        $stmt = $this->db->prepare("DELETE FROM prodi WHERE id = :id");
        $stmt->execute(['id' => $id]);
    }
}
