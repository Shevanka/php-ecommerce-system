Sistem Informasi Penjualan Online
Aplikasi penjualan online berbasis PHP Native dan MySQL yang dikembangkan sebagai proyek akhir Praktikum Pemrograman Web. Sistem ini mengimplementasikan fitur autentikasi menggunakan Session dan Cookies, manajemen produk, keranjang belanja, checkout, serta dashboard admin untuk pengelolaan data.

Status: 🚧 In Development (Proyek Akademik)

Preview
Screenshot akan ditambahkan setelah antarmuka utama selesai dikembangkan.

About This Project
Proyek ini dibuat untuk mengimplementasikan materi yang telah dipelajari pada mata kuliah Praktikum Pemrograman Web, meliputi:

PHP Native
MySQL Database
CRUD (Create, Read, Update, Delete)
Session Management
Cookies Management
Authentication & Authorization
Form Validation
Relasi Database
Git & GitHub Collaboration
Sistem memungkinkan pengguna melakukan pembelian produk secara online, sementara admin dapat mengelola produk, kategori, dan pesanan melalui dashboard admin.

Features
User Features
Registrasi akun
Login dan logout
Remember Me menggunakan cookies
Melihat daftar produk
Melihat detail produk
Menambahkan produk ke keranjang
Checkout pesanan
Melihat riwayat pesanan
Admin Features
Login admin
Dashboard admin
CRUD produk
CRUD kategori
Manajemen pesanan
Monitoring data transaksi
Security Features
Session-based authentication
Role-based authorization
Cookie implementation
Input validation
Session protection
Tech Stack
Backend
PHP Native
Frontend
HTML5
CSS3
JavaScript
Bootstrap 5
Database
MySQL / MariaDB
Development Tools
Visual Studio Code
Git
GitHub
XAMPP / Laragon
phpMyAdmin
Project Structure
penjualan-online/
│
├── index.php
├── login.php
├── register.php
├── logout.php
├── detail_produk.php
├── cart.php
├── checkout.php
├── riwayat_pesanan.php
│
├── config/
│   ├── database.php
│   └── session.php
│
├── admin/
│   ├── dashboard.php
│   ├── produk.php
│   ├── tambah_produk.php
│   ├── edit_produk.php
│   ├── hapus_produk.php
│   ├── kategori.php
│   ├── tambah_kategori.php
│   ├── edit_kategori.php
│   ├── hapus_kategori.php
│   └── pesanan.php
│
├── proses/
│   ├── proses_login.php
│   ├── proses_register.php
│   ├── proses_tambah_produk.php
│   ├── proses_edit_produk.php
│   ├── proses_hapus_produk.php
│   ├── proses_tambah_kategori.php
│   ├── proses_edit_kategori.php
│   ├── proses_hapus_kategori.php
│   ├── proses_cart.php
│   └── proses_checkout.php
│
├── includes/
│   ├── header.php
│   ├── navbar.php
│   ├── footer.php
│   └── alert.php
│
├── assets/
│   ├── css/
│   │   └── style.css
│   ├── js/
│   │   └── script.js
│   └── img/
│       ├── logo.png
│       └── produk/
│
└── database/
    └── penjualan_online.sql
Database Structure
Database yang digunakan:

penjualan_online
Tabel utama:

users
kategori
produk
pesanan
detail_pesanan
Relasi sederhana:

users
 └── pesanan
      └── detail_pesanan
            └── produk
                  └── kategori
Installation
1. Clone Repository
git clone <REPOSITORY_URL>
cd penjualan-online
2. Jalankan Web Server
Gunakan salah satu:

XAMPP
Laragon
Pastikan:

Apache aktif
MySQL aktif
3. Buat Database
CREATE DATABASE penjualan_online;
4. Import Database
Import file:

database/penjualan_online.sql
5. Konfigurasi Database
Konfigurasi default sudah cocok untuk Laragon (root tanpa password). Untuk environment lain, gunakan environment variable berikut berdasarkan .env.example:

APP_BASE_PATH=/penjualan-online
DB_HOST=127.0.0.1
DB_PORT=3306
DB_NAME=penjualan_online
DB_USER=root
DB_PASS=
6. Jalankan Aplikasi
Buka browser:

http://localhost/penjualan-online
Usage
User
Registrasi akun
Login ke sistem
Jelajahi produk
Tambahkan produk ke keranjang
Checkout pesanan
Lihat riwayat transaksi
Admin
Login sebagai admin
Kelola kategori
Kelola produk
Kelola pesanan
Monitoring transaksi
Session & Cookies Implementation
Session
Digunakan untuk:

Menyimpan status login
Menyimpan data pengguna aktif
Menyimpan keranjang belanja
Flash message
Contoh:

$_SESSION['user_id'];
$_SESSION['username'];
$_SESSION['role'];
$_SESSION['cart'];
Cookies
Digunakan untuk:

Remember Me
Menyimpan preferensi pengguna
Contoh:

setcookie(
    "remember_user",
    $user_id,
    time() + (86400 * 7),
    "/"
);
GitHub Workflow
Branch Strategy
main
dev
feature/login-register
feature/admin-produk
feature/cart-checkout
feature/database
Development Flow
Issue
 ↓
Feature Branch
 ↓
Commit
 ↓
Push
 ↓
Pull Request
 ↓
Code Review
 ↓
Merge ke dev
 ↓
Testing
 ↓
Merge ke main
Commit Convention
Gunakan format:

type: short description
Contoh:

feat: add login feature
feat: add shopping cart
fix: resolve checkout validation
docs: update readme
style: improve navbar layout
refactor: separate database configuration
test: add login testing
chore: update gitignore
Jenis commit:

Type	Kegunaan
feat	Fitur baru
fix	Perbaikan bug
docs	Dokumentasi
style	Tampilan
refactor	Perapian kode
test	Pengujian
chore	Maintenance
Team Collaboration Rules
Branch Naming
feature/nama-fitur
fix/nama-bug
docs/nama-dokumentasi
Contoh:

feature/login
feature/cart
fix/session-error
Pull Request Rules
Tidak boleh push langsung ke main
Semua perubahan melalui Pull Request
Pull Request wajib memiliki deskripsi
Pull Request harus lolos testing lokal
Review Rules
Minimal 1 reviewer
Berikan komentar yang konstruktif
Jangan merge kode yang belum diuji
Merge Rules
Lakukan merge jika:

Tidak ada konflik
Tidak ada error
Fitur berjalan sesuai kebutuhan
Review telah selesai
Screenshots
Screenshot akan ditambahkan setelah UI final selesai.
Rekomendasi screenshot:

Halaman Login
Halaman Register
Dashboard Admin
Daftar Produk
Keranjang Belanja
Checkout
Roadmap
Version 1.0
 Perancangan struktur proyek
 Perancangan database
 Login & Register
 Session Management
 Cookies Implementation
 CRUD Produk
 CRUD Kategori
 Shopping Cart
 Checkout
 Riwayat Pesanan
Version 1.1
 Pencarian produk
 Filter kategori
 Upload gambar produk
 Dashboard statistik
Version 1.2
 Pagination produk
 Export laporan
 Notifikasi transaksi
What I Learned
Melalui proyek ini, tim mempelajari:

Implementasi PHP Native dalam aplikasi nyata
Manajemen Session dan Cookies
Perancangan database relasional MySQL
Penerapan CRUD pada aplikasi web
Kolaborasi tim menggunakan Git dan GitHub
Penggunaan Pull Request dan Code Review
Pengelolaan proyek menggunakan GitHub Issues
Author
Team Project
Shevanka Bagus D. K

Role: Backend Authentication, Session, Cookies, Database
Kristian Utama Putra

Role: Product Management, Admin Panel, Testing
Repository ini dikembangkan sebagai proyek akhir Praktikum Pemrograman Web.

License
Project ini dibuat untuk tujuan akademik dan pembelajaran.

© 2026 Tim Pengembang Sistem Informasi Penjualan Online
