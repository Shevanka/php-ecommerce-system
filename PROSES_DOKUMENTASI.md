# Dokumentasi Proses Folder

Folder `proses/` berisi file-file PHP yang menangani pemrosesan form dan CRUD operations untuk sistem penjualan online.

## Struktur File

### 1. Authentication & User Management

#### `proses_login.php`
**Tujuan**: Menangani login pengguna dan autentikasi

**HTTP Method**: POST

**Parameters**:
- `email` (string) - Email pengguna
- `password` (string) - Password pengguna
- `remember` (checkbox) - Remember me function (optional)

**Proses**:
1. Validasi input (email, password)
2. Query database untuk mencari user berdasarkan email
3. Verifikasi password dengan `password_verify()`
4. Set session variables: `user_id`, `username`, `email`, `role`
5. Handle remember me dengan cookie jika dipilih
6. Redirect ke dashboard admin atau homepage sesuai role

**Flash Message**: Login berhasil / Error message

---

#### `proses_register.php`
**Tujuan**: Menangani registrasi pengguna baru

**HTTP Method**: POST

**Parameters**:
- `username` (string) - Username pengguna
- `email` (string) - Email pengguna
- `password` (string) - Password pengguna
- `confirm_password` (string) - Konfirmasi password

**Proses**:
1. Validasi semua field tidak kosong
2. Validasi panjang username (min 3 karakter)
3. Validasi format email
4. Validasi panjang password (min 6 karakter)
5. Validasi kecocokan password dan confirm password
6. Periksa duplikasi username dan email di database
7. Hash password dengan `password_hash()`
8. Insert user ke database dengan role 'user'
9. Auto-login dengan set session
10. Redirect ke homepage

**Flash Message**: Registrasi berhasil / Error message

---

### 2. Product Management (Admin Only)

#### `proses_tambah_produk.php`
**Tujuan**: Menambahkan produk baru

**Requires**: Admin role (`requireAdmin()`)

**HTTP Method**: POST

**Parameters**:
- `nama_produk` (string) - Nama produk
- `deskripsi` (text) - Deskripsi produk
- `harga` (float) - Harga produk
- `stok` (int) - Jumlah stok
- `kategori_id` (int) - ID kategori produk
- `gambar` (file) - Gambar produk (JPEG, PNG, GIF, max 2MB)

**Proses**:
1. Validasi admin role
2. Validasi semua field required
3. Validasi harga > 0, stok >= 0
4. Validasi kategori ada di database
5. Handle file upload dengan validasi tipe dan ukuran
6. Simpan file gambar ke `assets/img/produk/`
7. Insert produk ke database
8. Redirect ke halaman produk admin

**Flash Message**: Produk berhasil ditambahkan / Error message

---

#### `proses_edit_produk.php`
**Tujuan**: Memperbarui data produk

**Requires**: Admin role

**HTTP Method**: POST

**Parameters**: Sama seperti tambah_produk + `id` (int)

**Proses**:
1. Validasi admin role
2. Validasi ID produk valid dan ada di database
3. Validasi semua field
4. Handle file upload baru (hapus gambar lama jika ada)
5. Update record produk di database
6. Redirect ke halaman produk admin

**Flash Message**: Produk berhasil diperbarui / Error message

---

#### `proses_hapus_produk.php`
**Tujuan**: Menghapus produk

**Requires**: Admin role

**HTTP Method**: GET (via URL parameter)

**Parameters**:
- `id` (int) - ID produk yang akan dihapus

**Proses**:
1. Validasi admin role
2. Validasi ID produk
3. Ambil data produk dari database
4. Hapus file gambar jika ada
5. Delete record dari database
6. Redirect ke halaman produk admin

**Flash Message**: Produk berhasil dihapus / Error message

---

### 3. Category Management (Admin Only)

#### `proses_tambah_kategori.php`
**Tujuan**: Menambahkan kategori baru

**Requires**: Admin role

**HTTP Method**: POST

**Parameters**:
- `nama_kategori` (string) - Nama kategori
- `deskripsi` (text) - Deskripsi kategori (optional)

**Proses**:
1. Validasi admin role
2. Validasi nama_kategori tidak kosong
3. Periksa duplikasi kategori
4. Insert kategori ke database
5. Redirect ke halaman kategori admin

**Flash Message**: Kategori berhasil ditambahkan / Error message

---

#### `proses_edit_kategori.php`
**Tujuan**: Memperbarui kategori

**Requires**: Admin role

**HTTP Method**: POST

**Parameters**:
- `id` (int) - ID kategori
- `nama_kategori` (string) - Nama kategori baru
- `deskripsi` (text) - Deskripsi kategori baru

**Proses**:
1. Validasi admin role
2. Validasi ID dan field
3. Periksa duplikasi nama (exclude kategori yang sedang diedit)
4. Update kategori di database
5. Redirect ke halaman kategori admin

**Flash Message**: Kategori berhasil diperbarui / Error message

---

#### `proses_hapus_kategori.php`
**Tujuan**: Menghapus kategori

**Requires**: Admin role

**HTTP Method**: GET (via URL parameter)

**Parameters**:
- `id` (int) - ID kategori yang akan dihapus

**Proses**:
1. Validasi admin role
2. Validasi ID kategori
3. Periksa kategori memiliki produk (cannot delete if has products)
4. Delete kategori dari database
5. Redirect ke halaman kategori admin

**Flash Message**: Kategori berhasil dihapus / Error message

---

### 4. Shopping Cart (User Only)

#### `proses_cart.php`
**Tujuan**: Mengelola keranjang belanja (SESSION-based)

**Requires**: User login (`requireLogin()`)

**HTTP Method**: POST

**Parameters (Dynamic berdasarkan action)**:

**action: add**
- `produk_id` (int) - ID produk
- `jumlah` (int) - Jumlah yang ditambahkan

**action: update**
- `produk_id` (int) - ID produk
- `jumlah` (int) - Jumlah baru

**action: remove**
- `produk_id` (int) - ID produk yang dihapus

**action: clear**
- (no parameters needed)

**Proses (Add)**:
1. Validasi user login
2. Validasi produk_id dan jumlah
3. Ambil data produk dari database
4. Validasi stok cukup
5. Tambah ke `$_SESSION['cart']` atau update jumlah jika sudah ada
6. Redirect ke halaman cart

**Proses (Update)**:
1. Validasi user login
2. Validasi produk_id dan jumlah baru
3. Validasi stok cukup
4. Update jumlah di `$_SESSION['cart']`
5. Redirect ke halaman cart

**Proses (Remove)**:
1. Validasi user login
2. Validasi produk_id
3. Hapus item dari `$_SESSION['cart']`
4. Redirect ke halaman cart

**Proses (Clear)**:
1. Validasi user login
2. Kosongkan `$_SESSION['cart']`
3. Redirect ke halaman cart

**Flash Message**: Item ditambahkan/diupdate/dihapus / Error message

---

### 5. Checkout (User Only)

#### `proses_checkout.php`
**Tujuan**: Memproses pemesanan dan membuat order

**Requires**: User login

**HTTP Method**: POST

**Parameters**:
- `nama_penerima` (string) - Nama penerima pesanan
- `alamat` (text) - Alamat pengiriman
- `telepon` (string) - Nomor telepon (format: +62 atau 0 diikuti 9-12 digit)
- `metode_pembayaran` (string) - Metode: 'transfer', 'cod', 'kartu_kredit'
- `catatan` (text) - Catatan pesanan (optional)

**Proses**:
1. Validasi user login
2. Validasi keranjang tidak kosong
3. Validasi semua field required
4. Validasi format telepon
5. Validasi metode pembayaran
6. Begin database transaction
7. Validasi stok real-time untuk semua item
8. Hitung total harga
9. Insert record ke tabel `pesanan`
10. Insert detail pesanan ke tabel `detail_pesanan`
11. Update stok produk di tabel `produk`
12. Commit transaction
13. Clear `$_SESSION['cart']`
14. Redirect ke halaman riwayat pesanan

**Flash Message**: Pesanan berhasil dibuat (No. Pesanan: XXX) / Error message

**Error Handling**:
- Jika ada error, transaction di-rollback
- Semua data kembali ke keadaan sebelumnya

---

## Session Management

### Session Variables Yang Digunakan

```php
$_SESSION['user_id']      // ID pengguna yang login
$_SESSION['username']     // Username pengguna
$_SESSION['email']        // Email pengguna
$_SESSION['role']         // Role: 'user' atau 'admin'
$_SESSION['cart']         // Array keranjang belanja
$_SESSION['flash']        // Flash message untuk notifikasi
```

### Flash Message Structure

```php
[
    'key'     => 'identifier',
    'message' => 'Pesan notifikasi',
    'type'    => 'success|danger|warning|info'
]
```

---

## Security Features

1. **Authentication Check**: Semua proses memvalidasi user login dengan `requireLogin()`
2. **Authorization Check**: Admin-only proses memvalidasi dengan `requireAdmin()`
3. **SQL Injection Prevention**: Semua query menggunakan prepared statements dengan `bind_param()`
4. **Password Security**: Password di-hash dengan `password_hash(PASSWORD_BCRYPT)`
5. **File Upload Validation**: Validasi tipe file dan ukuran file
6. **Input Validation**: Semua input di-validasi sesuai tipe data
7. **Transaction Safety**: Checkout menggunakan database transaction untuk data consistency
8. **Error Logging**: Semua error di-log dengan `error_log()`

---

## Usage Examples

### Form Login
```html
<form method="POST" action="proses/proses_login.php">
    <input type="email" name="email" required>
    <input type="password" name="password" required>
    <input type="checkbox" name="remember">
    <button type="submit">Login</button>
</form>
```

### Form Tambah Produk
```html
<form method="POST" action="proses/proses_tambah_produk.php" enctype="multipart/form-data">
    <input type="text" name="nama_produk" required>
    <textarea name="deskripsi"></textarea>
    <input type="number" name="harga" step="0.01" required>
    <input type="number" name="stok" required>
    <select name="kategori_id" required>
        <!-- Options dari database -->
    </select>
    <input type="file" name="gambar" accept="image/*">
    <button type="submit">Tambah Produk</button>
</form>
```

### Add to Cart
```html
<form method="POST" action="proses/proses_cart.php">
    <input type="hidden" name="action" value="add">
    <input type="hidden" name="produk_id" value="<?php echo $product_id; ?>">
    <input type="number" name="jumlah" value="1" min="1">
    <button type="submit">Tambah ke Keranjang</button>
</form>
```

### Display Flash Message
```php
<?php include 'includes/alert.php'; ?>
```

---

## Error Handling

Semua proses file menggunakan try-catch untuk error handling:

```php
try {
    // Proses logic
    if (error_condition) {
        throw new Exception('Error message');
    }
} catch (Exception $e) {
    error_log('Error: ' . $e->getMessage());
    setFlash('key', $e->getMessage(), 'danger');
    header('Location: ...'); // Redirect ke halaman awal
    exit;
}
```

---

## Database Relationships

### Pesanan → Detail Pesanan → Produk

```
pesanan (1)
  └── detail_pesanan (M)
       └── produk (M)
```

### User → Pesanan

```
users (1)
  └── pesanan (M)
```

### Kategori → Produk

```
kategori (1)
  └── produk (M)
```

---

## Notes

- Semua proses file redirect dengan session flash message
- Tidak ada output HTML/JSON, hanya redirect
- Cart disimpan di SESSION, bukan database (untuk performa)
- Transaction digunakan di checkout untuk data consistency
- File upload menggunakan unique identifier untuk menghindari conflict
- Remember token disimpan di database, bukan session
