<?php
/**
 * Proses Edit Produk
 * Menangani update produk (Admin)
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
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
    $nama_produk = isset($_POST['nama_produk']) ? trim($_POST['nama_produk']) : '';
    $deskripsi = isset($_POST['deskripsi']) ? trim($_POST['deskripsi']) : '';
    $harga = isset($_POST['harga']) ? floatval($_POST['harga']) : 0;
    $stok = isset($_POST['stok']) ? intval($_POST['stok']) : 0;
    $kategori_id = isset($_POST['kategori_id']) ? intval($_POST['kategori_id']) : 0;

    // Validasi input
    if ($id <= 0) {
        throw new Exception('ID produk tidak valid');
    }

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

    // Ambil data produk lama
    $query = "SELECT * FROM produk WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        throw new Exception('Produk tidak ditemukan');
    }

    $oldProduk = $result->fetch_assoc();

    // Validasi kategori ada
    $checkKategori = "SELECT id FROM kategori WHERE id = ?";
    $stmt = $conn->prepare($checkKategori);
    $stmt->bind_param('i', $kategori_id);
    $stmt->execute();
    
    if ($stmt->get_result()->num_rows === 0) {
        throw new Exception('Kategori tidak ditemukan');
    }

    // Handle upload gambar jika ada
    $gambar = $oldProduk['gambar'];
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../assets/img/produk/';
        
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        // Hapus gambar lama jika ada
        if (!empty($oldProduk['gambar']) && file_exists($uploadDir . $oldProduk['gambar'])) {
            unlink($uploadDir . $oldProduk['gambar']);
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

        if (!move_uploaded_file($_FILES['gambar']['tmp_name'], $filePath)) {
            throw new Exception('Gagal upload gambar');
        }

        $gambar = $fileName;
    }

    // Update produk
    $updateQuery = "UPDATE produk SET nama_produk = ?, deskripsi = ?, harga = ?, stok = ?, kategori_id = ?, gambar = ?, updated_at = NOW() WHERE id = ?";
    $stmt = $conn->prepare($updateQuery);
    
    if (!$stmt) {
        throw new Exception('Preparasi query gagal: ' . $conn->error);
    }

    $stmt->bind_param('ssdiisi', $nama_produk, $deskripsi, $harga, $stok, $kategori_id, $gambar, $id);
    
    if (!$stmt->execute()) {
        throw new Exception('Gagal mengupdate produk: ' . $stmt->error);
    }

    // Flash message
    setFlash('produk', 'Produk berhasil diperbarui', 'success');

    // Redirect ke halaman produk
    header('Location: ../admin/produk.php');
    exit;

} catch (Exception $e) {
    // Log error dan set flash message
    error_log('Edit Produk Error: ' . $e->getMessage());
    setFlash('produk', $e->getMessage(), 'danger');
    header('Location: ../admin/edit_produk.php?id=' . (isset($_POST['id']) ? intval($_POST['id']) : ''));
    exit;
}
