-- Database Acara 17: API CRUD dengan PHP Native (MVC)
CREATE DATABASE IF NOT EXISTS db_workshop CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE db_workshop;

-- ---------- Resource 1: mahasiswa ----------
DROP TABLE IF EXISTS mahasiswa;
CREATE TABLE mahasiswa (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nim VARCHAR(15) NOT NULL,
  nama VARCHAR(100) NOT NULL,
  prodi VARCHAR(50) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uq_mahasiswa_nim (nim)
);

INSERT INTO mahasiswa (nim, nama, prodi) VALUES
('2201002', 'Siti Aminah', 'Teknik Informatika'),
('2201003', 'Budi Hartono', 'Manajemen Informatika'),
('2201004', 'Dewi Lestari', 'Teknik Informatika');

-- ---------- Resource 2: buku (Tugas Praktik Mandiri 1) ----------
DROP TABLE IF EXISTS buku;
CREATE TABLE buku (
  id INT AUTO_INCREMENT PRIMARY KEY,
  judul VARCHAR(150) NOT NULL,
  penulis VARCHAR(100) NOT NULL,
  penerbit VARCHAR(100) NOT NULL,
  tahun_terbit SMALLINT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO buku (judul, penulis, penerbit, tahun_terbit) VALUES
('Pemrograman Web dengan PHP', 'Rahmat Hidayat', 'Informatika', 2021),
('Basis Data Relasional', 'Sari Wulandari', 'Andi Offset', 2020);


-- ---------- Resource 3: users (Acara 19 - Registrasi)
CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) NOT NULL UNIQUE,
  email VARCHAR(100) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  role ENUM('admin', 'user') DEFAULT 'user',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
