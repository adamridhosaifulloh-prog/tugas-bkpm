# Login Admin (Acara 25 - RBAC)

Panduan membuat dan menggunakan akun dengan role **admin** pada aplikasi
`sistem-informasi-akademik`.

## Ringkasan

| Item | Keterangan |
|------|------------|
| Role | `admin` |
| Endpoint login | `POST {base_url}/login` |
| Halaman login | `public/views/login.html` |
| Token | JWT HS256, berlaku **1 jam** |
| Akun contoh | `admin_test` / `Rahasia123!` (hanya untuk praktik lokal) |

## Hak akses admin

| Resource | Method | Akses |
|----------|--------|-------|
| `/mahasiswa` | GET | Boleh (publik) |
| `/mahasiswa` | POST (tambah) | Boleh |
| `/mahasiswa?id=` | PUT (ubah) | Boleh |
| `/mahasiswa?id=` | DELETE (hapus) | Boleh |

Dashboard menampilkan tombol **Tambah**, kolom **Aksi** (Edit/Hapus), dan teks
`Masuk sebagai: admin`.

## Cara membuat akun admin

Akun yang mendaftar lewat `POST /register` **selalu berole `user`**. Role admin
hanya diberikan lewat basis data.

1. Daftarkan akun (Postman: request **S-02 Daftar akun admin_test**):
   ```json
   POST {base_url}/register
   {
     "username": "admin_test",
     "email": "admin_test@example.com",
     "password": "Rahasia123!"
   }
   ```
2. Buka phpMyAdmin > database `db_workshop` > tab SQL, jalankan **satu kali**
   `database/jadikan_admin.sql`:
   ```sql
   UPDATE users SET role = 'admin' WHERE username = 'admin_test';
   SELECT id, username, role FROM users;
   ```
3. Pastikan kolom `role` pada `admin_test` bernilai `admin`.

## Cara login

### Lewat browser
1. Buka `http://localhost/<folder-proyek>/sistem-informasi-akademik/public/views/login.html`
2. Isi username `admin_test` dan password `Rahasia123!`, klik **Masuk**.
3. Berhasil: diarahkan ke `dashboard.html`; `token` dan `role` tersimpan di `localStorage`.

### Lewat Postman / API
```json
POST {base_url}/login
{
  "username": "admin_test",
  "password": "Rahasia123!"
}
```
Respons sukses (200):
```json
{
  "success": true,
  "message": "Login berhasil",
  "data": { "token": "<jwt>", "role": "admin" }
}
```
Gunakan token pada request berikutnya:
```
Authorization: Bearer <jwt>
```

## Pengujian (Postman: Acara25_RBAC_Testing)

| Kode | Skenario | Hasil yang diharapkan |
|------|----------|-----------------------|
| TC-21 | Login admin | 200, `role` = `admin` |
| TC-27 | Admin tambah data | 201 Created |
| TC-28 | Admin ubah data | 200 OK |
| TC-29 | Admin hapus data | 200 OK |

## Kemungkinan error

| Status | Penyebab |
|--------|----------|
| 400 | Username atau password kosong |
| 401 | Username atau password salah / token tidak valid atau kedaluwarsa |
| 409 | Username atau email sudah terdaftar (saat register) |

> **Catatan keamanan:** password contoh dan `Auth::SECRET` hanya untuk praktik
> lokal. Jangan dipakai di server produksi.
