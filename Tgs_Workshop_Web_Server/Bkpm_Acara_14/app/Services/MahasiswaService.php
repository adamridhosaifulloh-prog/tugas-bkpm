<?php

require_once __DIR__ . '/../Logger.php';
require_once __DIR__ . '/../Repositories/MahasiswaRepository.php';
require_once __DIR__ . '/../Repositories/ProdiRepository.php';

// Service Layer: tempat logika bisnis mahasiswa.
//   Controller  -> mengatur alur (terima request, tentukan response)
//   Service     -> validasi + aturan bisnis + exception handling + logging (class ini)
//   Repository  -> menjalankan query ke database (prepared statement)
class MahasiswaService
{
    // Kode error MySQL untuk "Duplicate entry"
    private const MYSQL_DUPLICATE = 1062;

    // Constructor injection: kedua Repository diberikan dari luar (lihat public/index.php)
    public function __construct(
        private MahasiswaRepository $repo,
        private ProdiRepository $prodiRepo
    ) {}

    // ---------- Proses baca data (diteruskan ke Repository) ----------
    // Exception database tidak ditangkap di sini; Controller yang menangkapnya
    // supaya bisa menentukan tampilan yang aman bagi pengguna.

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
    //   errors (array per field), data (objek Mahasiswa untuk mengisi ulang form;
    //   null jika tidak ada form yang perlu ditampilkan, mis. data tidak ditemukan)

    public function create(array $input): array
    {
        $m = null;

        try {
            [$m, $errors] = $this->validate($input);

            if (!empty($errors)) {
                return $this->gagal($m, $errors);
            }

            $this->repo->create($m);
        } catch (PDOException $e) {
            return $this->gagalDatabase($e, $m ?? new Mahasiswa(), 'menambah');
        }

        return $this->sukses($m, 'Data mahasiswa berhasil ditambahkan.');
    }

    public function update(int $id, array $input): array
    {
        $m = null;

        try {
            if ($this->repo->find($id) === null) {
                return $this->tidakDitemukan();
            }

            [$m, $errors] = $this->validate($input, $id);

            if (!empty($errors)) {
                return $this->gagal($m, $errors);
            }

            $this->repo->update($m);
        } catch (PDOException $e) {
            $kosong = new Mahasiswa();
            $kosong->setId($id);

            return $this->gagalDatabase($e, $m ?? $kosong, 'mengubah');
        }

        return $this->sukses($m, 'Data mahasiswa berhasil diubah.');
    }

    public function delete(int $id): array
    {
        try {
            if ($this->repo->find($id) === null) {
                return $this->tidakDitemukan();
            }

            $this->repo->delete($id);
        } catch (PDOException $e) {
            Logger::error("Gagal menghapus mahasiswa id=$id", $e);

            return ['success' => false, 'message' => 'Data gagal dihapus.', 'errors' => [], 'data' => null];
        }

        return ['success' => true, 'message' => 'Data mahasiswa berhasil dihapus.', 'errors' => [], 'data' => null];
    }

    // ---------- Validasi ----------

    // Mengembalikan [objek Mahasiswa, array error].
    // $id = 0 untuk data baru, > 0 untuk data yang sedang diubah.
    private function validate(array $input, int $id = 0): array
    {
        $m = new Mahasiswa();
        $m->setId($id);
        $errors = [];

        // 1. Aturan format: dicek lewat setter pada class Mahasiswa
        $fields = [
            'nim'      => ['setNim',      (string) ($input['nim'] ?? '')],
            'nama'     => ['setNama',     (string) ($input['nama'] ?? '')],
            'email'    => ['setEmail',    (string) ($input['email'] ?? '')],
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

    // ---------- Penyusun hasil ----------

    private function sukses(Mahasiswa $m, string $message): array
    {
        return ['success' => true, 'message' => $message, 'errors' => [], 'data' => $m];
    }

    private function tidakDitemukan(): array
    {
        return ['success' => false, 'message' => 'Data mahasiswa tidak ditemukan.', 'errors' => [], 'data' => null];
    }

    // Hasil gagal. Pesan flash dibedakan agar pengguna tahu penyebabnya.
    private function gagal(Mahasiswa $m, array $errors): array
    {
        $message = 'Data gagal disimpan. Periksa kembali isian form.';

        if (($errors['nim'] ?? '') === 'NIM sudah terdaftar.') {
            $message = 'NIM sudah terdaftar.';
        }

        return ['success' => false, 'message' => $message, 'errors' => $errors, 'data' => $m];
    }

    // Exception database: detail teknis HANYA ke app.log, pengguna mendapat pesan aman.
    private function gagalDatabase(PDOException $e, Mahasiswa $m, string $aksi): array
    {
        Logger::error("Gagal $aksi data mahasiswa", $e);

        // NIM duplikat yang lolos pengecekan existsByNim (mis. dua request bersamaan)
        if (($e->errorInfo[1] ?? 0) === self::MYSQL_DUPLICATE) {
            return $this->gagal($m, ['nim' => 'NIM sudah terdaftar.']);
        }

        return ['success' => false, 'message' => 'Data gagal disimpan.', 'errors' => [], 'data' => $m];
    }
}
