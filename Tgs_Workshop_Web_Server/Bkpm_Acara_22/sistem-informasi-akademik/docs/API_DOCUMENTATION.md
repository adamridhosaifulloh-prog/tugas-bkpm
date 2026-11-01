# Dokumentasi API — Sistem Informasi Akademik (MVC)

Acara 21 · BKPM Workshop SI Web Server (TIF330805)

**Base URL (lokal):** `http://localhost/<path-proyek>/sistem-informasi-akademik/public`
**Format data:** JSON (`Content-Type: application/json`)
**Autentikasi:** JWT Bearer Token, masa berlaku 1 jam (3600 detik)

---

## 1. Ringkasan Seluruh Endpoint

| No | Method | URL | Controller::method | Autentikasi | Keterangan |
|----|--------|-----|--------------------|-------------|------------|
| 1 | POST | `/register` | `AuthController::register()` | Publik | Pendaftaran pengguna baru |
| 2 | POST | `/login` | `AuthController::login()` | Publik | Login, menghasilkan token JWT |
| 3 | GET | `/mahasiswa` | `MahasiswaController::index()` | Publik | Daftar seluruh mahasiswa |
| 4 | GET | `/mahasiswa?id={id}` | `MahasiswaController::index()` | Publik | Detail satu mahasiswa |
| 5 | POST | `/mahasiswa` | `MahasiswaController::store()` | **Bearer Token** | Tambah mahasiswa |
| 6 | PUT | `/mahasiswa?id={id}` | `MahasiswaController::update()` | **Bearer Token** | Ubah mahasiswa |
| 7 | DELETE | `/mahasiswa?id={id}` | `MahasiswaController::destroy()` | **Bearer Token** | Hapus mahasiswa |
| 8 | GET | `/buku` | `BukuController::index()` | Publik | Daftar seluruh buku |
| 9 | GET | `/buku?id={id}` | `BukuController::index()` | Publik | Detail satu buku |
| 10 | POST | `/buku` | `BukuController::store()` | Publik | Tambah buku |
| 11 | PUT | `/buku?id={id}` | `BukuController::update()` | Publik | Ubah buku |
| 12 | DELETE | `/buku?id={id}` | `BukuController::destroy()` | Publik | Hapus buku |

> Catatan: pada kode saat ini endpoint `/buku` belum memanggil `Auth::requireAuth()`, sehingga seluruh method-nya bersifat publik.

## 2. Peta Router

`Router::dispatch()` mengambil segmen terakhir path URL sebagai nama *resource*, lalu memetakannya ke Controller.

| Resource (URL) | Controller | Keterangan |
|----------------|------------|------------|
| `/mahasiswa` | `MahasiswaController` | CRUD data mahasiswa (index/store/update/destroy) |
| `/buku` | `BukuController` | CRUD data buku |
| `/login` | `AuthController::login` | Autentikasi pengguna, menghasilkan token |
| `/register` | `AuthController::register` | Pendaftaran pengguna baru |

Pemetaan method HTTP pada resource CRUD:

| Method HTTP | Aksi Controller |
|-------------|-----------------|
| GET | `index()` |
| POST | `store()` |
| PUT | `update($id)` (id dari query string `?id=`) |
| DELETE | `destroy($id)` (id dari query string `?id=`) |

Method lain dijawab `405` dengan header `Allow: GET, POST, PUT, DELETE`. Resource `/login` dan `/register` hanya menerima `POST` (header `Allow: POST`). Resource yang tidak terdaftar dijawab `404 Endpoint tidak ditemukan`.

## 3. Format Response Standar

Seluruh response dibentuk oleh `Response::json()`:

```json
{
  "success": true,
  "message": "Pesan hasil proses",
  "data": null,
  "errors": {},
  "timestamp": "2026-10-25T10:00:00+07:00"
}
```

| Field | Tipe | Keterangan |
|-------|------|------------|
| `success` | boolean | `true` bila berhasil, `false` bila gagal |
| `message` | string | Pesan hasil proses |
| `data` | object / array / null | Data hasil proses |
| `errors` | object | Pesan error per field (kosong `{}` bila tidak ada) |
| `timestamp` | string | Waktu response (format ISO 8601) |

## 4. Autentikasi

Endpoint terproteksi memerlukan header:

```
Authorization: Bearer <token>
```

Token diperoleh dari `POST /login`. Kegagalan autentikasi pada endpoint terproteksi:

| Kondisi | Status | `message` |
|---------|--------|-----------|
| Header Authorization tidak ada / format salah | 401 | Token tidak ditemukan |
| Tanda tangan salah atau token kedaluwarsa | 401 | Token tidak valid atau kedaluwarsa |

---

## 5. Endpoint Autentikasi

### POST /register

**Controller:** `AuthController::register()` · **Akses:** Publik

**Headers:** `Content-Type: application/json`

**Request Body:**

| Field | Tipe | Wajib | Keterangan |
|-------|------|-------|------------|
| `username` | string | Ya | Harus unik |
| `email` | string | Ya | Format email valid, harus unik |
| `password` | string | Ya | Disimpan dalam bentuk hash |

```json
{ "username": "adam_test", "email": "adam_test@example.com", "password": "Rahasia123!" }
```

**Response 201 Created:**
```json
{
  "success": true,
  "message": "Registrasi berhasil",
  "data": { "id": "1" },
  "errors": {},
  "timestamp": "2026-10-25T10:00:00+07:00"
}
```

**Response gagal:**

| Status | Penyebab | `message` |
|--------|----------|-----------|
| 400 | Body bukan JSON valid | Body request harus berupa JSON yang valid |
| 400 | Field wajib kosong | Username, email, dan password wajib diisi |
| 422 | Email tidak valid | Format email tidak valid (`errors.email`) |
| 409 | Username/email sudah ada | Username sudah digunakan / Username atau email sudah digunakan |

---

### POST /login

**Controller:** `AuthController::login()` · **Akses:** Publik

**Headers:** `Content-Type: application/json`

**Request Body:**

| Field | Tipe | Wajib |
|-------|------|-------|
| `username` | string | Ya |
| `password` | string | Ya |

```json
{ "username": "adam_test", "password": "Rahasia123!" }
```

**Response 200 OK:**
```json
{
  "success": true,
  "message": "Login berhasil",
  "data": { "token": "<header>.<payload>.<signature>", "role": "user" },
  "errors": {},
  "timestamp": "2026-10-25T10:00:00+07:00"
}
```

**Response gagal:**

| Status | Penyebab | `message` |
|--------|----------|-----------|
| 400 | Username/password kosong atau body tidak valid | Username dan password wajib diisi |
| 401 | Kredensial salah | Username atau password salah |
| 405 | Method selain POST | Method tidak diizinkan |

---

## 6. Endpoint Mahasiswa

Controller: `MahasiswaController`. Tabel: `mahasiswa`.

**Field data:**

| Field | Tipe | Aturan |
|-------|------|--------|
| `nim` | string | Wajib, tepat 9 digit angka, unik |
| `nama` | string | Wajib, maksimal 100 karakter |
| `prodi` | string | Wajib, maksimal 50 karakter |

### GET /mahasiswa

**Controller:** `MahasiswaController::index()` · **Akses:** Publik

**Response 200 OK:**
```json
{
  "success": true,
  "message": "Data berhasil diambil",
  "data": [
    { "id": 1, "nim": "220100201", "nama": "Siti Aminah", "prodi": "Teknik Informatika", "created_at": "2026-10-21 08:00:00" }
  ],
  "errors": {},
  "timestamp": "2026-10-25T10:00:00+07:00"
}
```

### GET /mahasiswa?id={id}

**Controller:** `MahasiswaController::index()` · **Akses:** Publik

| Status | Kondisi | `message` |
|--------|---------|-----------|
| 200 | Data ditemukan | Data berhasil ditemukan (`data` berisi satu objek) |
| 400 | `id` bukan angka positif | ID harus berupa angka positif |
| 404 | `id` tidak ada | Data tidak ditemukan |

### POST /mahasiswa

**Controller:** `MahasiswaController::store()` · **Akses:** Bearer Token

**Headers:** `Content-Type: application/json`, `Authorization: Bearer <token>`

```json
{ "nim": "220100201", "nama": "Andi Saputra", "prodi": "Teknik Informatika" }
```

**Response 201 Created:**
```json
{
  "success": true,
  "message": "Data berhasil ditambahkan",
  "data": { "id": 4, "nim": "220100201", "nama": "Andi Saputra", "prodi": "Teknik Informatika" },
  "errors": {},
  "timestamp": "2026-10-25T10:00:00+07:00"
}
```

**Response gagal:**

| Status | Penyebab | `message` |
|--------|----------|-----------|
| 401 | Token tidak ada / tidak valid | Token tidak ditemukan / Token tidak valid atau kedaluwarsa |
| 400 | Body kosong atau JSON rusak | Body request kosong... / Format JSON tidak valid |
| 422 | Validasi field gagal | Data yang dikirim tidak valid |
| 409 | NIM sudah terdaftar | NIM {nim} sudah terdaftar |

Contoh **422**:
```json
{
  "success": false,
  "message": "Data yang dikirim tidak valid",
  "data": null,
  "errors": { "nim": ["NIM harus terdiri dari tepat 9 digit angka"], "nama": ["Nama wajib diisi"] },
  "timestamp": "2026-10-25T10:00:00+07:00"
}
```

### PUT /mahasiswa?id={id}

**Controller:** `MahasiswaController::update()` · **Akses:** Bearer Token

Body sama dengan `POST /mahasiswa`.

| Status | Kondisi | `message` |
|--------|---------|-----------|
| 200 | Berhasil | Data berhasil diperbarui |
| 401 | Token bermasalah | Token tidak ditemukan / tidak valid |
| 400 | `id` tidak ada / bukan angka | Parameter id wajib disertakan, contoh: ?id=1 / ID harus berupa angka positif |
| 404 | `id` tidak ditemukan | Data tidak ditemukan |
| 422 | Validasi gagal | Data yang dikirim tidak valid |
| 409 | NIM dipakai mahasiswa lain | NIM {nim} sudah dipakai mahasiswa lain |

### DELETE /mahasiswa?id={id}

**Controller:** `MahasiswaController::destroy()` · **Akses:** Bearer Token

**Response 200 OK:**
```json
{ "success": true, "message": "Data berhasil dihapus", "data": null, "errors": {}, "timestamp": "2026-10-25T10:00:00+07:00" }
```

Response gagal: 401 (token), 400 (id tidak valid), 404 (data tidak ditemukan).

---

## 7. Endpoint Buku

Controller: `BukuController`. Tabel: `buku`. Seluruh method saat ini **publik**.

**Field data:**

| Field | Tipe | Aturan |
|-------|------|--------|
| `judul` | string | Wajib, maksimal 150 karakter |
| `penulis` | string | Wajib, maksimal 100 karakter |
| `penerbit` | string | Wajib, maksimal 100 karakter |
| `tahun_terbit` | integer | Wajib, antara 1900 dan tahun depan |

| Method & URL | Controller::method | Status sukses | Status gagal |
|--------------|--------------------|---------------|--------------|
| GET `/buku` | `index()` | 200 | – |
| GET `/buku?id={id}` | `index()` | 200 | 400, 404 |
| POST `/buku` | `store()` | 201 | 400 |
| PUT `/buku?id={id}` | `update()` | 200 | 400, 404 |
| DELETE `/buku?id={id}` | `destroy()` | 200 | 400, 404 |

Contoh body `POST /buku` dan `PUT /buku?id=1`:
```json
{ "judul": "Pemrograman Web dengan PHP", "penulis": "Rahmat Hidayat", "penerbit": "Informatika", "tahun_terbit": 2021 }
```

Contoh **400** (validasi `tahun_terbit`):
```json
{ "success": false, "message": "tahun_terbit harus berupa angka tahun yang valid", "data": null, "errors": {}, "timestamp": "2026-10-25T10:00:00+07:00" }
```

---

## 8. Kode Status HTTP yang Digunakan

| Kode | Arti | Contoh penggunaan |
|------|------|-------------------|
| 200 | OK | Data berhasil diambil, diubah, dihapus; login berhasil |
| 201 | Created | Data berhasil ditambahkan; registrasi berhasil |
| 400 | Bad Request | Body kosong/rusak, `id` tidak valid |
| 401 | Unauthorized | Token tidak ada, palsu, atau kedaluwarsa |
| 404 | Not Found | Data atau endpoint tidak ditemukan |
| 405 | Method Not Allowed | Method HTTP tidak didukung |
| 409 | Conflict | NIM/username/email duplikat |
| 422 | Unprocessable Entity | Validasi field gagal |
| 500 | Internal Server Error | Kesalahan database (`PDOException`) |
