<?php
<<<<<<< HEAD
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/session.php';
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

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
=======
/**
 * Proses Checkout
 * Menangani pemrosesan pesanan
 */

require_once '../config/database.php';
require_once '../config/session.php';

try {
    // Validasi autentikasi
    requireLogin();

    // Validasi method request
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Method tidak diizinkan');
    }

    // Validasi cart tidak kosong
    if (empty($_SESSION['cart'])) {
        throw new Exception('Keranjang belanja kosong');
    }

    // Ambil data checkout
    $nama_penerima = isset($_POST['nama_penerima']) ? trim($_POST['nama_penerima']) : '';
    $alamat = isset($_POST['alamat']) ? trim($_POST['alamat']) : '';
    $telepon = isset($_POST['telepon']) ? trim($_POST['telepon']) : '';
    $catatan = isset($_POST['catatan']) ? trim($_POST['catatan']) : '';
    $metode_pembayaran = isset($_POST['metode_pembayaran']) ? trim($_POST['metode_pembayaran']) : '';

    // Validasi input
    if (empty($nama_penerima)) {
        throw new Exception('Nama penerima harus diisi');
    }

    if (empty($alamat)) {
        throw new Exception('Alamat harus diisi');
    }

    if (empty($telepon)) {
        throw new Exception('Nomor telepon harus diisi');
    }

    if (!preg_match('/^(\+62|0)[0-9]{9,12}$/', $telepon)) {
        throw new Exception('Format nomor telepon tidak valid');
    }

    if (empty($metode_pembayaran)) {
        throw new Exception('Metode pembayaran harus dipilih');
    }

    // Validasi metode pembayaran
    $allowedMethods = ['transfer', 'cod', 'kartu_kredit'];
    if (!in_array($metode_pembayaran, $allowedMethods)) {
        throw new Exception('Metode pembayaran tidak valid');
    }

    // Mulai transaction
    $conn->begin_transaction();

    try {
        // Hitung total harga
        $total_harga = 0;
        $cartItems = [];

        foreach ($_SESSION['cart'] as $cartKey => $item) {
            // Validasi stok real-time
            $produk_id = $item['produk_id'];
            $query = "SELECT stok FROM produk WHERE id = ? FOR UPDATE";
            $stmt = $conn->prepare($query);
            $stmt->bind_param('i', $produk_id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows === 0) {
                throw new Exception('Produk tidak ditemukan');
            }

            $produk = $result->fetch_assoc();
            if ($item['jumlah'] > $produk['stok']) {
                throw new Exception('Stok ' . $item['nama_produk'] . ' tidak cukup');
            }

            $total_harga += $item['harga'] * $item['jumlah'];
            $cartItems[] = $item;
        }

        // Insert pesanan
        $user_id = $_SESSION['user_id'];
        $status_pesanan = 'pending';
        $tgl_pesanan = date('Y-m-d H:i:s');

        $orderQuery = "INSERT INTO pesanan (user_id, tanggal_pesanan, total_harga, status_pesanan, nama_penerima, alamat, telepon, metode_pembayaran, catatan, created_at, updated_at) 
                       VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())";
        $stmt = $conn->prepare($orderQuery);

        if (!$stmt) {
            throw new Exception('Preparasi query gagal: ' . $conn->error);
        }

        $stmt->bind_param('isdsssss', $user_id, $tgl_pesanan, $total_harga, $status_pesanan, $nama_penerima, $alamat, $telepon, $metode_pembayaran);

        if (!$stmt->execute()) {
            throw new Exception('Gagal membuat pesanan: ' . $stmt->error);
        }

        // Ambil ID pesanan
        $pesanan_id = $conn->insert_id;

        // Insert detail pesanan dan update stok
        $detailQuery = "INSERT INTO detail_pesanan (pesanan_id, produk_id, jumlah, harga_satuan, subtotal, created_at, updated_at) 
                        VALUES (?, ?, ?, ?, ?, NOW(), NOW())";

        foreach ($cartItems as $item) {
            $stmt = $conn->prepare($detailQuery);
            $produk_id = $item['produk_id'];
            $jumlah = $item['jumlah'];
            $harga_satuan = $item['harga'];
            $subtotal = $harga_satuan * $jumlah;

            $stmt->bind_param('iiiid', $pesanan_id, $produk_id, $jumlah, $harga_satuan, $subtotal);

            if (!$stmt->execute()) {
                throw new Exception('Gagal membuat detail pesanan: ' . $stmt->error);
            }

            // Update stok produk
            $updateStokQuery = "UPDATE produk SET stok = stok - ? WHERE id = ?";
            $stmt = $conn->prepare($updateStokQuery);
            $stmt->bind_param('ii', $jumlah, $produk_id);

            if (!$stmt->execute()) {
                throw new Exception('Gagal update stok: ' . $stmt->error);
            }
        }

        // Commit transaction
        $conn->commit();

        // Clear cart
        $_SESSION['cart'] = [];

        // Flash message
        setFlash('checkout', 'Pesanan berhasil dibuat! No. Pesanan: ' . $pesanan_id, 'success');

        // Redirect ke halaman riwayat pesanan
        header('Location: ../riwayat_pesanan.php');
        exit;

    } catch (Exception $e) {
        // Rollback transaction jika error
        $conn->rollback();
        throw $e;
    }

} catch (Exception $e) {
    // Log error dan set flash message
>>>>>>> ae7e33b89ac0a04007ceeceae2da2177530b51c0
    error_log('Checkout Error: ' . $e->getMessage());
    setFlash('checkout', $e->getMessage(), 'danger');
    header('Location: ../checkout.php');
    exit;
}
