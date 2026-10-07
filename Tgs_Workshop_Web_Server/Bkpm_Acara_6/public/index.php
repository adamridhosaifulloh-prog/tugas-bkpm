<?php

session_start();

require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../routes/web.php';

$method = $_SERVER['REQUEST_METHOD'];

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Base URL project
$basePath = '/tugas-bkpm/Tgs_Workshop_Web_Server/Bkpm_Acara_6/public';

// Hapus base path
if (str_starts_with($uri, $basePath)) {
    $uri = substr($uri, strlen($basePath));
}

// Jika kosong
if ($uri === '') {
    $uri = '/';
}

/*
|--------------------------------------------------------------------------
| Routing
|--------------------------------------------------------------------------
*/

if (isset($routes[$method][$uri])) {

    $route = $routes[$method][$uri];

    // Middleware
    if (isset($route['middleware'])) {

        $middleware = $route['middleware'];

        require_once __DIR__ . '/../app/middleware/' . $middleware . '.php';

        $middleware::handle();
    }

    // Controller
    $controllerName = $route['controller'];
    $methodName = $route['method'];

    require_once __DIR__ . '/../app/Controllers/' . $controllerName . '.php';

    $controller = new $controllerName();

    $controller->$methodName();

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