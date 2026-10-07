<?php

// Jangan tampilkan error teknis ke pengguna; error dicatat ke storage/logs/app.log
ini_set('display_errors', '0');
error_reporting(E_ALL);

// Cookie session lebih aman: tidak bisa dibaca JavaScript dan tidak ikut request lintas situs
session_set_cookie_params(['httponly' => true, 'samesite' => 'Lax']);
session_start();

// BASE_PATH otomatis (contoh: /Bkpm_Acara_13/public), jadi tidak perlu diubah manual
define('BASE_PATH', rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])), '/'));

require_once __DIR__ . '/../app/helpers.php';
require_once __DIR__ . '/../app/Logger.php';
require_once __DIR__ . '/../app/Controllers/AuthController.php';
require_once __DIR__ . '/../app/Controllers/MahasiswaController.php';

// Penangkap terakhir: exception yang lolos (mis. koneksi database gagal)
// dicatat ke app.log, pengguna hanya melihat halaman error yang aman.
set_exception_handler(function (Throwable $e): void {
    Logger::error('Exception tidak tertangani', $e);
    http_response_code(500);
    require __DIR__ . '/../app/Views/errors/500.php';
});

// Ambil path tanpa BASE_PATH, contoh: /mahasiswa/edit
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = substr($uri, strlen(BASE_PATH));
$uri = '/' . trim($uri, '/');

// Login & logout (boleh diakses tanpa login)
if ($uri === '/login') {
    $auth = new AuthController();
    $_SERVER['REQUEST_METHOD'] === 'POST' ? $auth->login() : $auth->loginForm();
    exit;
}

if ($uri === '/logout') {
    (new AuthController())->logout();
}

// Semua halaman di bawah ini wajib login
cekLogin();

if ($uri === '/') {
    redirect('/mahasiswa');
}

// Merakit objek (Dependency Injection):
// Database -> Repository -> Service -> Controller
$database    = new Database();
$mhsRepo     = new MahasiswaRepository($database);
$prodiRepo   = new ProdiRepository($database);
$service     = new MahasiswaService($mhsRepo, $prodiRepo);
$controller  = new MahasiswaController($service);

// GET untuk menampilkan, POST untuk mengubah data
$parts  = explode('/', trim($uri, '/'));
$action = $parts[1] ?? 'index';
$allowed = ($_SERVER['REQUEST_METHOD'] === 'POST')
    ? ['store', 'update', 'delete']
    : ['index', 'create', 'edit'];

if ($parts[0] === 'mahasiswa' && count($parts) <= 2 && in_array($action, $allowed, true)) {
    $controller->$action();
} else {
    http_response_code(404);
    echo '<h1>404</h1><p>Halaman tidak ditemukan.</p>';
}
