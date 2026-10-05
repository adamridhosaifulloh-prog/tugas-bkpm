<?php

class MahasiswaController
{
    public function index()
    {
        require_once __DIR__ . '/../views/mahasiswa/index.php';
    }

    public function create()
    {
        require_once __DIR__ . '/../views/mahasiswa/create.php';
    }

    public function edit()
    {
        require_once __DIR__ . '/../views/mahasiswa/edit.php';
    }
}