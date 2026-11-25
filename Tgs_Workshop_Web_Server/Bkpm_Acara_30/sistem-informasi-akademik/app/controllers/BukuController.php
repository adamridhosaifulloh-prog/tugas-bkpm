<?php
class BukuController extends BaseController {
    private Buku $model;

    public function __construct() {
        $this->model = new Buku();
    }

    // GET /buku dan GET /buku?id=1
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

    // POST /buku
    public function store(): void {
        $data = $this->validate($this->input());
        $id = (int) $this->model->create($data);

        $this->json(201, [
            'success' => true,
            'message' => 'Data berhasil ditambahkan',
            'data'    => ['id' => $id] + $data,
        ]);
    }

    // PUT /buku?id=1
    public function update($id): void {
        $id = $this->requireId($id);
        if (!$this->model->find($id)) {
            $this->fail(404, 'Data tidak ditemukan');
        }
        $data = $this->validate($this->input());
        $this->model->update($id, $data);

        $this->json(200, [
            'success' => true,
            'message' => 'Data berhasil diperbarui',
            'data'    => ['id' => $id] + $data,
        ]);
    }

    // DELETE /buku?id=1
    public function destroy($id): void {
        $id = $this->requireId($id);
        if (!$this->model->find($id)) {
            $this->fail(404, 'Data tidak ditemukan');
        }
        $this->model->delete($id);
        $this->json(200, ['success' => true, 'message' => 'Data berhasil dihapus', 'data' => null]);
    }

    private function validate(array $in): array {
        $judul    = $this->text($in['judul'] ?? '');
        $penulis  = $this->text($in['penulis'] ?? '');
        $penerbit = $this->text($in['penerbit'] ?? '');
        $tahun    = $this->text($in['tahun_terbit'] ?? '');

        if ($judul === '' || $penulis === '' || $penerbit === '' || $tahun === '') {
            $this->fail(400, 'Field judul, penulis, penerbit, dan tahun_terbit wajib diisi');
        }
        if (!ctype_digit($tahun) || (int) $tahun < 1900 || (int) $tahun > (int) date('Y') + 1) {
            $this->fail(400, 'tahun_terbit harus berupa angka tahun yang valid');
        }
        if (strlen($judul) > 150)    $this->fail(400, 'Judul maksimal 150 karakter');
        if (strlen($penulis) > 100)  $this->fail(400, 'Penulis maksimal 100 karakter');
        if (strlen($penerbit) > 100) $this->fail(400, 'Penerbit maksimal 100 karakter');

        return [
            'judul'        => $judul,
            'penulis'      => $penulis,
            'penerbit'     => $penerbit,
            'tahun_terbit' => (int) $tahun,
        ];
    }
}
