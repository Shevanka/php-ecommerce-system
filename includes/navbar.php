<?php

declare(strict_types=1);

$cartCount = (int) ($_SESSION['cart_count'] ?? 0);
?>
<header class="site-header">
    <div class="container header-inner">
        <a class="brand" href="index.php">
            <img src="assets/img/logo.png" alt="" class="brand-logo" onerror="this.style.display='none'">
            <span>Penjualan Online</span>
        </a>

        <button type="button" class="nav-toggle" aria-label="Buka menu" aria-expanded="false">
            <span></span><span></span><span></span>
        </button>

        <nav class="main-nav" id="main-nav">
            <a href="index.php#produk">Produk</a>
            <a href="index.php#fitur">Fitur</a>
            <a href="cart.php" class="nav-cart">
                Keranjang
                <?php if ($cartCount > 0): ?>
                    <span class="badge"><?= $cartCount ?></span>
                <?php endif; ?>
            </a>

            <?php if (isLoggedIn()): ?>
                <a href="riwayat_pesanan.php">Riwayat Pesanan</a>
                <?php if (isAdmin()): ?>
                    <a href="admin/dashboard.php" class="nav-admin">Admin</a>
                <?php endif; ?>
                <span class="nav-user">Halo, <?= htmlspecialchars(currentUserName(), ENT_QUOTES, 'UTF-8') ?></span>
                <a href="logout.php" class="btn btn-outline btn-sm">Logout</a>
            <?php else: ?>
                <a href="login.php">Login</a>
                <a href="register.php" class="btn btn-primary btn-sm">Daftar</a>
            <?php endif; ?>
        </nav>
    </div>
</header>
