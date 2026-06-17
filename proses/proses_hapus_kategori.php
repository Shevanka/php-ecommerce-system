<?php
<<<<<<< HEAD
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/session.php';
requireAdmin();

$id = (int) ($_GET['id'] ?? 0);

if ($id <= 0) {
    setFlash('danger', 'ID kategori tidak valid.');
    header('Location: ../admin/kategori.php');
    exit;
}

// Cek apakah masih ada produk di kategori ini
$cek = $conn->prepare('SELECT COUNT(*) FROM produk WHERE kategori_id = ?');
$cek->bind_param('i', $id);
$cek->execute();
$jumlah = $cek->get_result()->fetch_row()[0];

if ($jumlah > 0) {
    setFlash('warning', "Kategori tidak bisa dihapus karena masih memiliki $jumlah produk.");
    header('Location: ../admin/kategori.php');
    exit;
}

// Ambil nama untuk flash message
$ambilNama = $conn->prepare('SELECT nama FROM kategori WHERE id = ?');
$ambilNama->bind_param('i', $id);
$ambilNama->execute();
$row = $ambilNama->get_result()->fetch_assoc();

if (!$row) {
    setFlash('danger', 'Kategori tidak ditemukan.');
    header('Location: ../admin/kategori.php');
    exit;
}

// Hapus dari database
$stmt = $conn->prepare('DELETE FROM kategori WHERE id = ?');
$stmt->bind_param('i', $id);

if ($stmt->execute()) {
    setFlash('success', "Kategori \"{$row['nama']}\" berhasil dihapus.");
} else {
    setFlash('danger', 'Gagal menghapus kategori: ' . $conn->error);
}

header('Location: ../admin/kategori.php');
exit;
=======
/**
 * Proses Hapus Kategori
 * Menangani penghapusan kategori (Admin)
 */

require_once '../config/database.php';
require_once '../config/session.php';

try {
    // Validasi autentikasi dan autorisasi
    requireAdmin();

    // Ambil ID kategori
    $id = isset($_GET['id']) ? intval($_GET['id']) : 0;

    // Validasi ID
    if ($id <= 0) {
        throw new Exception('ID kategori tidak valid');
    }

    // Ambil data kategori
    $query = "SELECT * FROM kategori WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        throw new Exception('Kategori tidak ditemukan');
    }

    // Periksa apakah kategori memiliki produk
    $checkProduk = "SELECT COUNT(*) as total FROM produk WHERE kategori_id = ?";
    $stmt = $conn->prepare($checkProduk);
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if ($row['total'] > 0) {
        throw new Exception('Kategori masih memiliki produk. Hapus semua produk dalam kategori terlebih dahulu.');
    }

    // Hapus kategori dari database
    $deleteQuery = "DELETE FROM kategori WHERE id = ?";
    $stmt = $conn->prepare($deleteQuery);
    
    if (!$stmt) {
        throw new Exception('Preparasi query gagal: ' . $conn->error);
    }

    $stmt->bind_param('i', $id);
    
    if (!$stmt->execute()) {
        throw new Exception('Gagal menghapus kategori: ' . $stmt->error);
    }

    // Flash message
    setFlash('kategori', 'Kategori berhasil dihapus', 'success');

    // Redirect ke halaman kategori
    header('Location: ../admin/kategori.php');
    exit;

} catch (Exception $e) {
    // Log error dan set flash message
    error_log('Hapus Kategori Error: ' . $e->getMessage());
    setFlash('kategori', $e->getMessage(), 'danger');
    header('Location: ../admin/kategori.php');
    exit;
}
>>>>>>> ae7e33b89ac0a04007ceeceae2da2177530b51c0
