<?php
/**
 * Proses Tambah Produk
 * Menangani penambahan produk baru (Admin)
 */

require_once '../config/database.php';
require_once '../config/session.php';

try {
    // Validasi autentikasi dan autorisasi
    requireAdmin();

    // Validasi method request
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Method tidak diizinkan');
    }

    // Ambil dan validasi input
    $nama_produk = isset($_POST['nama_produk']) ? trim($_POST['nama_produk']) : '';
    $deskripsi = isset($_POST['deskripsi']) ? trim($_POST['deskripsi']) : '';
    $harga = isset($_POST['harga']) ? floatval($_POST['harga']) : 0;
    $stok = isset($_POST['stok']) ? intval($_POST['stok']) : 0;
    $kategori_id = isset($_POST['kategori_id']) ? intval($_POST['kategori_id']) : 0;

    // Validasi input
    if (empty($nama_produk)) {
        throw new Exception('Nama produk harus diisi');
    }

    if ($harga <= 0) {
        throw new Exception('Harga harus lebih dari 0');
    }

    if ($stok < 0) {
        throw new Exception('Stok tidak boleh negatif');
    }

    if ($kategori_id <= 0) {
        throw new Exception('Kategori harus dipilih');
    }

    // Validasi kategori ada
    $checkKategori = "SELECT id FROM kategori WHERE id = ?";
    $stmt = $conn->prepare($checkKategori);
    $stmt->bind_param('i', $kategori_id);
    $stmt->execute();
    
    if ($stmt->get_result()->num_rows === 0) {
        throw new Exception('Kategori tidak ditemukan');
    }

    // Handle upload gambar jika ada
    $gambar = null;
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../assets/img/produk/';
        
        // Buat folder jika belum ada
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $fileName = uniqid('produk_') . '_' . basename($_FILES['gambar']['name']);
        $filePath = $uploadDir . $fileName;

        // Validasi tipe file
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        if (!in_array($_FILES['gambar']['type'], $allowedTypes)) {
            throw new Exception('Tipe file tidak didukung. Gunakan JPG, PNG, atau GIF');
        }

        // Validasi ukuran file (max 2MB)
        if ($_FILES['gambar']['size'] > 2 * 1024 * 1024) {
            throw new Exception('Ukuran file terlalu besar (max 2MB)');
        }

        // Move uploaded file
        if (!move_uploaded_file($_FILES['gambar']['tmp_name'], $filePath)) {
            throw new Exception('Gagal upload gambar');
        }

        $gambar = $fileName;
    }

    // Insert produk ke database
    $query = "INSERT INTO produk (nama_produk, deskripsi, harga, stok, kategori_id, gambar, created_at, updated_at) 
              VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW())";
    $stmt = $conn->prepare($query);
    
    if (!$stmt) {
        throw new Exception('Preparasi query gagal: ' . $conn->error);
    }

    $stmt->bind_param('ssdiis', $nama_produk, $deskripsi, $harga, $stok, $kategori_id, $gambar);
    
    if (!$stmt->execute()) {
        throw new Exception('Gagal menambah produk: ' . $stmt->error);
    }

    // Flash message
    setFlash('produk', 'Produk berhasil ditambahkan', 'success');

    // Redirect ke halaman produk
    header('Location: ../admin/produk.php');
    exit;

} catch (Exception $e) {
    // Log error dan set flash message
    error_log('Tambah Produk Error: ' . $e->getMessage());
    setFlash('produk', $e->getMessage(), 'danger');
    header('Location: ../admin/tambah_produk.php');
    exit;
}
