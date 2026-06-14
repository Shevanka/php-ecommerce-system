<?php
require_once __DIR__ . '/config/session.php';
requireLogin();

if (empty($_SESSION['cart'])) {
    setFlash('cart', 'Keranjang belanja kosong', 'warning');
    header('Location: cart.php');
    exit;
}

$pageTitle = 'Checkout';
require __DIR__ . '/includes/header.php';
require __DIR__ . '/includes/navbar.php';
?>
<main class="section">
    <div class="container">
        <div class="section-head"><h1>Checkout</h1><p>Lengkapi data penerima dan pembayaran.</p></div>
        <?php require __DIR__ . '/includes/alert.php'; ?>
        <form action="proses/proses_checkout.php" method="post" class="feature-card">
            <p><label>Nama Penerima<br><input type="text" name="nama_penerima" required></label></p>
            <p><label>Alamat<br><textarea name="alamat" rows="4" required></textarea></label></p>
            <p><label>Nomor Telepon<br><input type="tel" name="telepon" placeholder="081234567890" required></label></p>
            <p><label>Metode Pembayaran<br><select name="metode_pembayaran" required><option value="">Pilih</option><option value="transfer">Transfer</option><option value="cod">COD</option><option value="kartu_kredit">Kartu Kredit</option></select></label></p>
            <p><label>Catatan<br><textarea name="catatan" rows="2"></textarea></label></p>
            <button class="btn btn-primary" type="submit">Buat Pesanan</button>
        </form>
    </div>
</main>
<?php require __DIR__ . '/includes/footer.php'; ?>
