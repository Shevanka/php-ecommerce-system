<?php
/**
 * Proses Cart
 * Menangani operasi keranjang belanja (Session-based)
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

    // Ambil action
    $action = isset($_POST['action']) ? trim($_POST['action']) : '';

    // Inisialisasi cart jika belum ada
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    switch ($action) {
        case 'add':
            handleAddToCart();
            break;
        case 'update':
            handleUpdateCart();
            break;
        case 'remove':
            handleRemoveFromCart();
            break;
        case 'clear':
            handleClearCart();
            break;
        default:
            throw new Exception('Action tidak valid');
    }

} catch (Exception $e) {
    error_log('Cart Error: ' . $e->getMessage());
    setFlash('cart', $e->getMessage(), 'danger');
    
    // Redirect ke halaman yang sesuai
    $referrer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '../cart.php';
    header('Location: ' . $referrer);
    exit;
}

function handleAddToCart() {
    global $conn;

    // Ambil data
    $produk_id = isset($_POST['produk_id']) ? intval($_POST['produk_id']) : 0;
    $jumlah = isset($_POST['jumlah']) ? intval($_POST['jumlah']) : 1;

    // Validasi
    if ($produk_id <= 0) {
        throw new Exception('ID produk tidak valid');
    }

    if ($jumlah <= 0) {
        throw new Exception('Jumlah harus lebih dari 0');
    }

    // Ambil data produk
    $query = "SELECT * FROM produk WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $produk_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        throw new Exception('Produk tidak ditemukan');
    }

    $produk = $result->fetch_assoc();

    // Validasi stok
    if ($jumlah > $produk['stok']) {
        throw new Exception('Stok tidak cukup. Stok tersedia: ' . $produk['stok']);
    }

    // Tambah ke cart atau update jumlah jika sudah ada
    $cartKey = 'produk_' . $produk_id;
    if (isset($_SESSION['cart'][$cartKey])) {
<<<<<<< HEAD
        $jumlahBaru = $_SESSION['cart'][$cartKey]['jumlah'] + $jumlah;
        if ($jumlahBaru > $produk['stok']) {
            throw new Exception('Jumlah melebihi stok yang tersedia');
        }
        $_SESSION['cart'][$cartKey]['jumlah'] = $jumlahBaru;
    } else {
        $_SESSION['cart'][$cartKey] = [
            'produk_id' => $produk_id,
            'nama_produk' => $produk['nama'],
=======
        $_SESSION['cart'][$cartKey]['jumlah'] += $jumlah;
    } else {
        $_SESSION['cart'][$cartKey] = [
            'produk_id' => $produk_id,
            'nama_produk' => $produk['nama_produk'],
>>>>>>> ae7e33b89ac0a04007ceeceae2da2177530b51c0
            'harga' => $produk['harga'],
            'jumlah' => $jumlah,
            'gambar' => $produk['gambar']
        ];
    }

<<<<<<< HEAD
    setFlash('cart', $produk['nama'] . ' ditambahkan ke keranjang', 'success');
=======
    setFlash('cart', $produk['nama_produk'] . ' ditambahkan ke keranjang', 'success');
>>>>>>> ae7e33b89ac0a04007ceeceae2da2177530b51c0
    header('Location: ../cart.php');
    exit;
}

function handleUpdateCart() {
    global $conn;

    // Ambil data
    $produk_id = isset($_POST['produk_id']) ? intval($_POST['produk_id']) : 0;
    $jumlah = isset($_POST['jumlah']) ? intval($_POST['jumlah']) : 0;

    // Validasi
    if ($produk_id <= 0) {
        throw new Exception('ID produk tidak valid');
    }

    if ($jumlah <= 0) {
        throw new Exception('Jumlah harus lebih dari 0');
    }

    // Ambil data produk
    $query = "SELECT * FROM produk WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $produk_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        throw new Exception('Produk tidak ditemukan');
    }

    $produk = $result->fetch_assoc();

    // Validasi stok
    if ($jumlah > $produk['stok']) {
        throw new Exception('Stok tidak cukup. Stok tersedia: ' . $produk['stok']);
    }

    // Update cart
    $cartKey = 'produk_' . $produk_id;
    if (isset($_SESSION['cart'][$cartKey])) {
        $_SESSION['cart'][$cartKey]['jumlah'] = $jumlah;
        setFlash('cart', 'Keranjang berhasil diperbarui', 'success');
    } else {
        throw new Exception('Produk tidak ada di keranjang');
    }

    header('Location: ../cart.php');
    exit;
}

function handleRemoveFromCart() {
    // Ambil data
    $produk_id = isset($_POST['produk_id']) ? intval($_POST['produk_id']) : 0;

    // Validasi
    if ($produk_id <= 0) {
        throw new Exception('ID produk tidak valid');
    }

    $cartKey = 'produk_' . $produk_id;
    if (isset($_SESSION['cart'][$cartKey])) {
        $nama = $_SESSION['cart'][$cartKey]['nama_produk'];
        unset($_SESSION['cart'][$cartKey]);
        setFlash('cart', $nama . ' dihapus dari keranjang', 'info');
    } else {
        throw new Exception('Produk tidak ada di keranjang');
    }

    header('Location: ../cart.php');
    exit;
}

function handleClearCart() {
    $_SESSION['cart'] = [];
    setFlash('cart', 'Keranjang berhasil dikosongkan', 'info');
    header('Location: ../cart.php');
    exit;
}
