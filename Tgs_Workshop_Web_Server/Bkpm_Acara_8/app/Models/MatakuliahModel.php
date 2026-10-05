<?php

require_once __DIR__ . '/../Database.php';

class MatakuliahModel
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    // Daftar mata kuliah + nama prodi (JOIN)
    public function all(): array
    {
        return $this->db->query(
            "SELECT mk.*, p.nama AS prodi_nama
             FROM matakuliah mk
             JOIN prodi p ON mk.prodi_id = p.id
             ORDER BY mk.kode"
        )->fetchAll();
    }

    public function find(int $id)
    {
        $stmt = $this->db->prepare("SELECT * FROM matakuliah WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    public function create(array $d): void
    {
        $stmt = $this->db->prepare(
            "INSERT INTO matakuliah (kode, nama, sks, prodi_id)
             VALUES (:kode, :nama, :sks, :prodi_id)"
        );
        $stmt->execute($d);
    }

    public function update(int $id, array $d): void
    {
        $d['id'] = $id;
        $stmt = $this->db->prepare(
            "UPDATE matakuliah
             SET kode = :kode, nama = :nama, sks = :sks, prodi_id = :prodi_id
             WHERE id = :id"
        );
        $stmt->execute($d);
    }

    public function delete(int $id): void
    {
        $stmt = $this->db->prepare("DELETE FROM matakuliah WHERE id = :id");
        $stmt->execute(['id' => $id]);
    }
}
