-- Membuat database jika belum ada
CREATE DATABASE IF NOT EXISTS db_siw CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Menggunakan database db_siw
USE db_siw;

-- 1. Tabel Pengguna
DROP TABLE IF EXISTS tabel_pengguna;
CREATE TABLE tabel_pengguna (
    id_pengguna INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    nama_lengkap VARCHAR(100) NOT NULL
) ENGINE=InnoDB;

-- 2. Tabel Kategori
DROP TABLE IF EXISTS tabel_kategori;
CREATE TABLE tabel_kategori (
    id_kategori INT AUTO_INCREMENT PRIMARY KEY,
    nama_kategori VARCHAR(100) NOT NULL
) ENGINE=InnoDB;

-- 3. Tabel Barang
DROP TABLE IF EXISTS tabel_barang;
CREATE TABLE tabel_barang (
    id_barang INT AUTO_INCREMENT PRIMARY KEY,
    nama_barang VARCHAR(255) NOT NULL,
    id_kategori INT NOT NULL,
    harga_beli DECIMAL(10, 2) NOT NULL,
    harga_jual DECIMAL(10, 2) NOT NULL,
    stok INT NOT NULL,
    is_deleted TINYINT(1) NOT NULL DEFAULT 0,
    FOREIGN KEY (id_kategori) REFERENCES tabel_kategori(id_kategori) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB;

-- 4. Tabel Penjualan (Header)
DROP TABLE IF EXISTS tabel_penjualan;
CREATE TABLE tabel_penjualan (
    id_penjualan INT AUTO_INCREMENT PRIMARY KEY,
    tanggal_penjualan TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    total_harga DECIMAL(10, 2) NOT NULL,
    id_pengguna INT NOT NULL,
    FOREIGN KEY (id_pengguna) REFERENCES tabel_pengguna(id_pengguna)
) ENGINE=InnoDB;

-- 5. Tabel Detail Penjualan (Detail)
DROP TABLE IF EXISTS tabel_detail_penjualan;
CREATE TABLE tabel_detail_penjualan (
    id_detail INT AUTO_INCREMENT PRIMARY KEY,
    id_penjualan INT NOT NULL,
    id_barang INT NOT NULL,
    jumlah INT NOT NULL,
    harga_saat_transaksi DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (id_penjualan) REFERENCES tabel_penjualan(id_penjualan) ON DELETE CASCADE,
    FOREIGN KEY (id_barang) REFERENCES tabel_barang(id_barang) ON DELETE RESTRICT
) ENGINE=InnoDB;

-- 6. Tabel Riwayat Perubahan Stok (Stock Log)
DROP TABLE IF EXISTS tabel_stock_log;
CREATE TABLE tabel_stock_log (
    id_log INT AUTO_INCREMENT PRIMARY KEY,
    id_barang INT NULL,
    perubahan INT NOT NULL,
    stok_sebelum INT NOT NULL,
    stok_sesudah INT NOT NULL,
    keterangan VARCHAR(255) NOT NULL,
    tanggal_log TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    id_pengguna INT,
    FOREIGN KEY (id_barang) REFERENCES tabel_barang(id_barang) ON DELETE SET NULL,
    FOREIGN KEY (id_pengguna) REFERENCES tabel_pengguna(id_pengguna)
) ENGINE=InnoDB;

-- Memasukkan data contoh
-- Kata sandi untuk 'admin' adalah 'password123'
INSERT INTO tabel_pengguna (username, password, nama_lengkap) VALUES 
('admin', '$2y$10$ZYgh1ajD2w5gS9BcGc5.WeYoXquiCDDKuYvYNRM23zm8nOW/df0uW', 'Pemilik Warung');

INSERT INTO tabel_kategori (nama_kategori) VALUES 
('Makanan Ringan'), ('Minuman'), ('Sembako'), ('Perlengkapan Mandi');

INSERT INTO tabel_barang (nama_barang, id_kategori, harga_beli, harga_jual, stok) VALUES
('Indomie Goreng', 3, 2500.00, 3000.00, 100),
('Teh Botol Sosro 250ml', 2, 3000.00, 3500.00, 50),
('Chitato Sapi Panggang 68g', 1, 8000.00, 10000.00, 30),
('Beras Rojolele 5kg', 3, 60000.00, 65000.00, 15),
('Sabun Lifebuoy Total 10', 4, 3500.00, 4000.00, 40),
('Kopi Kapal Api Special Mix', 2, 1000.00, 1500.00, 200),
('Minyak Goreng Sania 2L', 3, 32000.00, 35000.00, 20),
('Lays Rumput Laut 55g', 1, 9000.00, 11000.00, 4);