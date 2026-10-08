<?php

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../Repositories/MahasiswaRepository.php';

// Mewarisi BaseController: view() dan redirect() dipakai dari class induk
class MahasiswaController extends BaseController
{
    private MahasiswaRepository $repo;

    // Constructor injection: Repository diberikan dari luar (lihat public/index.php)
    public function __construct(MahasiswaRepository $repo)
    {
        $this->repo = $repo;
    }

    public function index(): void
    {
        $keyword = trim($_GET['q'] ?? '');
        $daftar  = $this->repo->all($keyword);
        $this->view('mahasiswa/index', ['daftar' => $daftar, 'keyword' => $keyword]);
    }

    public function create(): void
    {
        $this->tampilForm(new Mahasiswa(), url('/mahasiswa/store'));
    }

    public function store(): void
    {
        [$m, $errors] = $this->ambilInput();

        if (!$errors) {
            try {
                $this->repo->create($m);
                $this->redirect('/mahasiswa', 'Data mahasiswa berhasil ditambahkan.');
            } catch (PDOException $e) {
                $errors = $this->errorDatabase($e);
            }
        }

        $this->tampilForm($m, url('/mahasiswa/store'), $errors, $_POST);
    }

    public function edit(): void
    {
        $m = $this->repo->find((int) ($_GET['id'] ?? 0));
        if ($m === null) {
            $this->redirect('/mahasiswa', 'Data mahasiswa tidak ditemukan.', 'danger');
        }

        $this->tampilForm($m, url('/mahasiswa/update'));
    }

    public function update(): void
    {
        $id = (int) ($_POST['id'] ?? 0);
        if ($this->repo->find($id) === null) {
            $this->redirect('/mahasiswa', 'Data mahasiswa tidak ditemukan.', 'danger');
        }

        [$m, $errors] = $this->ambilInput($id);

        if (!$errors) {
            try {
                $this->repo->update($m);
                $this->redirect('/mahasiswa', 'Data mahasiswa berhasil diperbarui.');
            } catch (PDOException $e) {
                $errors = $this->errorDatabase($e);
            }
        }

        $this->tampilForm($m, url('/mahasiswa/update'), $errors, $_POST);
    }

    public function destroy(): void
    {
        $this->repo->delete((int) ($_POST['id'] ?? 0));
        $this->redirect('/mahasiswa', 'Data mahasiswa berhasil dihapus.');
    }

    // Isi objek Mahasiswa lewat setter. Setter yang gagal validasi menghasilkan pesan error.
    private function ambilInput(int $id = 0): array
    {
        $m = new Mahasiswa();
        $m->setId($id);
        $errors = [];

        $input = [
            'nim'      => ['setNim',      $_POST['nim'] ?? ''],
            'nama'     => ['setNama',     $_POST['nama'] ?? ''],
            'email'    => ['setEmail',    $_POST['email'] ?? ''],
            'prodi_id' => ['setProdiId',  (int) ($_POST['prodi_id'] ?? 0)],
            'angkatan' => ['setAngkatan', (int) ($_POST['angkatan'] ?? 0)],
        ];

        foreach ($input as $field => [$setter, $nilai]) {
            try {
                $m->$setter($nilai);
            } catch (InvalidArgumentException $e) {
                $errors[$field] = $e->getMessage();
            }
        }

        return [$m, $errors];
    }

    // NIM sudah dipakai (kolom UNIQUE) -> error 23000
    private function errorDatabase(PDOException $e): array
    {
        if ($e->getCode() === '23000') {
            return ['nim' => 'NIM sudah terdaftar.'];
        }
        throw $e;
    }

    private function tampilForm(Mahasiswa $m, string $action, array $errors = [], array $old = []): void
    {
        $this->view('mahasiswa/form', [
            'm'         => $m,
            'action'    => $action,
            'errors'    => $errors,
            'old'       => $old,
            'prodiList' => $this->repo->daftarProdi(),
        ]);
    }
}
