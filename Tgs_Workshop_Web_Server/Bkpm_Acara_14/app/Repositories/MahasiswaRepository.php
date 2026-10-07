<?php

require_once __DIR__ . '/../Models/BaseModel.php';
require_once __DIR__ . '/../Models/Mahasiswa.php';

// Repository Pattern: seluruh query SQL mahasiswa ada di class ini.
// Mewarisi BaseModel, jadi $this->pdo sudah tersedia dari class induk
// (Database disuntikkan lewat constructor BaseModel).
class MahasiswaRepository extends BaseModel
{
    // Semua mahasiswa + nama prodi (JOIN). Jika $keyword diisi, cari nama atau NIM dengan LIKE
    public function all(string $keyword = ''): array
    {
        $sql = "SELECT m.*, p.nama AS prodi_nama
                FROM mahasiswa m
                JOIN prodi p ON m.prodi_id = p.id";
        $params = [];

        if ($keyword !== '') {
            $sql .= " WHERE m.nama LIKE :nama OR m.nim LIKE :nim";
            $params['nama'] = '%' . $keyword . '%';
            $params['nim']  = '%' . $keyword . '%';
        }

        $sql .= " ORDER BY m.nim";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        $hasil = [];
        foreach ($stmt->fetchAll() as $row) {
            $hasil[] = Mahasiswa::dariBaris($row);
        }
        return $hasil;
    }

    public function find(int $id): ?Mahasiswa
    {
        $stmt = $this->pdo->prepare("SELECT * FROM mahasiswa WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();

        return $row ? Mahasiswa::dariBaris($row) : null;
    }

    public function create(Mahasiswa $m): void
    {
        $stmt = $this->pdo->prepare(
            "INSERT INTO mahasiswa (nim, nama, email, prodi_id, angkatan)
             VALUES (:nim, :nama, :email, :prodi_id, :angkatan)"
        );
        $stmt->execute([
            'nim'      => $m->getNim(),
            'nama'     => $m->getNama(),
            'email'    => $m->getEmail(),
            'prodi_id' => $m->getProdiId(),
            'angkatan' => $m->getAngkatan(),
        ]);
    }

    public function update(Mahasiswa $m): void
    {
        $stmt = $this->pdo->prepare(
            "UPDATE mahasiswa
             SET nim = :nim, nama = :nama, email = :email, prodi_id = :prodi_id, angkatan = :angkatan
             WHERE id = :id"
        );
        $stmt->execute([
            'nim'      => $m->getNim(),
            'nama'     => $m->getNama(),
            'email'    => $m->getEmail(),
            'prodi_id' => $m->getProdiId(),
            'angkatan' => $m->getAngkatan(),
            'id'       => $m->getId(),
        ]);
    }

    public function delete(int $id): void
    {
        $stmt = $this->pdo->prepare("DELETE FROM mahasiswa WHERE id = :id");
        $stmt->execute(['id' => $id]);
    }

    // Cek apakah NIM sudah dipakai mahasiswa lain.
    // $exceptId diisi saat edit, supaya NIM milik data itu sendiri tidak dianggap duplikat.
    public function existsByNim(string $nim, int $exceptId = 0): bool
    {
        $stmt = $this->pdo->prepare("SELECT 1 FROM mahasiswa WHERE nim = :nim AND id <> :id");
        $stmt->execute(['nim' => $nim, 'id' => $exceptId]);

        return (bool) $stmt->fetchColumn();
    }
}
