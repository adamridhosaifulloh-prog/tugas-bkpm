<?php
header('Content-Type: application/json; charset=utf-8');

// Konfigurasi Database
$host = 'localhost';
$db   = 'si_akademik_api';
$user = 'root';
$pass = ''; // Sesuaikan dengan password database Anda jika ada

try {
    // 3. Buat koneksi PDO
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Koneksi database gagal: ' . $e->getMessage(),
        'data' => null
    ]);
    exit;
}

// Ambil HTTP Method
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        // Cek apakah ada parameter 'id' (misal: api/mahasiswa.php?id=1)
        if (isset($_GET['id'])) {
            $id = $_GET['id'];
            $stmt = $pdo->prepare("SELECT * FROM mahasiswa WHERE id = :id");
            $stmt->execute(['id' => $id]);
            $data = $stmt->fetch();

            if ($data) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Data mahasiswa berhasil ditemukan',
                    'data' => $data
                ]);
            } else {
                http_response_code(404);
                echo json_encode([
                    'success' => false,
                    'message' => 'Data mahasiswa tidak ditemukan',
                    'data' => null
                ]);
            }
        } else {
            // 4. Ambil seluruh data mahasiswa
            $stmt = $pdo->query("SELECT * FROM mahasiswa");
            $data = $stmt->fetchAll();

            // 5 & 6. Ubah hasil query menjadi JSON dengan format terstruktur
            echo json_encode([
                'success' => true,
                'message' => 'Data berhasil diambil',
                'data' => $data
            ]);
        }
        break;

    case 'POST':
        // Membaca data JSON dari request body
        $input = json_decode(file_get_contents('php://input'), true);

        if (isset($input['nim'], $input['nama'], $input['email'])) {
            $stmt = $pdo->prepare("INSERT INTO mahasiswa (nim, nama, email) VALUES (:nim, :nama, :email)");
            $success = $stmt->execute([
                'nim'   => $input['nim'],
                'nama'  => $input['nama'],
                'email' => $input['email']
            ]);

            if ($success) {
                http_response_code(201);
                echo json_encode([
                    'success' => true,
                    'message' => 'Data mahasiswa berhasil ditambahkan',
                    'data' => [
                        'id'    => $pdo->lastInsertId(),
                        'nim'   => $input['nim'],
                        'nama'  => $input['nama'],
                        'email' => $input['email']
                    ]
                ]);
            } else {
                http_response_code(500);
                echo json_encode([
                    'success' => false,
                    'message' => 'Gagal menambahkan data mahasiswa',
                    'data' => null
                ]);
            }
        } else {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'Data input tidak lengkap (nim, nama, dan email wajib diisi)',
                'data' => null
            ]);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode([
            'success' => false,
            'message' => 'Metode HTTP tidak diizinkan',
            'data' => null
        ]);
        break;
}