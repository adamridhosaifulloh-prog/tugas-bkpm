<?php
/**
 * RESTful API Data Mahasiswa (Acara 16)
 *
 * GET    /api/mahasiswa        -> semua mahasiswa
 * GET    /api/mahasiswa/{id}   -> satu mahasiswa
 * POST   /api/mahasiswa        -> tambah mahasiswa
 * PUT    /api/mahasiswa/{id}   -> ubah mahasiswa
 * DELETE /api/mahasiswa/{id}   -> hapus mahasiswa
 *
 * Alternatif tanpa mod_rewrite: mahasiswa.php?id=1 atau mahasiswa.php/1
 */
header('Content-Type: application/json; charset=utf-8');

// ---------- Helper response ----------
function kirim(int $status, bool $success, string $message, $data = null): void
{
    http_response_code($status);
    echo json_encode(
        ['success' => $success, 'message' => $message, 'data' => $data],
        JSON_UNESCAPED_UNICODE
    );
    exit;
}

// ---------- Koneksi database (PDO) ----------
$host = 'localhost';
$db   = 'si_akademik_api';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    kirim(500, false, 'Koneksi database gagal: ' . $e->getMessage());
}

// ---------- Ambil method dan ID ----------
$method = $_SERVER['REQUEST_METHOD'];
$id = null;

if (isset($_GET['id'])) {
    $id = $_GET['id'];
} elseif (!empty($_SERVER['PATH_INFO'])) {
    $id = trim($_SERVER['PATH_INFO'], '/');
}

// ID harus berupa bilangan bulat positif
if ($id !== null && $id !== '' && !ctype_digit((string)$id)) {
    kirim(400, false, 'ID harus berupa angka');
}
$id = ($id === null || $id === '') ? null : (int)$id;

// ---------- Helper: baca & validasi body JSON ----------
function bacaInput(): array
{
    $raw = file_get_contents('php://input');
    if (trim($raw) === '') {
        kirim(400, false, 'Body request kosong, kirim data dalam format JSON');
    }
    $input = json_decode($raw, true);
    if (!is_array($input)) {
        kirim(400, false, 'Format JSON tidak valid');
    }
    return $input;
}

function validasi(array $input): array
{
    foreach (['nim', 'nama', 'email'] as $f) {
        if (!array_key_exists($f, $input)) {
            kirim(400, false, "Data JSON tidak lengkap: field '$f' wajib ada");
        }
        if (!is_string($input[$f]) && !is_numeric($input[$f])) {
            kirim(400, false, "Field '$f' harus berupa teks");
        }
    }
    $nim   = trim((string)$input['nim']);
    $nama  = trim((string)$input['nama']);
    $email = trim((string)$input['email']);

    if ($nim === '')   kirim(400, false, 'NIM tidak boleh kosong');
    if ($nama === '')  kirim(400, false, 'Nama tidak boleh kosong');
    if ($email === '') kirim(400, false, 'Email tidak boleh kosong');
    if (strlen($nim) > 20)    kirim(400, false, 'NIM maksimal 20 karakter');
    if (strlen($nama) > 100)  kirim(400, false, 'Nama maksimal 100 karakter');
    if (strlen($email) > 100) kirim(400, false, 'Email maksimal 100 karakter');
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        kirim(400, false, 'Format email tidak valid');
    }
    return ['nim' => $nim, 'nama' => $nama, 'email' => $email];
}

function nimSudahAda(PDO $pdo, string $nim, ?int $kecualiId = null): bool
{
    $sql = "SELECT COUNT(*) FROM mahasiswa WHERE nim = :nim";
    $par = ['nim' => $nim];
    if ($kecualiId !== null) {
        $sql .= " AND id <> :id";
        $par['id'] = $kecualiId;
    }
    $stmt = $pdo->prepare($sql);
    $stmt->execute($par);
    return (int)$stmt->fetchColumn() > 0;
}

function cariById(PDO $pdo, int $id)
{
    $stmt = $pdo->prepare("SELECT * FROM mahasiswa WHERE id = :id");
    $stmt->execute(['id' => $id]);
    return $stmt->fetch();
}

// ---------- Routing berdasarkan HTTP method ----------
try {
    switch ($method) {

        case 'GET':
            if ($id === null) {
                $data = $pdo->query("SELECT * FROM mahasiswa ORDER BY id")->fetchAll();
                kirim(200, true, 'Data berhasil diambil', $data);
            }
            $data = cariById($pdo, $id);
            if (!$data) {
                kirim(404, false, "Data mahasiswa dengan ID $id tidak ditemukan");
            }
            kirim(200, true, 'Data mahasiswa berhasil ditemukan', $data);
            break;

        case 'POST':
            if ($id !== null) {
                kirim(405, false, 'POST tidak boleh memakai ID, gunakan PUT untuk mengubah data');
            }
            $d = validasi(bacaInput());
            if (nimSudahAda($pdo, $d['nim'])) {
                kirim(409, false, 'NIM ' . $d['nim'] . ' sudah terdaftar');
            }
            $stmt = $pdo->prepare("INSERT INTO mahasiswa (nim, nama, email) VALUES (:nim, :nama, :email)");
            $stmt->execute($d);
            $d = ['id' => (int)$pdo->lastInsertId()] + $d;
            kirim(201, true, 'Data mahasiswa berhasil ditambahkan', $d);
            break;

        case 'PUT':
            if ($id === null) {
                kirim(400, false, 'ID wajib disertakan, contoh: /api/mahasiswa/1');
            }
            if (!cariById($pdo, $id)) {
                kirim(404, false, "Data mahasiswa dengan ID $id tidak ditemukan");
            }
            $d = validasi(bacaInput());
            if (nimSudahAda($pdo, $d['nim'], $id)) {
                kirim(409, false, 'NIM ' . $d['nim'] . ' sudah dipakai mahasiswa lain');
            }
            $stmt = $pdo->prepare("UPDATE mahasiswa SET nim = :nim, nama = :nama, email = :email WHERE id = :id");
            $stmt->execute($d + ['id' => $id]);
            $d = ['id' => $id] + $d;
            kirim(200, true, 'Data mahasiswa berhasil diubah', $d);
            break;

        case 'DELETE':
            if ($id === null) {
                kirim(400, false, 'ID wajib disertakan, contoh: /api/mahasiswa/1');
            }
            if (!cariById($pdo, $id)) {
                kirim(404, false, "Data mahasiswa dengan ID $id tidak ditemukan");
            }
            $stmt = $pdo->prepare("DELETE FROM mahasiswa WHERE id = :id");
            $stmt->execute(['id' => $id]);
            kirim(200, true, 'Data mahasiswa berhasil dihapus');
            break;

        default:
            header('Allow: GET, POST, PUT, DELETE');
            kirim(405, false, 'Metode HTTP tidak diizinkan');
    }
} catch (PDOException $e) {
    kirim(500, false, 'Terjadi kesalahan pada server: ' . $e->getMessage());
}
