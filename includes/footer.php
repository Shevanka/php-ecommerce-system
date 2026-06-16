<?php declare(strict_types=1); ?>
<footer class="site-footer">
    <div class="container footer-grid">

        <div>
            <strong>Penjualan Online</strong>
            <p>Platform e-commerce dengan autentikasi session, role admin, dan checkout aman.</p>
        </div>

        <div>
            <strong>Belanja</strong>
            <ul>
                <li><a href="/ta2/index.php#produk">Katalog Produk</a></li>
                <li><a href="/ta2/cart.php">Keranjang</a></li>
                <li><a href="/ta2/checkout.php">Checkout</a></li>
                <li><a href="/ta2/riwayat_pesanan.php">Riwayat Pesanan</a></li>
            </ul>
        </div>

        <div>
            <strong>Akun</strong>
            <ul>
                <li><a href="/ta2/register.php">Registrasi</a></li>
                <li><a href="/ta2/login.php">Login</a></li>
            </ul>
        </div>

        <div>
            <strong>Admin</strong>
            <ul>
                <li><a href="/ta2/admin/dashboard.php">Dashboard</a></li>
                <li><a href="/ta2/admin/produk.php">Kelola Produk</a></li>
                <li><a href="/ta2/admin/kategori.php">Kelola Kategori</a></li>
                <li><a href="/ta2/admin/pesanan.php">Kelola Pesanan</a></li>
            </ul>
        </div>

    </div>

    <div class="footer-bottom">
        <div class="container">
            &copy; <?= date('Y') ?> Penjualan Online &mdash; Session-based auth &amp; RBAC
        </div>
    </div>
</footer>

<script src="/ta2/assets/js/script.js"></script>
</body>
</html>