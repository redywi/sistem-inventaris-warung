# Sistem Inventaris Warung (SIW)

**SIW** adalah aplikasi web sederhana berbasis PHP & MySQL untuk membantu pengelolaan inventaris barang dan kategori di warung/toko kecil.  
Aplikasi ini cocok untuk UMKM yang ingin melakukan pencatatan stok barang secara digital dan efisien.

## ✨ Fitur Utama

- **Manajemen Barang:**  
  Tambah, edit, hapus, dan lihat daftar barang beserta stok, harga beli, harga jual, dan kategori.

- **Manajemen Kategori:**  
  Kelola kategori barang agar data lebih terstruktur.

- **Notifikasi Otomatis:**  
  Pesan sukses/gagal otomatis tampil setelah aksi CRUD.

- **API CRUD Barang:**  
  Endpoint API untuk integrasi eksternal (Postman-ready).

- **Desain Responsive:**  
  Tampilan sederhana dan mudah digunakan di berbagai perangkat.

## 🚀 Cara Instalasi

1. **Clone atau Download** repository ini ke folder `htdocs` XAMPP Anda.
2. Buat database MySQL, lalu import file SQL (jika tersedia).
3. Edit file `config/database.php` sesuai konfigurasi database Anda.
4. Jalankan XAMPP, lalu akses di browser:  
   `http://localhost/sistem-inventaris-warung/public/`

## 📦 Struktur Folder

- `public/` — Semua file frontend (dashboard, daftar barang, kategori, dll)
- `api/` — Endpoint API untuk operasi CRUD barang
- `config/` — Konfigurasi database
- `public/templates/` — Template header & footer

## 🛠️ Teknologi

- PHP 7/8
- MySQL/MariaDB
- HTML, CSS

## 📬 Kontribusi

Pull request & issue sangat terbuka untuk pengembangan lebih lanjut!

---

**Sistem Inventaris Warung** — Solusi sederhana untuk pencatatan stok warung Anda!  
