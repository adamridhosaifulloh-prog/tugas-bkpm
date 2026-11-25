# Jadwal Backup Basis Data (Tugas Praktik Mandiri 2 - Acara 26)

Proses backup basis data `db_workshop` dijadwalkan pada **setiap akhir sesi praktikum**
(setiap pertemuan, sebelum laptop dimatikan / praktikum ditutup).

| Item | Keterangan |
|------|------------|
| Waktu | Akhir setiap sesi praktikum (Acara 26 dan seterusnya) |
| Basis data | `db_workshop` (tabel `users`, `mahasiswa`, `buku`) |
| Metode manual | phpMyAdmin > db_workshop > Export > Quick > format SQL |
| Metode cepat | Jalankan `scripts/backup_db.bat` (mysqldump XAMPP) |
| Nama berkas | `db_workshop_YYYY-MM-DD_HHMM.sql` |
| Lokasi simpan | `database/backup/` lalu di-commit / disalin ke GitHub atau flashdisk |
| Verifikasi | Import ke basis data percobaan, cek tabel `users` dan `mahasiswa` |
| Catatan | Foto di `public/uploads/` bukan bagian dari berkas SQL, salin foldernya juga |

## Checklist akhir sesi
1. [ ] Export `db_workshop` ke berkas `.sql` bertanggal
2. [ ] Simpan di `database/backup/`
3. [ ] Salin folder `public/uploads/`
4. [ ] Commit dan push ke GitHub
