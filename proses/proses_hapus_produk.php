<?php
/**
 * Proses Hapus Produk
 * Menangani penghapusan produk (Admin)
 */

require_once '../config/database.php';
require_once '../config/session.php';

try {
    // Validasi autentikasi dan autorisasi
    requireAdmin();

    // Ambil ID produk
    $id = isset($_GET['id']) ? intval($_GET['id']) : 0;

    // Validasi ID
    if ($id <= 0) {
        throw new Exception('ID produk tidak valid');
    }

    // Ambil data produk
    $query = "SELECT * FROM produk WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        throw new Exception('Produk tidak ditemukan');
    }

    $produk = $result->fetch_assoc();

    // Hapus gambar jika ada
    if (!empty($produk['gambar'])) {
        $filePath = '../assets/img/produk/' . $produk['gambar'];
        if (file_exists($filePath)) {
            unlink($filePath);
        }
    }

    // Hapus produk dari database
    $deleteQuery = "DELETE FROM produk WHERE id = ?";
    $stmt = $conn->prepare($deleteQuery);
    
    if (!$stmt) {
        throw new Exception('Preparasi query gagal: ' . $conn->error);
    }

    $stmt->bind_param('i', $id);
    
    if (!$stmt->execute()) {
        throw new Exception('Gagal menghapus produk: ' . $stmt->error);
    }

    // Flash message
    setFlash('produk', 'Produk berhasil dihapus', 'success');

    // Redirect ke halaman produk
    header('Location: ../admin/produk.php');
    exit;

} catch (Exception $e) {
    // Log error dan set flash message
    error_log('Hapus Produk Error: ' . $e->getMessage());
    setFlash('produk', $e->getMessage(), 'danger');
    header('Location: ../admin/produk.php');
    exit;
}
