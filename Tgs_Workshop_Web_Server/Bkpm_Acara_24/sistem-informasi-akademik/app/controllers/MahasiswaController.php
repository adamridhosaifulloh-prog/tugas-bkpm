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

    // POST /mahasiswa
    public function store(): void
    {
        Auth::requireAuth();
        $data = $this->validate($this->input());

        try {
            $id = (int) $this->model->create($data);
        } catch (DomainException $e) {
            $this->fail(409, $e->getMessage());
        }

        $this->json(201, [
            'success' => true,
            'message' => 'Data berhasil ditambahkan',
            'data' => ['id' => $id] + $data
        ]);
    }

    // PUT /mahasiswa?id=1
    public function update($id): void
    {
        Auth::requireAuth();
        $id = $this->requireId($id);

        if (!$this->model->find($id)) {
            $this->fail(404, 'Data tidak ditemukan');
        }

        $data = $this->validate($this->input());

        try {
            $this->model->update($id, $data);
        } catch (DomainException $e) {
            $this->fail(409, $e->getMessage());
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
        Auth::requireAuth();
        $id = $this->requireId($id);

        if (!$this->model->find($id)) {
            $this->fail(404, 'Data tidak ditemukan');
        }

        $this->model->delete($id);

        $this->json(200, [
            'success' => true,
            'message' => 'Data berhasil dihapus',
            'data' => null
        ]);
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
