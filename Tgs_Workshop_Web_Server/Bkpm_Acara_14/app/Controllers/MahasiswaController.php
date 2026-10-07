<?php

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../Logger.php';
require_once __DIR__ . '/../Services/MahasiswaService.php';

// Controller tipis: hanya menerima request, memanggil Service, lalu menentukan response.
// Tidak ada validasi dan tidak ada query database di sini.
// Error saat membaca data ditangkap (try-catch) di sini: detail ke app.log,
// pengguna hanya mendapat pesan yang aman lewat flash message.
class MahasiswaController extends BaseController
{
    // Constructor injection: Service diberikan dari luar (lihat public/index.php)
    public function __construct(private MahasiswaService $service) {}

    public function index(): void
    {
        $keyword = trim($_GET['q'] ?? '');

        try {
            $daftar = $this->service->all($keyword);
        } catch (Throwable $e) {
            Logger::error('Gagal memuat daftar mahasiswa', $e);
            setFlash('danger', 'Data mahasiswa gagal dimuat. Silakan coba lagi nanti.');
            $daftar = [];
        }

        $this->view('mahasiswa/index', ['daftar' => $daftar, 'keyword' => $keyword]);
    }

    public function create(): void
    {
        $this->tampilForm(new Mahasiswa(), url('/mahasiswa/store'));
    }

    // POST -> proses -> redirect -> GET (PRG): jika berhasil, pengguna dialihkan
    // sehingga refresh halaman tidak mengirim ulang form.
    public function store(): void
    {
        $result = $this->service->create($_POST);

        if ($result['success']) {
            $this->redirect('/mahasiswa', $result['message']);
        }

        // Gagal: tampilkan flash message error + pesan per field, isian lama tetap terisi
        setFlash('danger', $result['message']);
        $this->tampilForm($result['data'], url('/mahasiswa/store'), $result['errors'], $_POST);
    }

    public function edit(): void
    {
        try {
            $m = $this->service->find((int) ($_GET['id'] ?? 0));
        } catch (Throwable $e) {
            Logger::error('Gagal memuat data mahasiswa untuk diedit', $e);
            $this->redirect('/mahasiswa', 'Data mahasiswa gagal dimuat. Silakan coba lagi nanti.', 'danger');
        }

        if ($m === null) {
            $this->redirect('/mahasiswa', 'Data mahasiswa tidak ditemukan.', 'danger');
        }

        $this->tampilForm($m, url('/mahasiswa/update'));
    }

    public function update(): void
    {
        $result = $this->service->update((int) ($_POST['id'] ?? 0), $_POST);

        if ($result['success']) {
            $this->redirect('/mahasiswa', $result['message']);
        }

        // data null = tidak ada form yang bisa ditampilkan (mis. data tidak ditemukan)
        if ($result['data'] === null) {
            $this->redirect('/mahasiswa', $result['message'], 'danger');
        }

        setFlash('danger', $result['message']);
        $this->tampilForm($result['data'], url('/mahasiswa/update'), $result['errors'], $_POST);
    }

    public function delete(): void
    {
        $result = $this->service->delete((int) ($_POST['id'] ?? 0));

        $this->redirect('/mahasiswa', $result['message'], $result['success'] ? 'success' : 'danger');
    }

    private function tampilForm(Mahasiswa $m, string $action, array $errors = [], array $old = []): void
    {
        try {
            $prodiList = $this->service->daftarProdi();
        } catch (Throwable $e) {
            Logger::error('Gagal memuat daftar prodi', $e);
            setFlash('danger', 'Daftar program studi gagal dimuat. Silakan coba lagi nanti.');
            $prodiList = [];
        }

        $this->view('mahasiswa/form', [
            'm'         => $m,
            'action'    => $action,
            'errors'    => $errors,
            'old'       => $old,
            'prodiList' => $prodiList,
        ]);
    }
}
