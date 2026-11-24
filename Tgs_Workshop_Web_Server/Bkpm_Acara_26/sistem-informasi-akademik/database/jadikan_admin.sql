-- Acara 25: jalankan SATU KALI di phpMyAdmin (database db_workshop)
-- setelah akun admin_test terdaftar (request S-02 pada collection Acara25_RBAC_Testing
-- atau POST /register). Akun yang didaftarkan via /register otomatis berole 'user'.
UPDATE users SET role = 'admin' WHERE username = 'admin_test';

-- Pemeriksaan:
SELECT id, username, role FROM users;
