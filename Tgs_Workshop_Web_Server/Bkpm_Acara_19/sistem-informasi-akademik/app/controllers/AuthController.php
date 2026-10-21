<?php

class AuthController
{
    private User $model;

    public function __construct()
    {
        $this->model = new User();
    }

    public function register(): void
    {
        $data = json_decode(file_get_contents('php://input'), true);

        if (!is_array($data)) {
            Response::json(400, false, 'Body request harus berupa JSON yang valid');
        }

        if (
            empty($data['username']) ||
            empty($data['email']) ||
            empty($data['password'])
        ) {
            Response::json(
                400,
                false,
                'Username, email, dan password wajib diisi'
            );
        }

        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            Response::json(422, false, 'Format email tidak valid', null, [
                'email' => 'Masukkan alamat email yang valid.',
            ]);
        }

        if ($this->model->findByUsername(trim($data['username']))) {
            Response::json(409, false, 'Username sudah digunakan');
        }

        $data['username'] = trim($data['username']);
        $data['email'] = trim($data['email']);
        $data['password'] = password_hash(
            $data['password'],
            PASSWORD_DEFAULT
        );

        try {
            $id = $this->model->create($data);
        } catch (PDOException $e) {
            // Constraint UNIQUE juga menjaga agar email tidak boleh duplikat.
            if ($e->getCode() === '23000') {
                Response::json(409, false, 'Username atau email sudah digunakan');
            }
            throw $e;
        }

        Response::json(
            201,
            true,
            'Registrasi berhasil',
            ['id' => $id]
        );
    }
}
