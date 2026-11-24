-- Acara 26 Langkah 1: kolom foto pada tabel mahasiswa
USE db_workshop;
ALTER TABLE mahasiswa ADD COLUMN foto VARCHAR(255) DEFAULT NULL AFTER prodi;
