<?php

require_once __DIR__ . '/../Models/MatakuliahModel.php';
require_once __DIR__ . '/../Models/ProdiModel.php';

class MatakuliahController
{
    private MatakuliahModel $model;
    private ProdiModel $prodi;

    public function __construct()
    {
        $this->model = new MatakuliahModel();
        $this->prodi = new ProdiModel();
    }

    public function index(): void
    {
        $daftar = $this->model->all();
        require __DIR__ . '/../Views/matakuliah/index.php';
    }

    public function create(): void
    {
        $data = ['id' => '', 'kode' => '', 'nama' => '', 'sks' => 2, 'prodi_id' => ''];
        $prodiList = $this->prodi->all();
        $action = url('/matakuliah/store');
        require __DIR__ . '/../Views/matakuliah/form.php';
    }

    public function store(): void
    {
        $data = $this->ambilInput();

        if ($data['kode'] === '' || $data['nama'] === '' || $data['prodi_id'] === 0) {
            redirect('/matakuliah/create', 'Kode, nama, dan prodi wajib diisi.', 'danger');
        }

        $this->model->create($data);
        redirect('/matakuliah', 'Data mata kuliah berhasil ditambahkan.');
    }

    public function edit(): void
    {
        $data = $this->model->find((int) ($_GET['id'] ?? 0));
        if (!$data) {
            redirect('/matakuliah', 'Data mata kuliah tidak ditemukan.', 'danger');
        }

        $prodiList = $this->prodi->all();
        $action = url('/matakuliah/update');
        require __DIR__ . '/../Views/matakuliah/form.php';
    }

    public function update(): void
    {
        $id   = (int) ($_POST['id'] ?? 0);
        $data = $this->ambilInput();

        if ($data['kode'] === '' || $data['nama'] === '' || $data['prodi_id'] === 0) {
            redirect('/matakuliah/edit?id=' . $id, 'Kode, nama, dan prodi wajib diisi.', 'danger');
        }

        $this->model->update($id, $data);
        redirect('/matakuliah', 'Data mata kuliah berhasil diperbarui.');
    }

    public function destroy(): void
    {
        $this->model->delete((int) ($_POST['id'] ?? 0));
        redirect('/matakuliah', 'Data mata kuliah berhasil dihapus.');
    }

    private function ambilInput(): array
    {
        return [
            'kode'     => strtoupper(trim($_POST['kode'] ?? '')),
            'nama'     => trim($_POST['nama'] ?? ''),
            'sks'      => (int) ($_POST['sks'] ?? 0),
            'prodi_id' => (int) ($_POST['prodi_id'] ?? 0),
        ];
    }
}
