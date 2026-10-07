<?php

require_once __DIR__ . '/../Models/MahasiswaModel.php';

class MahasiswaController
{
    public function index()
    {
        $mahasiswa = [];
        $error     = '';

        try {
            $model     = new MahasiswaModel();
            $mahasiswa = $model->all();
        } catch (PDOException $e) {
            // Detail error teknis tidak ditampilkan ke pengguna
            $error = 'Data mahasiswa tidak dapat dimuat. Periksa koneksi database.';
        }

        require __DIR__ . '/../Views/mahasiswa/index.php';
    }

    public function create()
    {
        require __DIR__ . '/../Views/mahasiswa/create.php';
    }

    public function edit()
    {
        require __DIR__ . '/../Views/mahasiswa/edit.php';
    }
}
