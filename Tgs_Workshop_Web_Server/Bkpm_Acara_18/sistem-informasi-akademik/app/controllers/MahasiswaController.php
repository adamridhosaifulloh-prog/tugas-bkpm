<?php
class MahasiswaController extends BaseController {
    private Mahasiswa $model;

    public function __construct() {
        $this->model = new Mahasiswa();
    }

    // GET /mahasiswa dan GET /mahasiswa?id=1
    public function index(): void {
        $id = $_GET['id'] ?? null;

        if ($id !== null && $id !== '') {
            $id = $this->requireId($id);
            $data = $this->model->find($id);
            if (!$data) {
                $this->fail(404, 'Data tidak ditemukan');
            }
            $this->json(200, ['success' => true, 'message' => 'Data berhasil ditemukan', 'data' => $data]);
        }

        $this->json(200, ['success' => true, 'message' => 'Data berhasil diambil', 'data' => $this->model->getAll()]);
    }

    // POST /mahasiswa
    public function store(): void {
        $data = $this->validate($this->input());

        try {
            $id = (int) $this->model->create($data);
        } catch (DomainException $e) {
            $this->fail(409, $e->getMessage());
        }

        $this->json(201, [
            'success' => true,
            'message' => 'Data berhasil ditambahkan',
            'data'    => ['id' => $id] + $data,
        ]);
    }

    // PUT /mahasiswa?id=1
    public function update($id): void {
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
            'data'    => ['id' => $id] + $data,
        ]);
    }

    // DELETE /mahasiswa?id=1
    public function destroy($id): void {
        $id = $this->requireId($id);
        if (!$this->model->find($id)) {
            $this->fail(404, 'Data tidak ditemukan');
        }
        $this->model->delete($id);
        $this->json(200, ['success' => true, 'message' => 'Data berhasil dihapus', 'data' => null]);
    }

    private function validate(array $in): array {
        $nim   = $this->text($in['nim'] ?? '');
        $nama  = $this->text($in['nama'] ?? '');
        $prodi = $this->text($in['prodi'] ?? '');

        if ($nim === '' || $nama === '' || $prodi === '') {
            $this->fail(400, 'Field nim, nama, dan prodi wajib diisi');
        }
        if (strlen($nim) > 15)    $this->fail(400, 'NIM maksimal 15 karakter');
        if (strlen($nama) > 100)  $this->fail(400, 'Nama maksimal 100 karakter');
        if (strlen($prodi) > 50)  $this->fail(400, 'Prodi maksimal 50 karakter');

        return ['nim' => $nim, 'nama' => $nama, 'prodi' => $prodi];
    }
}
