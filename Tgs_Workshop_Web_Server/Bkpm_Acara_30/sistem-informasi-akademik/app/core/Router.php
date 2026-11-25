<?php
class Router {
    // Peta sederhana: nama resource pada URL => nama class Controller
    private array $map = [
        'mahasiswa' => 'MahasiswaController',
        'buku'      => 'BukuController',   // Tugas Praktik Mandiri 1
        'register'  => 'AuthController',
        'login'     => 'AuthController',
    ];

    public function dispatch(): void {
        $uri = trim((string) parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
        $segments = explode('/', $uri);
        $resource = end($segments) ?: 'mahasiswa';

        $controllerName = $this->map[$resource] ?? null;
        if (!$controllerName) {
            $this->error(404, 'Endpoint tidak ditemukan');
        }

        $controller = new $controllerName();

        // Endpoint autentikasi bukan bagian dari CRUD standar.
        if (in_array($resource, ['register', 'login'], true)) {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                header('Allow: POST');
                Response::json(405, false, 'Method tidak diizinkan');
            }

            if ($resource === 'register') {
                $controller->register();
            } else {
                $controller->login();
            }
            return;
        }

        $id = $_GET['id'] ?? null;
        $method = $_SERVER['REQUEST_METHOD'];

        // PHP tidak mengisi $_POST/$_FILES untuk PUT multipart. Karena itu update
        // dengan foto dikirim lewat POST + ?_method=PUT (method override).
        if ($method === 'POST' && strtoupper($_GET['_method'] ?? '') === 'PUT') {
            $method = 'PUT';
        }

        try {
            switch ($method) {
                case 'GET':    $controller->index(); break;
                case 'POST':   $controller->store(); break;
                case 'PUT':    $controller->update($id); break;
                case 'DELETE': $controller->destroy($id); break;
                default:
                    header('Allow: GET, POST, PUT, DELETE');
                    $this->error(405, 'Method tidak diizinkan');
            }
        } catch (PDOException $e) {
            $this->error(500, 'Terjadi kesalahan pada server');
        }
    }

    private function error(int $status, string $message): void {
        Response::json($status, false, $message);
    }
}
