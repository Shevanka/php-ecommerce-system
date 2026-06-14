<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/session.php';

try {
    requireLogin();

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new RuntimeException('Method tidak diizinkan');
    }

    if (empty($_SESSION['cart'])) {
        throw new RuntimeException('Keranjang belanja kosong');
    }

    $namaPenerima = trim($_POST['nama_penerima'] ?? '');
    $alamat = trim($_POST['alamat'] ?? '');
    $telepon = trim($_POST['telepon'] ?? '');
    $metode = trim($_POST['metode_pembayaran'] ?? '');
    $catatan = trim($_POST['catatan'] ?? '');

    if ($namaPenerima === '' || $alamat === '' || $telepon === '') {
        throw new RuntimeException('Data penerima wajib diisi lengkap');
    }
    if (!preg_match('/^(?:\+62|0)[0-9]{9,12}$/', $telepon)) {
        throw new RuntimeException('Format nomor telepon tidak valid');
    }
    if (!in_array($metode, ['transfer', 'cod', 'kartu_kredit'], true)) {
        throw new RuntimeException('Metode pembayaran tidak valid');
    }

    $conn->begin_transaction();

    $items = [];
    $total = 0.0;
    $productStmt = $conn->prepare('SELECT nama, harga, stok FROM produk WHERE id = ? FOR UPDATE');

    foreach ($_SESSION['cart'] as $item) {
        $produkId = (int) $item['produk_id'];
        $jumlah = (int) $item['jumlah'];
        $productStmt->bind_param('i', $produkId);
        $productStmt->execute();
        $produk = $productStmt->get_result()->fetch_assoc();

        if (!$produk || $jumlah < 1) {
            throw new RuntimeException('Produk dalam keranjang tidak valid');
        }
        if ($jumlah > (int) $produk['stok']) {
            throw new RuntimeException('Stok ' . $produk['nama'] . ' tidak cukup');
        }

        $harga = (float) $produk['harga'];
        $total += $harga * $jumlah;
        $items[] = compact('produkId', 'jumlah', 'harga');
    }

    $userId = (int) $_SESSION['user_id'];
    $orderStmt = $conn->prepare(
        'INSERT INTO pesanan (user_id, total, status, nama_penerima, alamat, telepon, metode_pembayaran, catatan) VALUES (?, ?, ?, ?, ?, ?, ?, ?)'
    );
    $status = 'pending';
    $orderStmt->bind_param('idssssss', $userId, $total, $status, $namaPenerima, $alamat, $telepon, $metode, $catatan);
    $orderStmt->execute();
    $pesananId = $conn->insert_id;

    $detailStmt = $conn->prepare('INSERT INTO detail_pesanan (pesanan_id, produk_id, qty, harga) VALUES (?, ?, ?, ?)');
    $stockStmt = $conn->prepare('UPDATE produk SET stok = stok - ? WHERE id = ?');

    foreach ($items as $item) {
        $detailStmt->bind_param('iiid', $pesananId, $item['produkId'], $item['jumlah'], $item['harga']);
        $detailStmt->execute();
        $stockStmt->bind_param('ii', $item['jumlah'], $item['produkId']);
        $stockStmt->execute();
    }

    $conn->commit();
    $_SESSION['cart'] = [];
    setFlash('checkout', 'Pesanan #' . $pesananId . ' berhasil dibuat', 'success');
    header('Location: ../riwayat_pesanan.php');
    exit;
} catch (Throwable $e) {
    if (isset($conn) && $conn instanceof mysqli) {
        try {
            $conn->rollback();
        } catch (Throwable) {
        }
    }
    error_log('Checkout Error: ' . $e->getMessage());
    setFlash('checkout', $e->getMessage(), 'danger');
    header('Location: ../checkout.php');
    exit;
}
