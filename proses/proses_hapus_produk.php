<?php
<<<<<<< HEAD
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/session.php';
requireAdmin();

$id = (int) ($_GET['id'] ?? 0);

if ($id <= 0) {
    setFlash('danger', 'ID produk tidak valid.');
    header('Location: ../admin/produk.php');
    exit;
}

// Cek apakah produk masih ada di detail_pesanan
$cekPesanan = $conn->prepare('SELECT COUNT(*) FROM detail_pesanan WHERE produk_id = ?');
$cekPesanan->bind_param('i', $id);
$cekPesanan->execute();
$jmlPesanan = $cekPesanan->get_result()->fetch_row()[0];

if ($jmlPesanan > 0) {
    setFlash('warning', "Produk tidak bisa dihapus karena masih ada di $jmlPesanan pesanan.");
    header('Location: ../admin/produk.php');
    exit;
}

// Ambil nama dan gambar sebelum dihapus
$cekProduk = $conn->prepare('SELECT nama, gambar FROM produk WHERE id = ?');
$cekProduk->bind_param('i', $id);
$cekProduk->execute();
$produk = $cekProduk->get_result()->fetch_assoc();

if (!$produk) {
    setFlash('danger', 'Produk tidak ditemukan.');
    header('Location: ../admin/produk.php');
    exit;
}

// Hapus dari database
$stmt = $conn->prepare('DELETE FROM produk WHERE id = ?');
$stmt->bind_param('i', $id);

if ($stmt->execute()) {
    if ($produk['gambar']) {
        $filePath = __DIR__ . '/../assets/img/produk/' . $produk['gambar'];
=======
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
>>>>>>> ae7e33b89ac0a04007ceeceae2da2177530b51c0
        if (file_exists($filePath)) {
            unlink($filePath);
        }
    }
<<<<<<< HEAD
    setFlash('success', "Produk \"{$produk['nama']}\" berhasil dihapus.");
} else {
    setFlash('danger', 'Gagal menghapus produk: ' . $conn->error);
}

header('Location: ../admin/produk.php');
exit;
=======

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
>>>>>>> ae7e33b89ac0a04007ceeceae2da2177530b51c0
