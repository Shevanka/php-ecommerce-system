<?php
<<<<<<< HEAD
declare(strict_types=1);

if (!function_exists('currentUserName')) {
    function currentUserName(): string
    {
        return (string) ($_SESSION['user_name'] ?? $_SESSION['username'] ?? 'Pengguna');
    }
}

$cartCount = function_exists('cartCount') ? cartCount() : 0;
?>
<header class="site-header">
    <div class="container header-inner">

        <a class="brand" href="/ta2/index.php">
            <img src="/ta2/assets/img/logo.png" alt="" class="brand-logo"
                 onerror="this.style.display='none'">
            <span>Penjualan Online</span>
        </a>

        <button type="button" class="nav-toggle"
                aria-label="Buka menu" aria-expanded="false">
=======

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
>>>>>>> ae7e33b89ac0a04007ceeceae2da2177530b51c0
            <span></span><span></span><span></span>
        </button>

        <nav class="main-nav" id="main-nav">
<<<<<<< HEAD
            <a href="/ta2/index.php#produk">Produk</a>
            <a href="/ta2/index.php#fitur">Fitur</a>

            <a href="/ta2/cart.php" class="nav-cart">
=======
            <a href="index.php#produk">Produk</a>
            <a href="index.php#fitur">Fitur</a>
            <a href="cart.php" class="nav-cart">
>>>>>>> ae7e33b89ac0a04007ceeceae2da2177530b51c0
                Keranjang
                <?php if ($cartCount > 0): ?>
                    <span class="badge"><?= $cartCount ?></span>
                <?php endif; ?>
            </a>

            <?php if (isLoggedIn()): ?>
<<<<<<< HEAD
                <a href="/ta2/riwayat_pesanan.php">Riwayat</a>

                <?php if (isAdmin()): ?>
                    <a href="/ta2/admin/dashboard.php" class="nav-admin">Admin</a>
                <?php endif; ?>

                <span class="nav-user">
                    <?= htmlspecialchars(currentUserName(), ENT_QUOTES, 'UTF-8') ?>
                </span>

                <a href="/ta2/logout.php" class="btn btn-outline btn-sm">Logout</a>
            <?php else: ?>
                <a href="/ta2/login.php" class="nav-link">Login</a>
                <a href="/ta2/register.php" class="btn btn-primary btn-sm">Daftar</a>
            <?php endif; ?>
        </nav>

    </div>
</header>
=======
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
>>>>>>> ae7e33b89ac0a04007ceeceae2da2177530b51c0
