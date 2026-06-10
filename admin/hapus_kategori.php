<?php
require_once '../config/database.php';
require_once '../config/session.php';
requireAdmin();

$id = (int) ($_GET['id'] ?? 0);

if ($id <= 0) {
    setFlash('danger', 'ID kategori tidak valid.');
    header('Location: ../admin/kategori.php');
    exit;
}

// Cek apakah masih ada produk di kategori ini
$cek = mysqli_prepare($conn, "SELECT COUNT(*) FROM produk WHERE kategori_id = ?");
mysqli_stmt_bind_param($cek, 'i', $id);
mysqli_stmt_execute($cek);
$jumlah = mysqli_fetch_row(mysqli_stmt_get_result($cek))[0];

if ($jumlah > 0) {
    setFlash('warning', "Kategori tidak bisa dihapus karena masih memiliki $jumlah produk.");
    header('Location: ../admin/kategori.php');
    exit;
}

// Ambil nama dulu untuk flash message
$ambilNama = mysqli_prepare($conn, "SELECT nama FROM kategori WHERE id = ?");
mysqli_stmt_bind_param($ambilNama, 'i', $id);
mysqli_stmt_execute($ambilNama);
$row = mysqli_fetch_assoc(mysqli_stmt_get_result($ambilNama));

if (!$row) {
    setFlash('danger', 'Kategori tidak ditemukan.');
    header('Location: ../admin/kategori.php');
    exit;
}

$stmt = mysqli_prepare($conn, "DELETE FROM kategori WHERE id = ?");
mysqli_stmt_bind_param($stmt, 'i', $id);

if (mysqli_stmt_execute($stmt)) {
    setFlash('success', "Kategori \"{$row['nama']}\" berhasil dihapus.");
} else {
    setFlash('danger', 'Gagal menghapus kategori: ' . mysqli_error($conn));
}

header('Location: ../admin/kategori.php');
exit;
