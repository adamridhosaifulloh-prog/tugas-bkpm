<?php

class Mahasiswa
{
    private string $nim;
    private string $nama;
    private string $prodi;

    public function __construct(
        string $nim,
        string $nama,
        string $prodi
    ) {
        $this->nim = $nim;
        $this->nama = $nama;
        $this->prodi = $prodi;
    }

    public function getNim()
    {
        return $this->nim;
    }

    public function getNama()
    {
        return $this->nama;
    }

    public function getProdi()
    {
        return $this->prodi;
    }

    public function getAngkatan()
    {
        return 2000 + (int) substr($this->nim, 0, 2);
    }
}