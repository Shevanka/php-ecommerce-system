<?php
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
        if (file_exists($filePath)) {
            unlink($filePath);
        }
    }
    setFlash('success', "Produk \"{$produk['nama']}\" berhasil dihapus.");
} else {
    setFlash('danger', 'Gagal menghapus produk: ' . $conn->error);
}

header('Location: ../admin/produk.php');
exit;
