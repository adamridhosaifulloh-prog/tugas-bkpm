# Test Case — Blackbox Testing API Sistem Informasi Akademik

Acara 23 · BKPM Workshop SI Web Server (TIF330805)

## 1. Ruang Lingkup

Pengujian blackbox dengan teknik **Equivalence Partitioning** terhadap endpoint berikut (berdasarkan peta Router):

| Endpoint | Method | Controller::method |
|---|---|---|
| `/register` | POST | `AuthController::register()` |
| `/login` | POST | `AuthController::login()` |
| `/mahasiswa` | GET | `MahasiswaController::index()` |
| `/mahasiswa` | POST | `MahasiswaController::store()` |
| `/mahasiswa?id={id}` | PUT | `MahasiswaController::update()` |
| `/mahasiswa?id={id}` | DELETE | `MahasiswaController::destroy()` |

Test case di bawah memfokuskan login, store, update, dan destroy. Endpoint `/register` dan GET mahasiswa sudah diuji pada Acara 19–20.

## 2. Lingkungan dan Prasyarat

- XAMPP (Apache + MySQL) aktif, database `db_workshop` sudah diimpor.
- Postman dengan environment **Local** (`base_url`, `auth_username`, `auth_password`, `token`).
- Akun uji (`auth_username` / `auth_password`) sudah teregistrasi.
- Collection: `Acara23_Blackbox_Testing.postman_collection.json`, dijalankan berurutan lewat Collection Runner.

## 3. Partisi Ekuivalensi

| Data Masukan | Partisi Valid | Partisi Tidak Valid |
|---|---|---|
| `nim` | Tepat 9 digit angka, belum terdaftar | Kosong; kurang/lebih dari 9 digit; sudah terdaftar |
| `nama` | 1–100 karakter | Kosong; lebih dari 100 karakter |
| `prodi` | 1–50 karakter | Kosong; lebih dari 50 karakter |
| `id` (query) | Angka positif dan data ada | Tidak ada; bukan angka; kosong; data tidak ditemukan |
| Token | `Bearer` + JWT valid | Tidak ada; palsu; kedaluwarsa |
| Kredensial login | Username & password benar | Password salah |

## 4. Daftar Test Case

Kolom **Hasil Aktual** dan **Status** diisi setelah test case dieksekusi di Postman (kode status HTTP yang benar-benar diterima).

| ID | Skenario | Partisi | Data Uji | Hasil Diharapkan | Hasil Aktual | Status |
|---|---|---|---|---|---|---|
| TC-04 | Login dengan kredensial benar (AuthController::login) | Valid | username & password benar | 200 OK | | |
| TC-05 | Login dengan password salah | Tidak valid | password salah | 401 Unauthorized | | |
| TC-06 | Akses store tanpa token (Auth::requireAuth) | Tidak valid | tanpa header Authorization | 401 Unauthorized | | |
| TC-01 | Tambah mahasiswa dengan NIM valid dan lengkap (store) | Valid | nim=220100123 | 201 Created | | |
| TC-02 | Tambah mahasiswa dengan NIM kosong | Tidak valid | nim="" | 422 Unprocessable Entity | | |
| TC-03 | Tambah mahasiswa dengan NIM duplikat | Tidak valid | nim milik data lain (hasil TC-01) | 409 Conflict | | |
| S-01 | (Setup) Tambah mahasiswa kedua sebagai data uji NIM bentrok | Valid | nim=220100124 | 201 Created | | |
| TC-07 | Ubah mahasiswa dengan data valid (update) | Valid | id ada, NIM sendiri tidak berubah | 200 OK | | |
| TC-08 | Ubah NIM menjadi NIM milik mahasiswa lain | Tidak valid | nim=220100124 (milik id lain) | 409 Conflict | | |
| TC-09 | Ubah dengan NIM kurang dari 9 digit | Tidak valid | nim 8 digit | 422 Unprocessable Entity | | |
| TC-10 | Ubah data dengan id yang tidak ada | Tidak valid | id=999999 | 404 Not Found | | |
| TC-11 | Ubah data dengan id non-numerik | Tidak valid | id=abc | 400 Bad Request | | |
| TC-12 | Ubah data tanpa token | Tidak valid | tanpa header Authorization | 401 Unauthorized | | |
| TC-13 | Ubah dengan nama kosong | Tidak valid | nama="" | 422 Unprocessable Entity | | |
| TC-14 | Hapus mahasiswa dengan id valid (destroy) | Valid | id hasil S-01 | 200 OK | | |
| TC-15 | Hapus id yang sudah dihapus | Tidak valid | id hasil TC-14 | 404 Not Found | | |
| TC-16 | Hapus dengan id non-numerik | Tidak valid | id=abc | 400 Bad Request | | |
| TC-17 | Hapus tanpa parameter id | Tidak valid | tanpa ?id= | 400 Bad Request | | |
| TC-18 | Hapus tanpa token | Tidak valid | tanpa header Authorization | 401 Unauthorized | | |
| TC-19 | Hapus data hasil TC-01 (pembersihan, sekaligus uji destroy valid) | Valid | id hasil TC-01 | 200 OK | | |

## 5. Cara Eksekusi

1. Import `Acara23_Blackbox_Testing.postman_collection.json` dan pilih environment **Local**.
2. Klik **Run collection** (Collection Runner) dengan urutan bawaan, lalu **Run**.
3. Setiap request memiliki skrip `pm.test` yang otomatis menandai Pass/Fail terhadap hasil diharapkan.
4. Salin kode status aktual dari hasil run ke tabel di atas, lalu isi kolom Status (Pass bila sama dengan yang diharapkan).
5. Simpan tangkapan layar hasil Collection Runner sebagai bukti.

## 6. Catatan

- **TC-03 (NIM duplikat):** modul menuliskan hasil diharapkan 422, sedangkan implementasi (`Mahasiswa::create` melempar `DomainException`, ditangani sebagai `fail(409, ...)`) dan dokumentasi API Acara 21/22 memakai **409 Conflict**. Tabel ini memakai 409 karena lebih tepat secara semantik untuk konflik data duplikat.
- Data awal pada `db_workshop.sql` memakai NIM 7 digit, sedangkan validasi sekarang mewajibkan 9 digit. Karena itu test case update memakai data yang dibuat sendiri pada TC-01, bukan data bawaan.
- Test case dijalankan berurutan karena TC-07 sampai TC-19 memakai id hasil TC-01 dan S-01. Test case terakhir (TC-19) menghapus data uji sehingga pengujian dapat diulang.

## 7. Ringkasan Hasil

| Total | Pass | Fail |
|---|---|---|
| 19 | … | … |
