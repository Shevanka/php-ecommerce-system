<?php
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
