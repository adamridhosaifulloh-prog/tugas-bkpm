<?php

require_once __DIR__ . '/../Repositories/MahasiswaRepository.php';
require_once __DIR__ . '/../Repositories/ProdiRepository.php';

// Service Layer: tempat logika bisnis mahasiswa.
//   Controller  -> mengatur alur (terima request, tentukan response)
//   Service     -> validasi + aturan bisnis (class ini)
//   Repository  -> menjalankan query ke database
class MahasiswaService
{
    // Constructor injection: kedua Repository diberikan dari luar (lihat public/index.php)
    public function __construct(
        private MahasiswaRepository $repo,
        private ProdiRepository $prodiRepo
    ) {}

    // ---------- Proses baca data (diteruskan ke Repository) ----------

    public function all(string $keyword = ''): array
    {
        return $this->repo->all($keyword);
    }

    public function find(int $id): ?Mahasiswa
    {
        return $this->repo->find($id);
    }

    public function daftarProdi(): array
    {
        return $this->prodiRepo->all();
    }

    // ---------- Proses tulis data ----------
    // Semua method di bawah mengembalikan array:
    //   success (bool), message (string, untuk flash message),
    //   errors (array per field), data (objek Mahasiswa, dipakai untuk mengisi ulang form)

    public function create(array $input): array
    {
        [$m, $errors] = $this->validate($input);

        if (!empty($errors)) {
            return $this->gagal($m, $errors);
        }

        try {
            $this->repo->create($m);
        } catch (PDOException $e) {
            return $this->gagal($m, $this->errorDatabase($e));
        }

        return [
            'success' => true,
            'message' => 'Data mahasiswa berhasil ditambahkan.',
            'errors'  => [],
            'data'    => $m,
        ];
    }

    public function update(int $id, array $input): array
    {
        [$m, $errors] = $this->validate($input, $id);

        if (!empty($errors)) {
            return $this->gagal($m, $errors);
        }

        try {
            $this->repo->update($m);
        } catch (PDOException $e) {
            return $this->gagal($m, $this->errorDatabase($e));
        }

        return [
            'success' => true,
            'message' => 'Data mahasiswa berhasil diubah.',
            'errors'  => [],
            'data'    => $m,
        ];
    }

    public function delete(int $id): array
    {
        if ($this->repo->find($id) === null) {
            return ['success' => false, 'message' => 'Data mahasiswa tidak ditemukan.', 'errors' => [], 'data' => null];
        }

        try {
            $this->repo->delete($id);
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Data gagal dihapus.', 'errors' => [], 'data' => null];
        }

        return ['success' => true, 'message' => 'Data mahasiswa berhasil dihapus.', 'errors' => [], 'data' => null];
    }

    // ---------- Validasi (dipindah dari Controller) ----------

    // Mengembalikan [objek Mahasiswa, array error].
    // $id = 0 untuk data baru, > 0 untuk data yang sedang diubah.
    private function validate(array $input, int $id = 0): array
    {
        $m = new Mahasiswa();
        $m->setId($id);
        $errors = [];

        // 1. Aturan format: dicek lewat setter pada class Mahasiswa
        $fields = [
            'nim'      => ['setNim',      $input['nim'] ?? ''],
            'nama'     => ['setNama',     $input['nama'] ?? ''],
            'email'    => ['setEmail',    $input['email'] ?? ''],
            'prodi_id' => ['setProdiId',  (int) ($input['prodi_id'] ?? 0)],
            'angkatan' => ['setAngkatan', (int) ($input['angkatan'] ?? 0)],
        ];

        foreach ($fields as $field => [$setter, $nilai]) {
            try {
                $m->$setter($nilai);
            } catch (InvalidArgumentException $e) {
                $errors[$field] = $e->getMessage();
            }
        }

        // 2. Aturan bisnis: butuh data dari database, jadi dicek di Service
        if (!isset($errors['nim']) && $this->repo->existsByNim($m->getNim(), $id)) {
            $errors['nim'] = 'NIM sudah terdaftar.';
        }

        if (!isset($errors['prodi_id']) && !$this->prodiRepo->exists($m->getProdiId())) {
            $errors['prodi_id'] = 'Program studi tidak tersedia.';
        }

        return [$m, $errors];
    }

    // Susun hasil gagal. Pesan flash dibedakan agar pengguna tahu penyebabnya.
    private function gagal(Mahasiswa $m, array $errors): array
    {
        $message = 'Data gagal disimpan. Periksa kembali isian form.';

        if (isset($errors['nim']) && $errors['nim'] === 'NIM sudah terdaftar.') {
            $message = 'Data gagal disimpan. NIM sudah terdaftar.';
        }

        return ['success' => false, 'message' => $message, 'errors' => $errors, 'data' => $m];
    }

    // Jaring pengaman: NIM duplikat yang lolos pengecekan (kolom UNIQUE) -> error 23000
    private function errorDatabase(PDOException $e): array
    {
        if ($e->getCode() === '23000') {
            return ['nim' => 'NIM sudah terdaftar.'];
        }
        throw $e;
    }
}
