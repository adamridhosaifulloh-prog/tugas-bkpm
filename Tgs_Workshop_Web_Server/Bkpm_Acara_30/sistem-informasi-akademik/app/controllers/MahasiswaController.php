<?php

class MahasiswaController extends BaseController
{
    private Mahasiswa $model;

    public function __construct()
    {
        $this->model = new Mahasiswa();
    }

    // GET /mahasiswa dan GET /mahasiswa?id=1
    public function index(): void
    {
        $id = $_GET['id'] ?? null;

        if ($id !== null && $id !== '') {
            $id = $this->requireId($id);
            $data = $this->model->find($id);

            if (!$data) {
                $this->fail(404, 'Data tidak ditemukan');
            }

            $this->json(200, [
                'success' => true,
                'message' => 'Data berhasil ditemukan',
                'data' => $data
            ]);
        }

        $this->json(200, [
            'success' => true,
            'message' => 'Data berhasil diambil',
            'data' => $this->model->getAll()
        ]);
    }

    // POST /mahasiswa (JSON atau multipart/form-data dengan berkas foto)
    public function store(): void
    {
        Auth::requireAdmin();
        $data = $this->validate($this->input());

        $foto = $this->prosesUpload();
        if ($foto !== null) {
            $data['foto'] = $foto;
        }

        try {
            $id = (int) $this->model->create($data);
        } catch (DomainException $e) {
            $this->hapusFoto($foto); // batalkan berkas bila data gagal disimpan
            $this->fail(409, $e->getMessage());
        }

        $this->json(201, [
            'success' => true,
            'message' => 'Data berhasil ditambahkan',
            'data' => ['id' => $id] + $data
        ]);
    }

    // PUT /mahasiswa?id=1  atau  POST /mahasiswa?id=1&_method=PUT (bila membawa foto)
    public function update($id): void
    {
        Auth::requireAdmin();
        $id = $this->requireId($id);

        $lama = $this->model->find($id);
        if (!$lama) {
            $this->fail(404, 'Data tidak ditemukan');
        }

        $data = $this->validate($this->input());

        $fotoBaru = $this->prosesUpload();
        if ($fotoBaru !== null) {
            $data['foto'] = $fotoBaru;
        }

        try {
            $this->model->update($id, $data);
        } catch (DomainException $e) {
            $this->hapusFoto($fotoBaru); // data gagal diperbarui, berkas baru dibuang
            $this->fail(409, $e->getMessage());
        }

        // Tugas Praktik Mandiri 1: hapus foto lama dari server setelah
        // data berhasil diperbarui dengan foto baru.
        if ($fotoBaru !== null) {
            $this->hapusFoto($lama['foto'] ?? null);
        }

        $this->json(200, [
            'success' => true,
            'message' => 'Data berhasil diperbarui',
            'data' => ['id' => $id] + $data
        ]);
    }

    // DELETE /mahasiswa?id=1
    public function destroy($id): void
    {
        Auth::requireAdmin();
        $id = $this->requireId($id);

        $lama = $this->model->find($id);
        if (!$lama) {
            $this->fail(404, 'Data tidak ditemukan');
        }

        $this->model->delete($id);
        $this->hapusFoto($lama['foto'] ?? null); // jangan tinggalkan berkas yatim

        $this->json(200, [
            'success' => true,
            'message' => 'Data berhasil dihapus',
            'data' => null
        ]);
    }

    // Acara 26: validasi dan simpan foto. Mengembalikan nama berkas baru,
    // atau null bila tidak ada berkas yang diunggah.
    private function prosesUpload(): ?string
    {
        if (empty($_FILES['foto']) || $_FILES['foto']['error'] === UPLOAD_ERR_NO_FILE) {
            return null;
        }

        $file = $_FILES['foto'];

        if ($file['error'] === UPLOAD_ERR_INI_SIZE || $file['error'] === UPLOAD_ERR_FORM_SIZE) {
            $this->fail(422, 'Ukuran berkas maksimal 2MB');
        }
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $this->fail(400, 'Upload berkas gagal');
        }

        $ekstensi = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

        // Ekstensi dan signature berkas (exif_imagetype) sama-sama diperiksa.
        if (!in_array($ekstensi, ['jpg', 'jpeg', 'png'], true)
            || !in_array(@exif_imagetype($file['tmp_name']), [IMAGETYPE_JPEG, IMAGETYPE_PNG], true)) {
            $this->fail(422, 'Berkas harus berupa gambar JPG/PNG yang valid');
        }
        if ($file['size'] > 2 * 1024 * 1024) {
            $this->fail(422, 'Ukuran berkas maksimal 2MB');
        }

        $folder = __DIR__ . '/../../public/uploads/';
        if (!is_dir($folder)) {
            mkdir($folder, 0755, true);
        }

        $namaBaru = uniqid('foto_', true) . '.' . $ekstensi;
        if (!move_uploaded_file($file['tmp_name'], $folder . $namaBaru)) {
            $this->fail(500, 'Terjadi kesalahan pada server');
        }

        return $namaBaru;
    }

    // Menghapus berkas foto dari public/uploads. basename() mencegah path traversal.
    private function hapusFoto(?string $nama): void
    {
        if ($nama === null || $nama === '') {
            return;
        }
        $path = __DIR__ . '/../../public/uploads/' . basename($nama);
        if (is_file($path)) {
            unlink($path);
        }
    }

    // Validasi data dan errors per field (Acara 18)
    private function validate(array $in): array
    {
        $nim = $this->text($in['nim'] ?? '');
        $nama = $this->text($in['nama'] ?? '');
        $prodi = $this->text($in['prodi'] ?? '');

        $errors = [];

        if ($nim === '') {
            $errors['nim'][] = 'NIM wajib diisi';
        } elseif (!preg_match('/^\d{9}$/', $nim)) {
            $errors['nim'][] = 'NIM harus terdiri dari tepat 9 digit angka';
        }

        if ($nama === '') {
            $errors['nama'][] = 'Nama wajib diisi';
        } elseif (strlen($nama) > 100) {
            $errors['nama'][] = 'Nama maksimal 100 karakter';
        }

        if ($prodi === '') {
            $errors['prodi'][] = 'Prodi wajib diisi';
        } elseif (strlen($prodi) > 50) {
            $errors['prodi'][] = 'Prodi maksimal 50 karakter';
        }

        if (!empty($errors)) {
            $this->fail(
                422,
                'Data yang dikirim tidak valid',
                $errors
            );
        }

        return [
            'nim' => $nim,
            'nama' => $nama,
            'prodi' => $prodi
        ];
    }
}
