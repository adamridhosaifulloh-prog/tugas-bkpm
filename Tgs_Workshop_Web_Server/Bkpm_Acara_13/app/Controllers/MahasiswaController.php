<?php

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../Services/MahasiswaService.php';

// Controller tipis: hanya menerima request, memanggil Service, lalu menentukan response.
// Tidak ada validasi dan tidak ada query database di sini.
class MahasiswaController extends BaseController
{
    // Constructor injection: Service diberikan dari luar (lihat public/index.php)
    public function __construct(private MahasiswaService $service) {}

    public function index(): void
    {
        $keyword = trim($_GET['q'] ?? '');
        $daftar  = $this->service->all($keyword);
        $this->view('mahasiswa/index', ['daftar' => $daftar, 'keyword' => $keyword]);
    }

    public function create(): void
    {
        $this->tampilForm(new Mahasiswa(), url('/mahasiswa/store'));
    }

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
        $m = $this->service->find((int) ($_GET['id'] ?? 0));
        if ($m === null) {
            $this->redirect('/mahasiswa', 'Data mahasiswa tidak ditemukan.', 'danger');
        }

        $this->tampilForm($m, url('/mahasiswa/update'));
    }

    public function update(): void
    {
        $id = (int) ($_POST['id'] ?? 0);
        if ($this->service->find($id) === null) {
            $this->redirect('/mahasiswa', 'Data mahasiswa tidak ditemukan.', 'danger');
        }

        $result = $this->service->update($id, $_POST);

        if ($result['success']) {
            $this->redirect('/mahasiswa', $result['message']);
        }

        setFlash('danger', $result['message']);
        $this->tampilForm($result['data'], url('/mahasiswa/update'), $result['errors'], $_POST);
    }

    public function destroy(): void
    {
        $result = $this->service->delete((int) ($_POST['id'] ?? 0));

        $this->redirect('/mahasiswa', $result['message'], $result['success'] ? 'success' : 'danger');
    }

    private function tampilForm(Mahasiswa $m, string $action, array $errors = [], array $old = []): void
    {
        $this->view('mahasiswa/form', [
            'm'         => $m,
            'action'    => $action,
            'errors'    => $errors,
            'old'       => $old,
            'prodiList' => $this->service->daftarProdi(),
        ]);
    }
}
