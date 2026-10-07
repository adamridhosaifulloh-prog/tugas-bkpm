<?php

require_once __DIR__ . '/Model.php';

// Model untuk tabel mahasiswa.
class MahasiswaModel extends Model
{
    // Ambil semua mahasiswa beserta nama program studinya (JOIN ke tabel prodi)
    public function all(): array
    {
        $sql = "SELECT m.id, m.nim, m.nama, m.email, m.angkatan, m.status,
                       p.nama AS prodi
                FROM mahasiswa m
                JOIN prodi p ON p.id = m.prodi_id
                ORDER BY m.nim";

        return $this->db->query($sql)->fetchAll();
    }
}
