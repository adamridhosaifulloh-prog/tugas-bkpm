<?php

require_once __DIR__ . '/../Models/MahasiswaModel.php';
require_once __DIR__ . '/../Models/ProdiModel.php';

class MahasiswaController
{
    private MahasiswaModel $model;
    private ProdiModel $prodi;

    public function __construct()
    {
        $this->model = new MahasiswaModel();
        $this->prodi = new ProdiModel();
    }

    // Daftar + pencarian (Tugas Mandiri)
    public function index(): void
    {
        $keyword = trim($_GET['q'] ?? '');
        $daftar  = $this->model->all($keyword);
        require __DIR__ . '/../Views/mahasiswa/index.php';
    }

    public function create(): void
    {
        $data = ['id' => '', 'nim' => '', 'nama' => '', 'email' => '', 'prodi_id' => '', 'angkatan' => date('Y')];
        $prodiList = $this->prodi->all();
        $action = url('/mahasiswa/store');
        require __DIR__ . '/../Views/mahasiswa/form.php';
    }

    public function store(): void
    {
        $data = $this->ambilInput();

        if ($data['nim'] === '' || $data['nama'] === '' || $data['prodi_id'] === 0) {
            redirect('/mahasiswa/create', 'NIM, nama, dan prodi wajib diisi.', 'danger');
        }

        $this->model->create($data);
        redirect('/mahasiswa', 'Data mahasiswa berhasil ditambahkan.');
    }

    public function edit(): void
    {
        $data = $this->model->find((int) ($_GET['id'] ?? 0));
        if (!$data) {
            redirect('/mahasiswa', 'Data mahasiswa tidak ditemukan.', 'danger');
        }

        $prodiList = $this->prodi->all();
        $action = url('/mahasiswa/update');
        require __DIR__ . '/../Views/mahasiswa/form.php';
    }

    public function update(): void
    {
        $id   = (int) ($_POST['id'] ?? 0);
        $data = $this->ambilInput();

        if ($data['nim'] === '' || $data['nama'] === '' || $data['prodi_id'] === 0) {
            redirect('/mahasiswa/edit?id=' . $id, 'NIM, nama, dan prodi wajib diisi.', 'danger');
        }

        $this->model->update($id, $data);
        redirect('/mahasiswa', 'Data mahasiswa berhasil diperbarui.');
    }

    public function destroy(): void
    {
        $this->model->delete((int) ($_POST['id'] ?? 0));
        redirect('/mahasiswa', 'Data mahasiswa berhasil dihapus.');
    }

    private function ambilInput(): array
    {
        return [
            'nim'      => trim($_POST['nim'] ?? ''),
            'nama'     => trim($_POST['nama'] ?? ''),
            'email'    => trim($_POST['email'] ?? ''),
            'prodi_id' => (int) ($_POST['prodi_id'] ?? 0),
            'angkatan' => (int) ($_POST['angkatan'] ?? date('Y')),
        ];
    }
}
