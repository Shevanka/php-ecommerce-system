<?php
/**
 * Proses Tambah Kategori
 * Menangani penambahan kategori baru (Admin)
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
    $nama_kategori = isset($_POST['nama_kategori']) ? trim($_POST['nama_kategori']) : '';
    $deskripsi = isset($_POST['deskripsi']) ? trim($_POST['deskripsi']) : '';

    // Validasi input
    if (empty($nama_kategori)) {
        throw new Exception('Nama kategori harus diisi');
    }

    // Periksa duplikat kategori
    $checkQuery = "SELECT id FROM kategori WHERE nama_kategori = ?";
    $stmt = $conn->prepare($checkQuery);
    $stmt->bind_param('s', $nama_kategori);
    $stmt->execute();
    
    if ($stmt->get_result()->num_rows > 0) {
        throw new Exception('Kategori sudah ada');
    }

    // Insert kategori ke database
    $query = "INSERT INTO kategori (nama_kategori, deskripsi, created_at, updated_at) 
              VALUES (?, ?, NOW(), NOW())";
    $stmt = $conn->prepare($query);
    
    if (!$stmt) {
        throw new Exception('Preparasi query gagal: ' . $conn->error);
    }

    $stmt->bind_param('ss', $nama_kategori, $deskripsi);
    
    if (!$stmt->execute()) {
        throw new Exception('Gagal menambah kategori: ' . $stmt->error);
    }

    // Flash message
    setFlash('kategori', 'Kategori berhasil ditambahkan', 'success');

    // Redirect ke halaman kategori
    header('Location: ../admin/kategori.php');
    exit;

} catch (Exception $e) {
    // Log error dan set flash message
    error_log('Tambah Kategori Error: ' . $e->getMessage());
    setFlash('kategori', $e->getMessage(), 'danger');
    header('Location: ../admin/tambah_kategori.php');
    exit;
}
