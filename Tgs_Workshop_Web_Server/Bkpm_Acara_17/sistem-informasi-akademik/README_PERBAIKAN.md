# Perbaikan Acara 17 — Sistem Informasi Akademik

## Perbaikan utama
- Menambahkan `app/core/Response.php` yang dibutuhkan oleh Router dan Controller.
- Menambahkan `app/config/app.php` agar autoloader dapat memuat konfigurasi lingkungan.
- Response API konsisten menggunakan JSON: `success`, `message`, `data`, `errors`, `timestamp`.
- Format JSON pretty hanya aktif pada `APP_ENV = 'development'`.
- Fitur CRUD Mahasiswa tetap tersedia.
- Tugas Praktik Mandiri: CRUD Buku dan validasi NIM agar tidak duplikat tetap disertakan.

## Cara menjalankan
1. Ekstrak folder `sistem-informasi-akademik` ke `C:\XAMPP\htdocs\`.
2. Jalankan Apache dan MySQL melalui XAMPP.
3. Buka phpMyAdmin, lalu import `database/db_workshop.sql`.
   **Catatan:** SQL ini menggunakan `DROP TABLE IF EXISTS`, jadi data lama pada tabel `mahasiswa` dan `buku` akan dihapus saat file SQL diimpor ulang.
4. Buka `app/config/Database.php`; default database adalah `db_workshop`, user `root`, password kosong.
5. Buka:
   - `http://localhost/sistem-informasi-akademik/public/mahasiswa`
   - `http://localhost/sistem-informasi-akademik/public/buku`

## Uji API melalui Postman
Pilih Body > raw > JSON untuk request POST/PUT.

### Mahasiswa
- GET `http://localhost/sistem-informasi-akademik/public/mahasiswa`
- GET `http://localhost/sistem-informasi-akademik/public/mahasiswa?id=1`
- POST `http://localhost/sistem-informasi-akademik/public/mahasiswa`
  ```json
  {"nim":"230012345","nama":"Mahasiswa Uji","prodi":"Teknik Informatika"}
  ```
- PUT `http://localhost/sistem-informasi-akademik/public/mahasiswa?id=1`
- DELETE `http://localhost/sistem-informasi-akademik/public/mahasiswa?id=1`

### Buku (tugas praktik mandiri)
- GET `http://localhost/sistem-informasi-akademik/public/buku`
- POST `http://localhost/sistem-informasi-akademik/public/buku`
  ```json
  {"judul":"Buku Uji","penulis":"Penulis Uji","penerbit":"Penerbit Uji","tahun_terbit":2024}
  ```
- PUT `http://localhost/sistem-informasi-akademik/public/buku?id=1`
- DELETE `http://localhost/sistem-informasi-akademik/public/buku?id=1`

## Status validasi
Semua file PHP sudah diperiksa dengan `php -l` dan tidak ditemukan kesalahan sintaks. Pengujian HTTP langsung tetap perlu dilakukan pada XAMPP milik pengguna.
