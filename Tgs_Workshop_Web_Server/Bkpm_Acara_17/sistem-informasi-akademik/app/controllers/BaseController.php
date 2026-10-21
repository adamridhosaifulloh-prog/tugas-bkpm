<?php
abstract class BaseController {
    protected function json(int $status, array $body): void {
        Response::json(
            $status,
            (bool) ($body['success'] ?? false),
            (string) ($body['message'] ?? ''),
            $body['data'] ?? null,
            (array) ($body['errors'] ?? [])
        );
    }

    protected function fail(int $status, string $message, array $errors = []): void {
        Response::json($status, false, $message, null, $errors);
    }

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

    protected function requireId($id): int {
        if ($id === null || $id === '') {
            $this->fail(400, 'Parameter id wajib disertakan, contoh: ?id=1');
        }
        if (!ctype_digit((string) $id) || (int) $id < 1) {
            $this->fail(400, 'ID harus berupa bilangan bulat positif', ['id' => 'ID harus berupa bilangan bulat positif']);
        }
        return (int) $id;
    }

    protected function text($value): string {
        return is_scalar($value) ? trim((string) $value) : '';
    }
}
