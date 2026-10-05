<?php

class Mahasiswa
{
    // Semua atribut private: hanya bisa diakses lewat getter dan setter
    private int $id = 0;
    private string $nim = '';
    private string $nama = '';
    private string $email = '';
    private int $prodiId = 0;
    private int $angkatan;
    private string $prodiNama = '';   // hanya untuk tampilan (hasil JOIN)

    public function __construct()
    {
        $this->angkatan = (int) date('Y');
    }

    // Buat objek dari satu baris data database (data dari database dianggap sudah valid)
    public static function dariBaris(array $row): self
    {
        $m = new self();
        $m->id        = (int) $row['id'];
        $m->nim       = $row['nim'];
        $m->nama      = $row['nama'];
        $m->email     = $row['email'];
        $m->prodiId   = (int) $row['prodi_id'];
        $m->angkatan  = (int) $row['angkatan'];
        $m->prodiNama = $row['prodi_nama'] ?? '';
        return $m;
    }

    // ---------- Getter ----------
    public function getId(): int          { return $this->id; }
    public function getNim(): string      { return $this->nim; }
    public function getNama(): string     { return $this->nama; }
    public function getEmail(): string    { return $this->email; }
    public function getProdiId(): int     { return $this->prodiId; }
    public function getAngkatan(): int    { return $this->angkatan; }
    public function getProdiNama(): string { return $this->prodiNama; }

    // ---------- Setter (dengan validasi sederhana) ----------
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function setNim(string $nim): void
    {
        $nim = trim($nim);

        if ($nim === '') {
            throw new InvalidArgumentException('NIM wajib diisi.');
        }
        if (!ctype_digit($nim)) {
            throw new InvalidArgumentException('NIM harus berupa angka.');
        }
        if (strlen($nim) > 20) {
            throw new InvalidArgumentException('NIM maksimal 20 digit.');
        }

        $this->nim = $nim;
    }

    public function setNama(string $nama): void
    {
        $nama = trim($nama);

        if ($nama === '') {
            throw new InvalidArgumentException('Nama tidak boleh kosong.');
        }
        if (strlen($nama) > 100) {
            throw new InvalidArgumentException('Nama maksimal 100 karakter.');
        }

        $this->nama = $nama;
    }

    public function setEmail(string $email): void
    {
        $email = trim($email);

        if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('Format email tidak valid.');
        }

        $this->email = $email;
    }

    public function setProdiId(int $prodiId): void
    {
        if ($prodiId <= 0) {
            throw new InvalidArgumentException('Pilih program studi.');
        }

        $this->prodiId = $prodiId;
    }

    public function setAngkatan(int $angkatan): void
    {
        if ($angkatan < 1990 || $angkatan > (int) date('Y') + 1) {
            throw new InvalidArgumentException('Angkatan tidak valid.');
        }

        $this->angkatan = $angkatan;
    }
}
