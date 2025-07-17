-- Membuat database jika belum ada
CREATE DATABASE IF NOT EXISTS db_siw CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Menggunakan database db_siw
USE db_siw;

-- Membuat tabel untuk kategori barang
-- Tabel ini berfungsi sebagai tabel induk (master)
CREATE TABLE tabel_kategori (
    id_kategori INT AUTO_INCREMENT PRIMARY KEY,
    nama_kategori VARCHAR(100) NOT NULL
) ENGINE=InnoDB;

-- Membuat tabel untuk barang inventaris
-- Tabel ini memiliki relasi one-to-many dengan tabel_kategori
CREATE TABLE tabel_barang (
    id_barang INT AUTO_INCREMENT PRIMARY KEY,
    nama_barang VARCHAR(255) NOT NULL,
    id_kategori INT NOT NULL,
    harga_beli DECIMAL(10, 0) NOT NULL,
    harga_jual DECIMAL(10, 0) NOT NULL,
    stok INT NOT NULL,
    -- Mendefinisikan foreign key constraint
    -- ON DELETE RESTRICT: Mencegah penghapusan kategori jika masih ada barang yang terkait.
    -- ON UPDATE CASCADE: Jika id_kategori di tabel induk berubah, id_kategori di tabel anak akan ikut berubah.
    FOREIGN KEY (id_kategori) 
        REFERENCES tabel_kategori(id_kategori) 
        ON DELETE RESTRICT 
        ON UPDATE CASCADE
) ENGINE=InnoDB;

-- Memasukkan beberapa data contoh untuk memulai
INSERT INTO tabel_kategori (nama_kategori) VALUES 
('Makanan Ringan'), 
('Minuman'), 
('Sembako'),
('Perlengkapan Mandi');

INSERT INTO tabel_barang (nama_barang, id_kategori, harga_beli, harga_jual, stok) VALUES
('Indomie Goreng', 3, 2500, 3000, 100),
('Teh Botol Sosro 250ml', 2, 3000, 3500, 50),
('Chitato Sapi Panggang 68g', 1, 8000, 10000, 30),
('Beras Rojolele 5kg', 3, 60000, 65000, 15),
('Sabun Lifebuoy Total 10', 4, 3500, 4000, 40),
('Kopi Kapal Api Special Mix', 2, 1000, 1500, 200),
('Minyak Goreng Sania 2L', 3, 32000, 35000, 20),
('Lays Rumput Laut 55g', 1, 9000, 11000, 4);