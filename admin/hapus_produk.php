<?php
require_once '../config/database.php';
require_once '../config/session.php';
requireAdmin();

$id = (int) ($_GET['id'] ?? 0);

if ($id <= 0) {
    setFlash('danger', 'ID produk tidak valid.');
    header('Location: ../admin/produk.php');
    exit;
}

// ── Cek apakah produk masih ada di detail_pesanan ──
$cekPesanan = mysqli_prepare($conn, "SELECT COUNT(*) FROM detail_pesanan WHERE produk_id = ?");
mysqli_stmt_bind_param($cekPesanan, 'i', $id);
mysqli_stmt_execute($cekPesanan);
$jmlPesanan = mysqli_fetch_row(mysqli_stmt_get_result($cekPesanan))[0];

if ($jmlPesanan > 0) {
    setFlash('warning', "Produk tidak bisa dihapus karena masih ada di $jmlPesanan pesanan.");
    header('Location: ../admin/produk.php');
    exit;
}

// ── Ambil nama file gambar sebelum dihapus ──
$cekProduk = mysqli_prepare($conn, "SELECT nama, gambar FROM produk WHERE id = ?");
mysqli_stmt_bind_param($cekProduk, 'i', $id);
mysqli_stmt_execute($cekProduk);
$produk = mysqli_fetch_assoc(mysqli_stmt_get_result($cekProduk));

if (!$produk) {
    setFlash('danger', 'Produk tidak ditemukan.');
    header('Location: ../admin/produk.php');
    exit;
}

// ── Hapus dari database ──
$stmt = mysqli_prepare($conn, "DELETE FROM produk WHERE id = ?");
mysqli_stmt_bind_param($stmt, 'i', $id);

if (mysqli_stmt_execute($stmt)) {
    // Hapus file gambar jika ada
    if ($produk['gambar']) {
        $filePath = __DIR__ . '/../assets/img/produk/' . $produk['gambar'];
        if (file_exists($filePath)) {
            unlink($filePath);
        }
    }
    setFlash('success', "Produk \"{$produk['nama']}\" berhasil dihapus.");
} else {
    setFlash('danger', 'Gagal menghapus produk: ' . mysqli_error($conn));
}

header('Location: ../admin/produk.php');
exit;
