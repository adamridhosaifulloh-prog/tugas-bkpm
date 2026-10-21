# Acara 18 — JSON Encoding dan HTTP Status Code

## Perubahan dari Acara 17
- Menambahkan `app/core/Response.php` untuk response JSON standar: `success`, `message`, `data`, `errors`, dan `timestamp`.
- `JSON_PRETTY_PRINT` hanya aktif saat `APP_ENV` bernilai `development`.
- Router dan penanganan kegagalan koneksi database menggunakan response standar.
- Validasi mahasiswa dan buku mengembalikan HTTP 422 serta rincian kesalahan per field.
- Model, endpoint CRUD, dan struktur utama Acara 17 dipertahankan.

## Menjalankan
1. Ekstrak folder `sistem-informasi-akademik` ke `C:\xampp\htdocs\`.
2. Buka XAMPP, nyalakan Apache dan MySQL.
3. Buka `http://localhost/phpmyadmin`.
4. Import `database/db_workshop.sql` (file membuat database `db_workshop` dan tabel mahasiswa/buku).
5. Buka `http://localhost/sistem-informasi-akademik/public/mahasiswa` untuk menguji GET. Jika nama folder berbeda, sesuaikan URL.
6. Uji POST/PUT/DELETE melalui Postman; header `Content-Type: application/json`, body JSON.
7. Import collection `postman/Acara17_API_CRUD_MVC.postman_collection.json` ke Postman. Atur variable `base_url` menjadi `http://localhost/sistem-informasi-akademik/public`.

## Skenario pengujian
- GET `{{base_url}}/mahasiswa` → 200.
- GET `{{base_url}}/mahasiswa?id=999999` → 404.
- POST `{{base_url}}/mahasiswa` dengan data valid → 201.
- POST dengan field kosong → 422 dan object `errors` berisi field yang gagal.
- POST NIM duplikat → 409.
- PUT `{{base_url}}/mahasiswa?id=1` dengan JSON valid → 200; ID yang tidak ada → 404.
- DELETE `{{base_url}}/mahasiswa?id=1` → 200; ID tidak ada → 404.
- GET `{{base_url}}/endpoint-tidak-ada` → 404.
- Kirim method PATCH ke endpoint → 405.
- Ubah `APP_ENV` pada `app/config/app.php` dari `development` ke `production`, lalu response JSON tidak memakai indentasi `JSON_PRETTY_PRINT`.

## Tugas mandiri
1. Field `errors` disediakan sebagai object JSON agar detail validasi per field dapat dibaca client.
2. Flag `JSON_PRETTY_PRINT` dikontrol oleh konstanta `APP_ENV` pada `app/config/app.php`.
