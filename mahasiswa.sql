-- ============================================================
-- Database: db_mahasiswa
-- Tabel: mahasiswa
-- Untuk project UTS Pemrograman Web Lanjut - Yusran
-- ============================================================

CREATE DATABASE IF NOT EXISTS db_mahasiswa;
USE db_mahasiswa;

CREATE TABLE IF NOT EXISTS mahasiswa (
    id    INT AUTO_INCREMENT PRIMARY KEY,
    nama  VARCHAR(100) NOT NULL,
    prodi VARCHAR(100) NOT NULL
);

-- Data contoh
INSERT INTO mahasiswa (nama, prodi) VALUES
('Yusran',          'Informatika'),
('Budi Santoso',    'Sistem Informasi'),
('Citra Dewi',      'Informatika'),
('Dimas Prasetyo',  'Teknik Elektro');
