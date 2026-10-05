<?php

require_once __DIR__ . '/../Models/ProdiModel.php';

class ProdiController
{
    private ProdiModel $model;

    public function __construct()
    {
        $this->model = new ProdiModel();
    }

    public function index(): void
    {
        $daftar = $this->model->all();
        require __DIR__ . '/../Views/prodi/index.php';
    }

    public function create(): void
    {
        $data = ['id' => '', 'kode' => '', 'nama' => ''];
        $action = url('/prodi/store');
        require __DIR__ . '/../Views/prodi/form.php';
    }

    public function store(): void
    {
        $kode = strtoupper(trim($_POST['kode'] ?? ''));
        $nama = trim($_POST['nama'] ?? '');

        if ($kode === '' || $nama === '') {
            redirect('/prodi/create', 'Kode dan nama prodi wajib diisi.', 'danger');
        }

        $this->model->create($kode, $nama);
        redirect('/prodi', 'Data prodi berhasil ditambahkan.');
    }

    public function edit(): void
    {
        $data = $this->model->find((int) ($_GET['id'] ?? 0));
        if (!$data) {
            redirect('/prodi', 'Data prodi tidak ditemukan.', 'danger');
        }

        $action = url('/prodi/update');
        require __DIR__ . '/../Views/prodi/form.php';
    }

    public function update(): void
    {
        $id   = (int) ($_POST['id'] ?? 0);
        $kode = strtoupper(trim($_POST['kode'] ?? ''));
        $nama = trim($_POST['nama'] ?? '');

        if ($kode === '' || $nama === '') {
            redirect('/prodi/edit?id=' . $id, 'Kode dan nama prodi wajib diisi.', 'danger');
        }

        $this->model->update($id, $kode, $nama);
        redirect('/prodi', 'Data prodi berhasil diperbarui.');
    }

    public function destroy(): void
    {
        try {
            $this->model->delete((int) ($_POST['id'] ?? 0));
            redirect('/prodi', 'Data prodi berhasil dihapus.');
        } catch (PDOException $e) {
            // Prodi masih dipakai mahasiswa / mata kuliah (foreign key)
            redirect('/prodi', 'Prodi tidak bisa dihapus karena masih dipakai.', 'danger');
        }
    }
}
