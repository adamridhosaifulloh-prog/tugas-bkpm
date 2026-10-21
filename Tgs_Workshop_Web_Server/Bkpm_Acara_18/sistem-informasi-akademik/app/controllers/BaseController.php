<?php
// Class induk untuk seluruh Controller (pewarisan/inheritance).
// Pembentukan response masih sederhana; akan dirapikan menjadi class Response pada Acara 18.
abstract class BaseController {

    protected function json(int $status, array $body): void {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($body, JSON_UNESCAPED_UNICODE);
        exit;
    }

    protected function fail(int $status, string $message): void {
        $this->json($status, ['success' => false, 'message' => $message, 'data' => null]);
    }

    // Membaca body JSON dari request
    protected function input(): array {
        $raw = file_get_contents('php://input');
        if (trim($raw) === '') {
            $this->fail(400, 'Body request kosong, kirim data dalam format JSON');
        }
        $data = json_decode($raw, true);
        if (!is_array($data)) {
            $this->fail(400, 'Format JSON tidak valid');
        }
        return $data;
    }

    // Memastikan parameter id ada dan berupa angka
    protected function requireId($id): int {
        if ($id === null || $id === '') {
            $this->fail(400, 'Parameter id wajib disertakan, contoh: ?id=1');
        }
        if (!ctype_digit((string) $id)) {
            $this->fail(400, 'ID harus berupa angka');
        }
        return (int) $id;
    }

    protected function text($value): string {
        return is_scalar($value) ? trim((string) $value) : '';
    }
}
