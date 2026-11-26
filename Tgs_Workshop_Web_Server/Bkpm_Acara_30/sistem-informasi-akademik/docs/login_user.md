# Login User (Acara 25 - RBAC)

Panduan membuat dan menggunakan akun dengan role **user** pada aplikasi
`sistem-informasi-akademik`.

## Ringkasan

| Item | Keterangan |
|------|------------|
| Role | `user` (default setiap akun baru) |
| Endpoint login | `POST {base_url}/login` |
| Halaman login | `public/views/login.html` |
| Token | JWT HS256, berlaku **1 jam** |
| Akun contoh | `user_test` / `Rahasia123!` (hanya untuk praktik lokal) |

## Hak akses user

| Resource | Method | Akses |
|----------|--------|-------|
| `/mahasiswa` | GET | Boleh (publik) |
| `/mahasiswa` | POST (tambah) | **Ditolak** (403) |
| `/mahasiswa?id=` | PUT (ubah) | **Ditolak** (403) |
| `/mahasiswa?id=` | DELETE (hapus) | **Ditolak** (403) |

Dashboard hanya menampilkan data. Tombol **Tambah** dan kolom **Aksi**
disembunyikan, serta teks `Masuk sebagai: user`. Membuka `form_tambah.html` atau
`form_edit.html` langsung akan ditolak dengan pesan akses ditolak. Proteksi
utama tetap di backend (`Auth::requireAdmin()`), bukan di tampilan.

## Cara membuat akun user

Tidak perlu mengubah basis data; role `user` diberikan otomatis.

```json
POST {base_url}/register
{
  "username": "user_test",
  "email": "user_test@example.com",
  "password": "Rahasia123!"
}
```
Respons sukses (201):
```json
{ "success": true, "message": "Registrasi berhasil", "data": { "id": 2 } }
```
Postman: request **S-01 Daftar akun user_test**.

## Cara login

### Lewat browser
1. Buka `http://localhost/<folder-proyek>/sistem-informasi-akademik/public/views/login.html`
2. Isi username `user_test` dan password `Rahasia123!`, klik **Masuk**.
3. Berhasil: diarahkan ke `dashboard.html` dengan tampilan hanya-lihat.

### Lewat Postman / API
```json
POST {base_url}/login
{
  "username": "user_test",
  "password": "Rahasia123!"
}
```
Respons sukses (200):
```json
{
  "success": true,
  "message": "Login berhasil",
  "data": { "token": "<jwt>", "role": "user" }
}
```

## Pengujian (Postman: Acara25_RBAC_Testing)

| Kode | Skenario | Hasil yang diharapkan |
|------|----------|-----------------------|
| TC-20 | Login user | 200, `role` = `user` |
| TC-22 | User melihat data | 200 OK |
| TC-23 | User tambah data | 403 Forbidden |
| TC-24 | User ubah data | 403 Forbidden |
| TC-25 | User hapus data | 403 Forbidden |
| TC-26 | Tambah data tanpa token | 401 Unauthorized |

Pesan 403: `Akses ditolak, hanya admin yang diizinkan`.

## Kemungkinan error

| Status | Penyebab |
|--------|----------|
| 400 | Username, email, atau password kosong |
| 401 | Username atau password salah / token tidak ada, tidak valid, atau kedaluwarsa |
| 403 | Role `user` mencoba tambah, ubah, atau hapus data |
| 409 | Username atau email sudah terdaftar |
| 422 | Format email tidak valid |

> **Catatan keamanan:** password contoh dan `Auth::SECRET` hanya untuk praktik
> lokal. Jangan dipakai di server produksi.
