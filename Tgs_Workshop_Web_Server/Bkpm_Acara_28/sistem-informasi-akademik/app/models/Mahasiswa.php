<?php
class Mahasiswa {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function getAll(): array {
        return $this->db->query('SELECT * FROM mahasiswa ORDER BY id DESC')->fetchAll();
    }

    public function find(int $id) {
        $stmt = $this->db->prepare('SELECT * FROM mahasiswa WHERE id = :id');
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    // Tugas Praktik Mandiri 2: validasi NIM tidak boleh duplikat
    public function nimExists(string $nim, ?int $exceptId = null): bool {
        $sql = 'SELECT COUNT(*) FROM mahasiswa WHERE nim = :nim';
        $params = [':nim' => $nim];
        if ($exceptId !== null) {
            $sql .= ' AND id <> :id';
            $params[':id'] = $exceptId;
        }
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int) $stmt->fetchColumn() > 0;
    }

    public function create(array $data): string {
        if ($this->nimExists($data['nim'])) {
            throw new DomainException('NIM ' . $data['nim'] . ' sudah terdaftar');
        }
        try {
            $stmt = $this->db->prepare(
                'INSERT INTO mahasiswa (nim, nama, prodi, foto) VALUES (:nim, :nama, :prodi, :foto)'
            );
            $stmt->execute([
                ':nim'   => $data['nim'],
                ':nama'  => $data['nama'],
                ':prodi' => $data['prodi'],
                ':foto'  => $data['foto'] ?? null,
            ]);
            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            Response::json(500, false, 'Terjadi kesalahan pada server');
        }
    }

    public function update(int $id, array $data): void {
        if ($this->nimExists($data['nim'], $id)) {
            throw new DomainException('NIM ' . $data['nim'] . ' sudah dipakai mahasiswa lain');
        }
        // foto hanya diubah bila ada foto baru; jika tidak, foto lama dipertahankan.
        $sql = 'UPDATE mahasiswa SET nim = :nim, nama = :nama, prodi = :prodi';
        $params = [
            ':nim'   => $data['nim'],
            ':nama'  => $data['nama'],
            ':prodi' => $data['prodi'],
            ':id'    => $id,
        ];
        if (!empty($data['foto'])) {
            $sql .= ', foto = :foto';
            $params[':foto'] = $data['foto'];
        }
        $stmt = $this->db->prepare($sql . ' WHERE id = :id');
        $stmt->execute($params);
    }

    public function delete(int $id): void {
        $stmt = $this->db->prepare('DELETE FROM mahasiswa WHERE id = :id');
        $stmt->execute([':id' => $id]);
    }
}
