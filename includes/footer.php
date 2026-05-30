<?php

declare(strict_types=1);
?>
<footer class="site-footer">
    <div class="container footer-grid">
        <div>
            <strong>Penjualan Online</strong>
            <p>Platform e-commerce dengan autentikasi session, role admin, dan checkout aman.</p>
        </div>
        <div>
            <strong>Pengguna</strong>
            <ul>
                <li><a href="register.php">Registrasi</a></li>
                <li><a href="login.php">Login</a></li>
                <li><a href="cart.php">Keranjang</a></li>
                <li><a href="checkout.php">Checkout</a></li>
            </ul>
        </div>
        <div>
            <strong>Admin</strong>
            <ul>
                <li><a href="login.php">Login Admin</a></li>
                <li><a href="admin/dashboard.php">Dashboard</a></li>
                <li><a href="admin/produk.php">Kelola Produk</a></li>
                <li><a href="admin/pesanan.php">Kelola Pesanan</a></li>
            </ul>
        </div>
    </div>
    <div class="container footer-bottom">
        <p>&copy; <?= date('Y') ?> Penjualan Online. Session-based auth &amp; RBAC.</p>
    </div>
</footer>
<script src="assets/js/script.js"></script>
</body>
</html>
