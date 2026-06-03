# Integration Guide - Proses Folder

Panduan cepat untuk mengintegrasikan proses folder dengan form-form di halaman utama.

## Configuration Files Setup

### 1. config/database.php
- ✅ Sudah dibuat dengan koneksi MySQL
- Pastikan konfigurasi host, user, password, dan database sudah sesuai
- Gunakan `$conn` untuk semua database operations

### 2. config/session.php
- ✅ Sudah dibuat dengan session management dan helper functions
- Helper functions tersedia: `isLoggedIn()`, `isAdmin()`, `requireLogin()`, `requireAdmin()`, `setFlash()`, `getFlash()`

### 3. includes/alert.php
- ✅ Sudah dibuat untuk display flash messages
- Tambahkan di setiap halaman: `<?php include 'includes/alert.php'; ?>`

---

## Forms Integration Checklist

### Authentication Pages

#### login.php
- [ ] Include `config/session.php`
- [ ] Form action: `proses/proses_login.php`
- [ ] Form method: POST
- [ ] Fields: `email`, `password`
- [ ] Optional: `remember` checkbox
- [ ] Display alert: `<?php include 'includes/alert.php'; ?>`

#### register.php
- [ ] Include `config/session.php`
- [ ] Form action: `proses/proses_register.php`
- [ ] Form method: POST
- [ ] Fields: `username`, `email`, `password`, `confirm_password`
- [ ] Display alert: `<?php include 'includes/alert.php'; ?>`

#### logout.php
- [ ] Simple script to destroy session:
```php
<?php
session_start();
session_destroy();
header('Location: index.php');
exit;
?>
```

---

### Admin Pages

#### admin/tambah_produk.php
- [ ] Include `config/session.php` at top
- [ ] Call `requireAdmin()` to check authorization
- [ ] Form action: `../proses/proses_tambah_produk.php`
- [ ] Form method: POST
- [ ] Enctype: `multipart/form-data` (for file upload)
- [ ] Fields: `nama_produk`, `deskripsi`, `harga`, `stok`, `kategori_id`, `gambar`
- [ ] Display alert: `<?php include '../includes/alert.php'; ?>`

#### admin/edit_produk.php
- [ ] Include `config/session.php` and `config/database.php`
- [ ] Call `requireAdmin()` to check authorization
- [ ] Get product ID from `$_GET['id']`
- [ ] Query database to fetch current data
- [ ] Form action: `../proses/proses_edit_produk.php`
- [ ] Form method: POST
- [ ] Enctype: `multipart/form-data`
- [ ] Hidden field: `id` (product ID)
- [ ] Pre-fill form with current data
- [ ] Display alert: `<?php include '../includes/alert.php'; ?>`

#### admin/produk.php
- [ ] Include `config/session.php` and `config/database.php`
- [ ] Call `requireAdmin()` to check authorization
- [ ] Query all products: `SELECT * FROM produk`
- [ ] Display in table with actions:
  - Edit button: `admin/edit_produk.php?id={id}`
  - Delete button: `proses/proses_hapus_produk.php?id={id}`
- [ ] Display alert: `<?php include '../includes/alert.php'; ?>`

#### admin/tambah_kategori.php
- [ ] Include `config/session.php`
- [ ] Call `requireAdmin()`
- [ ] Form action: `../proses/proses_tambah_kategori.php`
- [ ] Form method: POST
- [ ] Fields: `nama_kategori`, `deskripsi`
- [ ] Display alert: `<?php include '../includes/alert.php'; ?>`

#### admin/edit_kategori.php
- [ ] Include `config/session.php` and `config/database.php`
- [ ] Call `requireAdmin()`
- [ ] Get category ID from `$_GET['id']`
- [ ] Query and pre-fill data
- [ ] Form action: `../proses/proses_edit_kategori.php`
- [ ] Form method: POST
- [ ] Hidden field: `id`
- [ ] Display alert: `<?php include '../includes/alert.php'; ?>`

#### admin/kategori.php
- [ ] Include `config/session.php` and `config/database.php`
- [ ] Call `requireAdmin()`
- [ ] Query all categories: `SELECT * FROM kategori`
- [ ] Display in table with actions:
  - Edit button: `admin/edit_kategori.php?id={id}`
  - Delete button: `proses/proses_hapus_kategori.php?id={id}`
- [ ] Display alert: `<?php include '../includes/alert.php'; ?>`

#### admin/dashboard.php
- [ ] Include `config/session.php` and `config/database.php`
- [ ] Call `requireAdmin()`
- [ ] Display summary statistics:
  - Total produk: `SELECT COUNT(*) FROM produk`
  - Total kategori: `SELECT COUNT(*) FROM kategori`
  - Total pesanan: `SELECT COUNT(*) FROM pesanan`
  - Total revenue: `SELECT SUM(total_harga) FROM pesanan`

#### admin/pesanan.php
- [ ] Include `config/session.php` and `config/database.php`
- [ ] Call `requireAdmin()`
- [ ] Query all orders: `SELECT p.*, u.username FROM pesanan p JOIN users u`
- [ ] Display order details with related products from `detail_pesanan`

---

### User Pages

#### index.php
- [ ] Include `config/session.php`
- [ ] Query products: `SELECT p.*, k.nama_kategori FROM produk p JOIN kategori k`
- [ ] Display alert: `<?php include 'includes/alert.php'; ?>`

#### detail_produk.php
- [ ] Include `config/session.php` and `config/database.php`
- [ ] Get product ID from `$_GET['id']`
- [ ] Query single product with category
- [ ] Display detail and add to cart form:
```html
<form method="POST" action="proses/proses_cart.php">
    <input type="hidden" name="action" value="add">
    <input type="hidden" name="produk_id" value="<?php echo $product['id']; ?>">
    <input type="number" name="jumlah" value="1" min="1" max="<?php echo $product['stok']; ?>">
    <button type="submit">Tambah ke Keranjang</button>
</form>
```

#### cart.php
- [ ] Include `config/session.php`
- [ ] Call `requireLogin()`
- [ ] Display cart items from `$_SESSION['cart']`
- [ ] Show total price calculation
- [ ] Update quantity form:
```html
<form method="POST" action="proses/proses_cart.php">
    <input type="hidden" name="action" value="update">
    <input type="hidden" name="produk_id" value="<?php echo $item['produk_id']; ?>">
    <input type="number" name="jumlah" value="<?php echo $item['jumlah']; ?>">
    <button type="submit">Update</button>
</form>
```
- [ ] Remove item form:
```html
<form method="POST" action="proses/proses_cart.php">
    <input type="hidden" name="action" value="remove">
    <input type="hidden" name="produk_id" value="<?php echo $item['produk_id']; ?>">
    <button type="submit">Hapus</button>
</form>
```
- [ ] Proceed to checkout button: `href="checkout.php"`
- [ ] Display alert: `<?php include 'includes/alert.php'; ?>`

#### checkout.php
- [ ] Include `config/session.php`
- [ ] Call `requireLogin()`
- [ ] Display order summary from `$_SESSION['cart']`
- [ ] Checkout form:
```html
<form method="POST" action="proses/proses_checkout.php">
    <input type="text" name="nama_penerima" required>
    <textarea name="alamat" required></textarea>
    <input type="text" name="telepon" required>
    <textarea name="catatan"></textarea>
    <select name="metode_pembayaran" required>
        <option value="transfer">Transfer Bank</option>
        <option value="cod">COD (Bayar di Tempat)</option>
        <option value="kartu_kredit">Kartu Kredit</option>
    </select>
    <button type="submit">Proses Pesanan</button>
</form>
```
- [ ] Display alert: `<?php include 'includes/alert.php'; ?>`

#### riwayat_pesanan.php
- [ ] Include `config/session.php` and `config/database.php`
- [ ] Call `requireLogin()`
- [ ] Query user's orders: `SELECT * FROM pesanan WHERE user_id = ?`
- [ ] Display order list with status
- [ ] Link to order details page
- [ ] Display alert: `<?php include 'includes/alert.php'; ?>`

---

## Session Check Template

Gunakan template ini di awal setiap file protected:

```php
<?php
require_once 'config/database.php';
require_once 'config/session.php';

// Check authentication (untuk user pages)
requireLogin();

// Check admin role (untuk admin pages)
requireAdmin();
?>
```

---

## Flash Message Display

Gunakan di setiap halaman untuk menampilkan notifikasi:

```php
<?php include 'includes/alert.php'; ?>
```

---

## Image Upload Directory

Pastikan folder `assets/img/produk/` ada dan writable:

```bash
mkdir -p assets/img/produk/
chmod 755 assets/img/produk/
```

---

## Database Relationships Reference

### Users Table
```
id (PK)
username
email
password
role (admin/user)
remember_token
created_at
updated_at
```

### Kategori Table
```
id (PK)
nama_kategori
deskripsi
created_at
updated_at
```

### Produk Table
```
id (PK)
nama_produk
deskripsi
harga
stok
kategori_id (FK)
gambar
created_at
updated_at
```

### Pesanan Table
```
id (PK)
user_id (FK)
tanggal_pesanan
total_harga
status_pesanan
nama_penerima
alamat
telepon
metode_pembayaran
catatan
created_at
updated_at
```

### Detail_Pesanan Table
```
id (PK)
pesanan_id (FK)
produk_id (FK)
jumlah
harga_satuan
subtotal
created_at
updated_at
```

---

## Testing Checklist

- [ ] Test login dengan user yang ada
- [ ] Test register dengan user baru
- [ ] Test admin menambah kategori
- [ ] Test admin menambah produk dengan gambar
- [ ] Test admin edit kategori dan produk
- [ ] Test admin hapus kategori (validation: harus kosong)
- [ ] Test admin hapus produk (gambar harus terhapus)
- [ ] Test user add to cart
- [ ] Test user update cart quantity
- [ ] Test user remove from cart
- [ ] Test user checkout dengan berbagai metode pembayaran
- [ ] Test stok berkurang setelah checkout
- [ ] Test lihat riwayat pesanan
- [ ] Test flash messages muncul dengan benar
- [ ] Test error handling (invalid input, duplicate data, dll)
- [ ] Test session protection (akses halaman admin as user)

---

## Common Issues & Solutions

### Session Tidak Jalan
**Solusi**: Pastikan `require_once 'config/session.php'` di awal setiap file

### Flash Message Tidak Tampil
**Solusi**: Tambahkan `<?php include 'includes/alert.php'; ?>` di halaman yang sesuai

### Upload Gambar Gagal
**Solusi**: 
1. Pastikan folder `assets/img/produk/` ada dan writable
2. Validasi ukuran dan tipe file
3. Check server upload_max_filesize setting

### Stok Tidak Berkurang
**Solusi**: Pastikan checkout process sudah complete (transaction commit)

### Remember Me Tidak Bekerja
**Solusi**: Pastikan cookies enabled dan remember_token field ada di users table

---

## Next Steps

1. Setup semua form di halaman-halaman utama dengan action mengarah ke proses folder
2. Test setiap proses file dengan data dummy
3. Fix validation messages sesuai kebutuhan
4. Customize flash message styling sesuai design
5. Setup error logging untuk production
