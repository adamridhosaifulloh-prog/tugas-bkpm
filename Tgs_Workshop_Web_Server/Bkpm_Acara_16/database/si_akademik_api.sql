-- Database Acara 16: RESTful API Data Mahasiswa
CREATE DATABASE IF NOT EXISTS si_akademik_api CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE si_akademik_api;

DROP TABLE IF EXISTS mahasiswa;
CREATE TABLE mahasiswa (
  id    INT(11)      NOT NULL AUTO_INCREMENT PRIMARY KEY,
  nim   VARCHAR(20)  NOT NULL,
  nama  VARCHAR(100) NOT NULL,
  email VARCHAR(100) NOT NULL,
  UNIQUE KEY uq_mahasiswa_nim (nim)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO mahasiswa (id, nim, nama, email) VALUES
(1, '23001', 'Budi', 'budi@gmail.com'),
(2, '23002', 'Siti', 'siti@gmail.com'),
(3, '23003', 'Andi', 'andi@gmail.com'),
(4, '23004', 'Dewi', 'dewi@gmail.com');
