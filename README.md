# Sistem Inventaris Warung (SIW)

**SIW** adalah aplikasi web sederhana berbasis PHP & MySQL untuk membantu pengelolaan inventaris barang dan kategori di warung/toko kecil.  
Aplikasi ini cocok untuk UMKM yang ingin melakukan pencatatan stok barang secara digital dan efisien.

## ✨ Fitur Utama

- **Manajemen Barang:**  
  Tambah, edit, hapus (soft delete), dan lihat daftar barang beserta stok, harga beli, harga jual, dan kategori.  
  Jika Anda menambah barang baru dengan nama & kategori yang sama seperti barang yang pernah dihapus, sistem akan membuat entri baru dengan ID berbeda. Untuk menghindari duplikasi logis, disarankan melakukan *restore* pada barang yang dihapus jika ingin mengaktifkan kembali barang tersebut.

- **Manajemen Kategori:**  
  Kelola kategori barang agar data lebih terstruktur.

- **Notifikasi Otomatis:**  
  Pesan sukses/gagal otomatis tampil setelah aksi CRUD.

- **API CRUD Barang:**  
  Endpoint API untuk integrasi eksternal (Postman-ready).

- **Desain Responsif:**  
  Tampilan sederhana dan mudah digunakan di berbagai perangkat.

## 🚀 Cara Instalasi

1. **Clone atau Download** repository ini ke folder `htdocs` XAMPP Anda.
2. Buat database MySQL, lalu import file SQL dari folder `db/`.
3. Edit file `config/database.php` sesuai konfigurasi database Anda.
4. Jalankan XAMPP, lalu akses di browser:  
   `http://localhost/sistem-inventaris-warung/public/`

## 📦 Struktur Folder

- `public/` — Semua file frontend (dashboard, daftar barang, kategori, dll)
- `api/` — Endpoint API untuk operasi CRUD barang
- `config/` — Konfigurasi database
- `db/` — File SQL untuk inisialisasi database
- `public/templates/` — Template header & footer

## 🛠️ Teknologi

- PHP 7/8
- MySQL/MariaDB
- HTML, CSS

## ℹ️ Catatan Soft Delete

- Barang yang dihapus tidak benar-benar dihapus dari database, melainkan diberi penanda `is_deleted = 1` (*soft delete*).
- Jika ingin menambah barang dengan nama & kategori yang sama seperti barang yang pernah dihapus, sebaiknya lakukan *restore* pada barang tersebut, bukan membuat entri baru, untuk menjaga konsistensi data.

## 📬 Kontribusi

Pull request & issue sangat terbuka untuk pengembangan lebih lanjut!

---

**Sistem Inventaris Warung** — Solusi sederhana untuk pencatatan stok warung Anda!
