# Dokumentasi — Sistem Penjualan Online

Referensi developer untuk sistem e-commerce PHP Native + MySQLi.

---

## Daftar Isi

1. [Struktur Proyek](#struktur-proyek)
2. [Setup](#setup)
3. [Konvensi Kode](#konvensi-kode)
4. [Config](#config)
5. [Proses Files](#proses-files)
6. [Halaman User](#halaman-user)
7. [Halaman Admin](#halaman-admin)
8. [Database](#database)
9. [Keamanan](#keamanan)
10. [Troubleshooting](#troubleshooting)

---

## Struktur Proyek

```
TA2/
├── index.php                  # Beranda + katalog produk (PDO)
├── login.php                  # Form login (render only)
├── register.php               # Form registrasi (render only)
├── logout.php                 # Hancurkan session, redirect login
├── detail_produk.php          # Detail produk + tombol add to cart
├── cart.php                   # Keranjang belanja (SESSION-based)
├── checkout.php               # Form checkout
├── riwayat_pesanan.php        # Riwayat pesanan user
├── error.php                  # Halaman error
│
├── config/
│   ├── database.php           # Koneksi MySQLi ($conn)
│   └── session.php            # Session + helper functions
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
│   ├── proses_checkout.php
│   └── proses_update_status_pesanan.php
│
├── admin/
│   ├── dashboard.php
│   ├── produk.php
│   ├── tambah_produk.php
│   ├── edit_produk.php
│   ├── kategori.php
│   ├── tambah_kategori.php
│   ├── edit_kategori.php
│   └── pesanan.php
│
├── includes/
│   ├── navbar.php             # Navigasi user
│   ├── footer.php             # Footer user
│   ├── alert.php              # Flash message display
│   ├── admin_header.php       # HTML shell + nav admin
│   └── admin_footer.php       # Bootstrap JS + alert autohide
│
├── assets/
│   ├── css/
│   │   ├── style.css          # Stylesheet user pages
│   │   ├── auth.css           # login.php + register.php
│   │   ├── detail.css         # detail_produk.php
│   │   └── error.css          # error.php
│   └── js/
│       └── script.js          # Mobile nav toggle
│
└── database/
    └── penjualan_online.sql
```

---

## Setup

```bash
# 1. Clone dan masuk ke folder
git clone <repo-url>
cd penjualan-online

# 2. Buat database
mysql -u root -e "CREATE DATABASE penjualan_online;"
mysql -u root penjualan_online < database/penjualan_online.sql

# 3. Konfigurasi environment (opsional, default sudah cocok untuk Laragon)
cp .env.example .env

# 4. Buat folder upload dan set permission
mkdir -p assets/img/produk
chmod 755 assets/img/produk

# 5. Buka di browser
# http://localhost/penjualan-online
```

**Default credentials:**

| Role  | Email              | Password |
|-------|--------------------|----------|
| Admin | admin@toko.local   | password |
| User  | user@toko.local    | password |

**Environment variables** (via `.env` atau server config):

| Variable      | Default           |
|---------------|-------------------|
| `APP_BASE_PATH` | `/penjualan-online` |
| `DB_HOST`     | `127.0.0.1`       |
| `DB_PORT`     | `3306`            |
| `DB_NAME`     | `penjualan_online`|
| `DB_USER`     | `root`            |
| `DB_PASS`     | *(kosong)*        |

---

## Konvensi Kode

- **Semua redirect** menggunakan absolute path: `/penjualan-online/login.php`
- **Database:** seluruh file menggunakan `$conn` (MySQLi), kecuali `index.php` yang menggunakan PDO lokal (`$pdo`) untuk query katalog
- **Pola form:** halaman file hanya render HTML, semua logika POST ada di `proses/`
- **Flash messages:** selalu via `setFlash()` + redirect, tidak ada output HTML dari `proses/`
- **Prepared statements:** semua query dengan input user wajib menggunakan `bind_param()` — tidak ada string interpolation

---

## Config

### `config/database.php`

Membuat koneksi MySQLi dan menyimpannya di `$conn`.

```php
require_once 'config/database.php';
// $conn siap digunakan
```

### `config/session.php`

Menginisialisasi session dan menyediakan helper functions.

```php
require_once 'config/session.php';
```

**Helper functions:**

| Function | Return | Keterangan |
|----------|--------|------------|
| `isLoggedIn()` | `bool` | Cek apakah user sudah login |
| `isAdmin()` | `bool` | Cek apakah user adalah admin |
| `requireLogin()` | `void` | Redirect ke login jika belum login |
| `requireAdmin()` | `void` | Redirect ke index jika bukan admin |
| `setFlash(key, message, type)` | `void` | Set flash message ke session |
| `getFlash()` | `array\|null` | Ambil dan hapus flash message |
| `cartCount()` | `int` | Jumlah total item di keranjang |

**Flash message types:** `success`, `danger`, `warning`, `info`

**Session variables:**

```php
$_SESSION['user_id']   // int   — ID user yang login
$_SESSION['username']  // string — nama user
$_SESSION['role']      // string — 'user' atau 'admin'
$_SESSION['cart']      // array  — keranjang belanja
$_SESSION['flash']     // array  — flash message (auto-clear setelah getFlash())
```

---

## Proses Files

Semua file di `proses/` mengikuti pola yang sama:

```php
require_once '../config/database.php';
require_once '../config/session.php';
// requireLogin() atau requireAdmin() sesuai kebutuhan

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new RuntimeException('Method tidak diizinkan');
    }
    // ... logika
    setFlash('success', 'Berhasil.');
    header('Location: ../halaman-tujuan.php');
} catch (Throwable $e) {
    error_log('Context: ' . $e->getMessage());
    setFlash('danger', $e->getMessage());
    header('Location: ../halaman-asal.php');
}
exit;
```

---

### `proses_login.php`

**Method:** POST  
**Auth:** —

| Field | Tipe | Keterangan |
|-------|------|------------|
| `username` | string | Nama atau email |
| `password` | string | Password |

Proses: cari user by nama atau email → `password_verify()` → set session → redirect by role (admin ke `admin/dashboard.php`, user ke `index.php`).

---

### `proses_register.php`

**Method:** POST  
**Auth:** —

| Field | Tipe | Keterangan |
|-------|------|------------|
| `username` | string | Min 3 karakter, unik |
| `email` | string | Format valid, unik |
| `password` | string | Min 6 karakter |
| `confirm_password` | string | Harus cocok dengan password |

Proses: validasi semua field → cek duplikat username/email → `password_hash(PASSWORD_DEFAULT)` → insert → set session → redirect `index.php`.

---

### `proses_tambah_produk.php`

**Method:** POST  
**Auth:** `requireAdmin()`  
**Enctype:** `multipart/form-data`

| Field | Tipe | Keterangan |
|-------|------|------------|
| `nama` | string | Nama produk |
| `kategori_id` | int | ID kategori (harus ada di DB) |
| `harga` | float | >= 0 |
| `stok` | int | >= 0 |
| `deskripsi` | string | Opsional |
| `gambar` | file | JPG/PNG/WEBP, maks 2MB, opsional |

Gambar disimpan ke `assets/img/produk/` dengan nama acak (`bin2hex(random_bytes(12))`).

---

### `proses_edit_produk.php`

**Method:** POST  
**Auth:** `requireAdmin()`  
**Enctype:** `multipart/form-data`

Field sama dengan tambah produk, ditambah:

| Field | Tipe | Keterangan |
|-------|------|------------|
| `id` | int | ID produk yang diedit |

Jika `gambar` diupload, gambar baru disimpan dan field `gambar` di DB diperbarui. Gambar lama tidak dihapus otomatis — hapus manual jika perlu.

---

### `proses_hapus_produk.php`

**Method:** GET  
**Auth:** `requireAdmin()`

| Parameter URL | Tipe | Keterangan |
|---------------|------|------------|
| `id` | int | ID produk |

Cek apakah produk masih ada di `detail_pesanan` — jika ya, tolak penghapusan. Jika tidak, hapus file gambar (jika ada) lalu hapus record.

---

### `proses_tambah_kategori.php`

**Method:** POST  
**Auth:** `requireAdmin()`

| Field | Tipe | Keterangan |
|-------|------|------------|
| `nama` | string | Nama kategori, unik |

Duplikat nama ditangkap via `mysqli_sql_exception` dengan error code 1062.

---

### `proses_edit_kategori.php`

**Method:** POST  
**Auth:** `requireAdmin()`

| Field | Tipe | Keterangan |
|-------|------|------------|
| `id` | int | ID kategori |
| `nama` | string | Nama baru, unik |

---

### `proses_hapus_kategori.php`

**Method:** GET  
**Auth:** `requireAdmin()`

| Parameter URL | Tipe | Keterangan |
|---------------|------|------------|
| `id` | int | ID kategori |

Cek apakah kategori masih punya produk — jika ya, tolak penghapusan.

---

### `proses_cart.php`

**Method:** POST  
**Auth:** `requireLogin()`

Dikendalikan oleh field `action`:

| Action | Field tambahan | Keterangan |
|--------|----------------|------------|
| `add` | `produk_id`, `jumlah` | Tambah item atau update jumlah jika sudah ada |
| `update` | `produk_id`, `jumlah` | Update jumlah item |
| `remove` | `produk_id` | Hapus satu item |
| `clear` | — | Kosongkan seluruh keranjang |

Semua action memvalidasi stok realtime dari DB sebelum mengubah `$_SESSION['cart']`.

**Struktur cart session:**

```php
$_SESSION['cart']['produk_1'] = [
    'produk_id'   => 1,
    'nama_produk' => 'Earphone Bluetooth',
    'harga'       => 299000.00,
    'jumlah'      => 2,
    'gambar'      => 'abc123.jpg',
];
```

---

### `proses_checkout.php`

**Method:** POST  
**Auth:** `requireLogin()`

| Field | Tipe | Keterangan |
|-------|------|------------|
| `nama_penerima` | string | Wajib |
| `alamat` | string | Wajib |
| `telepon` | string | Format: `0` atau `+62` diikuti 9–12 digit |
| `metode_pembayaran` | string | `transfer`, `cod`, atau `kartu_kredit` |
| `catatan` | string | Opsional |

Proses menggunakan **database transaction**:
1. Lock semua produk di cart dengan `SELECT ... FOR UPDATE`
2. Validasi stok realtime
3. Insert ke `pesanan`
4. Insert ke `detail_pesanan`
5. Update stok produk
6. `COMMIT` → clear cart → redirect `riwayat_pesanan.php`

Jika ada error di langkah manapun → `ROLLBACK`, tidak ada data yang tersimpan setengah-setengah.

---

### `proses_update_status_pesanan.php`

**Method:** POST  
**Auth:** `requireAdmin()`

| Field | Tipe | Keterangan |
|-------|------|------------|
| `id` | int | ID pesanan |
| `status` | string | `pending`, `diproses`, `dikirim`, `selesai`, `batal` |

---

## Halaman User

Setiap halaman user mengikuti pola:

```php
<?php
require_once __DIR__ . '/config/session.php';
require_once __DIR__ . '/config/database.php';
// requireLogin() jika halaman butuh auth

$pageTitle = 'Judul Halaman';
?>
<!doctype html>
<html lang="id">
<head>
    ...
    <link rel="stylesheet" href="/penjualan-online/assets/css/style.css">
</head>
<body>
<?php require __DIR__ . '/includes/navbar.php'; ?>
<main>
    <?php require __DIR__ . '/includes/alert.php'; ?>
    <!-- konten -->
</main>
<?php require __DIR__ . '/includes/footer.php'; ?>
```

**Flash message:** selalu sertakan `includes/alert.php` di dalam `<main>` untuk menampilkan notifikasi dari proses file.

---

## Halaman Admin

Setiap halaman admin mengikuti pola:

```php
<?php
require_once '../config/database.php';
require_once '../config/session.php';
requireAdmin();

$pageTitle = 'Judul Halaman';

// ... query DB

$flash = getFlash();
include '../includes/admin_header.php';
?>

<?php if ($flash): ?>
    <div class="alert alert-<?= $flash['type'] ?> alert-autohide alert-dismissible fade show mb-3" role="alert">
        <?= htmlspecialchars($flash['message']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<!-- konten halaman -->

<?php include '../includes/admin_footer.php'; ?>
```

**Menambah halaman admin baru:** cukup tambahkan satu entry ke array `$adminNav` di `includes/admin_header.php` — tidak ada file lain yang perlu diubah.

**Auto-dismiss alert:** semua alert dengan class `alert-autohide` otomatis hilang setelah 4 detik via script di `admin_footer.php`.

---

## Database

```sql
users           -- akun pengguna
kategori        -- kategori produk
produk          -- produk (FK: kategori)
pesanan         -- header pesanan (FK: users)
detail_pesanan  -- baris pesanan (FK: pesanan, produk)
```

**Relasi:**

```
users (1) ──→ pesanan (M) ──→ detail_pesanan (M) ←── produk (M) ←── kategori (1)
```

**Upload gambar:** disimpan di `assets/img/produk/`. Field `gambar` di tabel `produk` hanya menyimpan nama file (bukan path lengkap).

---

## Keamanan

**SQL Injection** — semua query dengan input user menggunakan prepared statements:

```php
$stmt = $conn->prepare('SELECT * FROM users WHERE email = ?');
$stmt->bind_param('s', $email);
$stmt->execute();
```

**Password** — selalu hash dengan `PASSWORD_DEFAULT` (bcrypt), verifikasi dengan `password_verify()`.

**File upload** — validasi MIME type via `finfo` (bukan dari `$_FILES['type']` yang bisa dipalsukan), batas ukuran 2MB, ekstensi terbatas JPG/PNG/WEBP, nama file diacak.

**Autorization** — setiap proses file admin memanggil `requireAdmin()` di baris pertama. Setiap proses file user memanggil `requireLogin()`. Tidak ada operasi yang mengandalkan input user untuk menentukan role.

**Session** — `session_regenerate_id(true)` dipanggil setelah login berhasil untuk mencegah session fixation.

---

## Troubleshooting

**Session tidak berjalan**
Pastikan `require_once 'config/session.php'` ada di baris pertama setiap file, sebelum output HTML apapun.

**Flash message tidak muncul**
Pastikan `<?php require __DIR__ . '/includes/alert.php'; ?>` ada di dalam `<main>` halaman tujuan redirect.

**Upload gambar gagal**
- Cek folder `assets/img/produk/` ada dan writable (`chmod 755`)
- Cek `upload_max_filesize` dan `post_max_size` di `php.ini` (minimal 2MB)
- Pastikan `enctype="multipart/form-data"` ada di tag `<form>`

**Stok tidak berkurang setelah checkout**
Checkout menggunakan transaction — jika ada error, rollback terjadi dan stok tidak berubah. Cek `error_log` PHP untuk pesan error dari `proses_checkout.php`.

**Redirect salah path**
Semua redirect menggunakan absolute path `/penjualan-online/...`. Jika deploy di path berbeda, update `APP_BASE_PATH` di environment dan sesuaikan hardcoded redirect di `config/session.php`.