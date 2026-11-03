# 🍽️ Sistem Menu Kantin

**Aplikasi Pengelolaan Menu Kantin Berbasis Web Menggunakan Laravel dan MySQL**

![Laravel](https://img.shields.io/badge/Laravel-Framework-red?logo=laravel)
![PHP](https://img.shields.io/badge/PHP-Backend-777BB4?logo=php)
![MySQL](https://img.shields.io/badge/MySQL-Database-4479A1?logo=mysql)
![Project](https://img.shields.io/badge/Project-UTS-blue)

Aplikasi **Sistem Menu Kantin** merupakan aplikasi berbasis web yang dikembangkan untuk memenuhi tugas Ujian Tengah Semester (UTS) mata kuliah Praktikum Pemrograman Web, Paket 2 Tipe E.

Aplikasi ini digunakan untuk mengelola data menu kantin, mulai dari menambahkan, menampilkan, mengubah, menghapus, mencari, hingga memfilter data berdasarkan kategori menu.

---

## 📋 Informasi Proyek

| Keterangan | Detail |
|---|---|
| Nama Proyek | Sistem Menu Kantin |
| Mata Kuliah | Praktikum Pemrograman Web |
| Kode Mata Kuliah | INF11197 |
| Kegiatan | Ujian Tengah Semester (UTS) |
| Paket Soal | Paket 2 - Tipe E |
| Program Studi | Teknik Informatika |
| Universitas | Universitas Maritim Raja Ali Haji |

### Identitas Pengembang

| Keterangan | Detail |
|---|---|
| Nama | Raihan Darma Putra |
| NIM | 2401020138 |
| Program Studi | Teknik Informatika |

---

## 🛠️ Teknologi yang Digunakan

- **Laravel** - Framework pengembangan aplikasi web.
- **PHP** - Bahasa pemrograman backend.
- **MySQL/MariaDB** - Sistem manajemen basis data.
- **PDO (PHP Data Objects)** - Akses database dan prepared statement.
- **Blade** - Template engine Laravel.
- **Git** - Version control.
- **GitHub** - Repository dan dokumentasi proyek.

---

## ✨ Fitur Aplikasi

### 1. Pengelolaan Data Menu (CRUD)
- Menampilkan seluruh data menu.
- Menambahkan menu baru.
- Mengubah informasi menu.
- Menghapus data menu.

### 2. Pencarian dan Filter
- Pencarian berdasarkan kode menu.
- Pencarian berdasarkan nama menu.
- Pencarian berdasarkan barcode.
- Filter berdasarkan kategori menu.

### 3. Validasi dan Keamanan Input
- Validasi input di sisi server.
- Sanitasi input menggunakan `trim()` dan `strip_tags()`.
- Validasi keunikan kode menu dan barcode.
- Perlindungan form menggunakan CSRF.
- Penggunaan prepared statement untuk query database yang menerima input pengguna.

### 4. Status Harga

Klasifikasi status harga berdasarkan ketentuan:

| Kondisi Harga | Status |
|---|---|
| Harga >= Rp20.000 | Premium |
| Harga < Rp20.000 | Reguler |

### 5. Error Handling
- Penanganan kesalahan menggunakan `try-catch`.
- Pencatatan detail error menggunakan `report()`.
- Menampilkan pesan kesalahan yang aman kepada pengguna.

---

## 🗄️ Struktur Database

Nama database:

`menu_kantin_2401020138`

Database menggunakan dua tabel dengan relasi one-to-many.

### Tabel `kategori_menu`

| Kolom | Tipe Data | Keterangan |
|---|---|---|
| id | BIGINT UNSIGNED | Primary Key, Auto Increment |
| nama_kategori | VARCHAR(100) | NOT NULL, UNIQUE |

### Tabel `menu`

| Kolom | Tipe Data | Keterangan |
|---|---|---|
| id | BIGINT UNSIGNED | Primary Key, Auto Increment |
| kode_menu | CHAR(10) | NOT NULL, UNIQUE |
| nama_menu | VARCHAR(100) | NOT NULL |
| barcode | VARCHAR(30) | NOT NULL, UNIQUE |
| harga | INT UNSIGNED | CHECK 1000–100000 |
| kategori_menu_id | BIGINT UNSIGNED | Foreign Key |
| created_at | TIMESTAMP | Waktu pembuatan |
| updated_at | TIMESTAMP | Waktu perubahan |

### Relasi Database

- Satu kategori dapat memiliki banyak menu.
- Setiap menu terhubung dengan satu kategori.
- Foreign key menggunakan `ON UPDATE CASCADE`.
- Penghapusan kategori menggunakan `ON DELETE RESTRICT`.

---

## 📦 Data Awal

| Kode Menu | Nama Menu | Barcode | Harga | Kategori |
|---|---|---|---:|---|
| MN00000001 | Nasi Goreng | BR-MKN-0001 | Rp18.000 | Makanan |
| MN00000002 | Es Teh | BR-MNM-0001 | Rp5.000 | Minuman |
| MN00000003 | Mie Rebus | BR-MKN-0002 | Rp15.000 | Makanan |

---

## ⚙️ Instalasi dan Konfigurasi

### Persyaratan Sistem

Pastikan perangkat telah memiliki:
- PHP yang kompatibel dengan versi Laravel proyek.
- Composer.
- MySQL atau MariaDB.
- Git.
- Web browser.

### 1. Clone Repository

```bash
git clone https://github.com/USERNAME/REPOSITORY.git