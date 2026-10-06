<?php

session_start();

// BASE_PATH otomatis (contoh: /Bkpm_Acara_10/public), jadi tidak perlu diubah manual
define('BASE_PATH', rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])), '/'));

require_once __DIR__ . '/../app/helpers.php';
require_once __DIR__ . '/../app/Controllers/AuthController.php';
require_once __DIR__ . '/../app/Controllers/MahasiswaController.php';

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
// Database -> dimasukkan ke Repository -> dimasukkan ke Controller
$database   = new Database();
$repository = new MahasiswaRepository($database);
$controller = new MahasiswaController($repository);

// GET untuk menampilkan, POST untuk mengubah data
$parts  = explode('/', trim($uri, '/'));
$action = $parts[1] ?? 'index';
$allowed = ($_SERVER['REQUEST_METHOD'] === 'POST')
    ? ['store', 'update', 'destroy']
    : ['index', 'create', 'edit'];

if ($parts[0] === 'mahasiswa' && count($parts) <= 2 && in_array($action, $allowed, true)) {
    $controller->$action();
} else {
    http_response_code(404);
    echo '<h1>404</h1><p>Halaman tidak ditemukan.</p>';
}
