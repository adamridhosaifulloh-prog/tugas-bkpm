<?php

require_once __DIR__ . '/../routes/web.php';

$method = $_SERVER['REQUEST_METHOD'];

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Base URL project
$basePath = '/tugas-bkpm/Tgs_Workshop_Web_Server/Bkpm_Acara_5/public';

// Hapus base path
if (str_starts_with($uri, $basePath)) {
    $uri = substr($uri, strlen($basePath));
}

// Jika kosong, jadikan /
if ($uri === '') {
    $uri = '/';
}

/*
|--------------------------------------------------------------------------
| Routing biasa
|--------------------------------------------------------------------------
*/

if (isset($routes[$method][$uri])) {

    $route = $routes[$method][$uri];

    $controllerName = $route['controller'];
    $methodName = $route['method'];

    require_once __DIR__ . '/../app/Controller/' . $controllerName . '.php';

    $controller = new $controllerName();

    $controller->$methodName();

    exit;
}

/*
|--------------------------------------------------------------------------
| Routing parameter /mahasiswa/{id}
|--------------------------------------------------------------------------
*/

if ($method === 'GET' && preg_match('#^/mahasiswa/([0-9]+)$#', $uri, $matches)) {

    $id = $matches[1];

    require_once __DIR__ . '/../app/Controller/MahasiswaController.php';

    $controller = new MahasiswaController();

    $controller->show($id);

    exit;
}

/*
|--------------------------------------------------------------------------
| 404
|--------------------------------------------------------------------------
*/

http_response_code(404);

echo "<h1>404</h1>";
echo "<p>Halaman tidak ditemukan.</p>";