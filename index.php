<?php
<<<<<<< HEAD
declare(strict_types=1);

// ── Bootstrap ──────────────────────────────────────────
require_once __DIR__ . '/config/session.php';
require_once __DIR__ . '/config/database.php';

// ── Input ──────────────────────────────────────────────
$kategoriId = filter_input(INPUT_GET, 'kategori', FILTER_VALIDATE_INT);
$search     = mb_substr(trim((string) (filter_input(INPUT_GET, 'q', FILTER_UNSAFE_RAW) ?? '')), 0, 100);

// ── Data ───────────────────────────────────────────────
$kategoriList  = [];
$produkList    = [];
$dbError       = null;
$totalProduk   = 0;
$totalKategori = 0;
$totalPesanan  = 0;

// Build WHERE clause
$conditions = ['p.stok > 0'];
$params     = [];
$types      = '';

if ($kategoriId !== false && $kategoriId !== null && $kategoriId > 0) {
    $conditions[] = 'p.kategori_id = ?';
    $params[]     = $kategoriId;
    $types       .= 'i';
}

if ($search !== '') {
    $conditions[] = '(p.nama LIKE ? OR p.deskripsi LIKE ?)';
    $searchParam  = '%' . $search . '%';
    $params[]     = $searchParam;
    $params[]     = $searchParam;
    $types       .= 'ss';
}

$whereClause = 'WHERE ' . implode(' AND ', $conditions);

try {
    // Kategori list
    $resKategori = mysqli_query($conn, 'SELECT id, nama FROM kategori ORDER BY nama ASC');
    if (!$resKategori) throw new RuntimeException(mysqli_error($conn));
    while ($row = mysqli_fetch_assoc($resKategori)) {
        $kategoriList[] = $row;
    }

    // Produk list
    $sql = "
        SELECT p.id, p.nama, p.deskripsi, p.harga, p.stok, p.gambar, k.nama AS kategori_nama
        FROM produk p
        INNER JOIN kategori k ON k.id = p.kategori_id
        $whereClause
        ORDER BY p.created_at DESC
    ";

    if ($params) {
        $stmt = mysqli_prepare($conn, $sql);
        if (!$stmt) throw new RuntimeException(mysqli_error($conn));
        mysqli_stmt_bind_param($stmt, $types, ...$params);
        mysqli_stmt_execute($stmt);
        $res = mysqli_stmt_get_result($stmt);
    } else {
        $res = mysqli_query($conn, $sql);
        if (!$res) throw new RuntimeException(mysqli_error($conn));
    }

    while ($row = mysqli_fetch_assoc($res)) {
        $produkList[] = $row;
    }

    // Counts
    $totalProduk   = (int) mysqli_fetch_row(mysqli_query($conn, 'SELECT COUNT(*) FROM produk WHERE stok > 0'))[0];
    $totalKategori = (int) mysqli_fetch_row(mysqli_query($conn, 'SELECT COUNT(*) FROM kategori'))[0];
    $totalPesanan  = (int) mysqli_fetch_row(mysqli_query($conn, 'SELECT COUNT(*) FROM pesanan'))[0];

} catch (Throwable $e) {
    $dbError = 'Database belum siap. Import file database/penjualan_online.sql terlebih dahulu. (' . $e->getMessage() . ')';
}

// ── Helpers ────────────────────────────────────────────
=======

declare(strict_types=1);

require_once __DIR__ . '/config/session.php';
require_once __DIR__ . '/config/database.php';

$pageTitle = 'Beranda';
$bodyClass = 'page-home';

$kategoriId = filter_input(INPUT_GET, 'kategori', FILTER_VALIDATE_INT);
$search = trim((string) (filter_input(INPUT_GET, 'q', FILTER_UNSAFE_RAW) ?? ''));
$search = mb_substr($search, 0, 100);

$kategoriList = [];
$produkList = [];
$dbError = null;

try {
    $pdo = db();

    $kategoriList = $pdo->query(
        'SELECT id, nama FROM kategori ORDER BY nama ASC'
    )->fetchAll();

    $sql = '
        SELECT p.id, p.nama, p.deskripsi, p.harga, p.stok, p.gambar, k.nama AS kategori_nama
        FROM produk p
        INNER JOIN kategori k ON k.id = p.kategori_id
        WHERE p.stok > 0
    ';
    $params = [];

    if ($kategoriId !== false && $kategoriId !== null && $kategoriId > 0) {
        $sql .= ' AND p.kategori_id = :kategori_id';
        $params['kategori_id'] = $kategoriId;
    }

    if ($search !== '') {
        $sql .= ' AND (p.nama LIKE :search OR p.deskripsi LIKE :search)';
        $params['search'] = '%' . $search . '%';
    }

    $sql .= ' ORDER BY p.created_at DESC';

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $produkList = $stmt->fetchAll();

    $totalProduk = (int) $pdo->query('SELECT COUNT(*) FROM produk WHERE stok > 0')->fetchColumn();
    $totalKategori = (int) $pdo->query('SELECT COUNT(*) FROM kategori')->fetchColumn();
    $totalPesanan = (int) $pdo->query('SELECT COUNT(*) FROM pesanan')->fetchColumn();
} catch (PDOException) {
    $dbError = 'Database belum siap. Import file config/penjualan_online.sql terlebih dahulu.';
    $totalProduk = 0;
    $totalKategori = 0;
    $totalPesanan = 0;
}

>>>>>>> ae7e33b89ac0a04007ceeceae2da2177530b51c0
function formatRupiah(float|int|string $amount): string
{
    return 'Rp ' . number_format((float) $amount, 0, ',', '.');
}

function productImageUrl(?string $gambar): ?string
{
<<<<<<< HEAD
    if ($gambar === null || $gambar === '') return null;
    $path = 'assets/img/produk/' . basename($gambar);
    return is_file(__DIR__ . '/' . $path) ? $path : null;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Toko online — belanja mudah, aman, dan cepat.">
    <title>Beranda | Penjualan Online</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700;1,9..40,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/ta2/assets/css/style.css">
</head>
<body>
<?php require __DIR__ . '/includes/navbar.php'; ?>

<main>
    <!-- ── Hero ── -->
=======
    if ($gambar === null || $gambar === '') {
        return null;
    }

    $path = 'assets/img/produk/' . basename($gambar);

    return is_file(__DIR__ . '/' . $path) ? $path : null;
}

require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>

<main>
>>>>>>> ae7e33b89ac0a04007ceeceae2da2177530b51c0
    <section class="hero">
        <div class="container hero-grid">
            <div>
                <span class="hero-eyebrow">E-Commerce PHP · Session &amp; RBAC</span>
                <h1>Belanja online mudah, aman, dan terintegrasi.</h1>
                <p>
                    Daftar akun, login dengan Remember Me, jelajahi katalog produk,
                    tambahkan ke keranjang, checkout, dan lacak riwayat pesanan.
                    Admin dapat mengelola produk, kategori, pesanan, dan transaksi.
                </p>
                <div class="hero-actions">
                    <a href="#produk" class="btn btn-primary">Lihat Produk</a>
                    <?php if (!isLoggedIn()): ?>
                        <a href="register.php" class="btn btn-outline">Buat Akun</a>
                    <?php else: ?>
<<<<<<< HEAD
                        <a href="cart.php" class="btn btn-outline">Ke Keranjang</a>                        
=======
                        <a href="cart.php" class="btn btn-outline">Ke Keranjang</a>
>>>>>>> ae7e33b89ac0a04007ceeceae2da2177530b51c0
                    <?php endif; ?>
                </div>
            </div>
            <div class="hero-card">
                <strong>Ringkasan toko</strong>
                <div class="hero-stats">
                    <div class="stat">
                        <strong><?= $totalProduk ?></strong>
                        <span>Produk aktif</span>
                    </div>
                    <div class="stat">
                        <strong><?= $totalKategori ?></strong>
                        <span>Kategori</span>
                    </div>
                    <div class="stat">
                        <strong><?= $totalPesanan ?></strong>
                        <span>Transaksi</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

<<<<<<< HEAD
    <!-- ── Fitur ── -->
=======
>>>>>>> ae7e33b89ac0a04007ceeceae2da2177530b51c0
    <section class="section" id="fitur">
        <div class="container">
            <div class="section-head">
                <h2>Fitur lengkap untuk pengguna &amp; admin</h2>
                <p>Autentikasi berbasis session, otorisasi role, validasi input, dan proteksi session.</p>
            </div>
            <div class="features-grid">
                <article class="feature-card">
                    <span class="feature-tag">Pengguna</span>
                    <h3>Registrasi &amp; Login</h3>
                    <p>Daftar akun, login/logout, dan Remember Me via cookie HttpOnly yang aman.</p>
                </article>
                <article class="feature-card">
                    <span class="feature-tag">Pengguna</span>
                    <h3>Belanja &amp; Checkout</h3>
                    <p>Lihat detail produk, tambah ke keranjang, checkout pesanan, dan riwayat pembelian.</p>
                </article>
                <article class="feature-card">
                    <span class="feature-tag">Admin</span>
                    <h3>Dashboard &amp; CRUD</h3>
                    <p>Kelola produk, kategori, pesanan, dan pantau data transaksi dari panel admin.</p>
                </article>
                <article class="feature-card">
                    <span class="feature-tag">Keamanan</span>
                    <h3>Session &amp; RBAC</h3>
                    <p>Session-based authentication, role user/admin, validasi input, dan CSRF token.</p>
                </article>
            </div>
        </div>
    </section>

<<<<<<< HEAD
    <!-- ── Katalog ── -->
=======
>>>>>>> ae7e33b89ac0a04007ceeceae2da2177530b51c0
    <section class="section" id="produk">
        <div class="container">
            <div class="section-head">
                <h2>Katalog Produk</h2>
                <p>Pilih kategori atau cari produk favorit Anda.</p>
            </div>

            <?php if ($dbError !== null): ?>
                <div class="alert alert-error"><?= htmlspecialchars($dbError, ENT_QUOTES, 'UTF-8') ?></div>
            <?php endif; ?>

            <div class="catalog-toolbar">
                <form class="search-form" method="get" action="index.php#produk">
                    <?php if ($kategoriId): ?>
                        <input type="hidden" name="kategori" value="<?= (int) $kategoriId ?>">
                    <?php endif; ?>
                    <input
                        type="search"
                        name="q"
                        placeholder="Cari produk..."
                        value="<?= htmlspecialchars($search, ENT_QUOTES, 'UTF-8') ?>"
                        maxlength="100"
                    >
                    <button type="submit" class="btn btn-primary btn-sm">Cari</button>
                </form>

                <div class="category-pills">
<<<<<<< HEAD
                    <a href="index.php#produk" class="pill<?= !$kategoriId ? ' is-active' : '' ?>">Semua</a>
=======
                    <a
                        href="index.php#produk"
                        class="pill<?= !$kategoriId ? ' is-active' : '' ?>"
                    >Semua</a>
>>>>>>> ae7e33b89ac0a04007ceeceae2da2177530b51c0
                    <?php foreach ($kategoriList as $kat): ?>
                        <a
                            href="index.php?kategori=<?= (int) $kat['id'] ?>#produk"
                            class="pill<?= $kategoriId === (int) $kat['id'] ? ' is-active' : '' ?>"
                        ><?= htmlspecialchars($kat['nama'], ENT_QUOTES, 'UTF-8') ?></a>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="product-grid">
                <?php if ($produkList === []): ?>
                    <div class="empty-state">
<<<<<<< HEAD
                        <p>
                            <?php if ($search !== ''): ?>
                                Tidak ada produk yang cocok dengan "<strong><?= htmlspecialchars($search, ENT_QUOTES, 'UTF-8') ?></strong>".
                            <?php else: ?>
                                Belum ada produk ditemukan.
                            <?php endif; ?>
                        </p>
                        <?php if ($search !== '' || $kategoriId): ?>
                            <a href="index.php#produk" class="btn btn-outline">Lihat Semua Produk</a>
                        <?php endif; ?>
=======
                        <p>Belum ada produk ditemukan.</p>
>>>>>>> ae7e33b89ac0a04007ceeceae2da2177530b51c0
                    </div>
                <?php else: ?>
                    <?php foreach ($produkList as $produk): ?>
                        <?php
<<<<<<< HEAD
                        $imgUrl    = productImageUrl($produk['gambar'] ?? null);
=======
                        $imgUrl = productImageUrl($produk['gambar'] ?? null);
>>>>>>> ae7e33b89ac0a04007ceeceae2da2177530b51c0
                        $detailUrl = 'detail_produk.php?id=' . (int) $produk['id'];
                        ?>
                        <article class="product-card">
                            <a href="<?= htmlspecialchars($detailUrl, ENT_QUOTES, 'UTF-8') ?>" class="product-thumb">
                                <?php if ($imgUrl): ?>
<<<<<<< HEAD
                                    <img
                                        src="<?= htmlspecialchars($imgUrl, ENT_QUOTES, 'UTF-8') ?>"
                                        alt="<?= htmlspecialchars($produk['nama'], ENT_QUOTES, 'UTF-8') ?>"
                                        loading="lazy"
                                    >
=======
                                    <img src="<?= htmlspecialchars($imgUrl, ENT_QUOTES, 'UTF-8') ?>" alt="">
>>>>>>> ae7e33b89ac0a04007ceeceae2da2177530b51c0
                                <?php else: ?>
                                    <span aria-hidden="true">📦</span>
                                <?php endif; ?>
                            </a>
                            <div class="product-body">
                                <span class="product-category">
                                    <?= htmlspecialchars($produk['kategori_nama'], ENT_QUOTES, 'UTF-8') ?>
                                </span>
                                <h3>
                                    <a href="<?= htmlspecialchars($detailUrl, ENT_QUOTES, 'UTF-8') ?>">
                                        <?= htmlspecialchars($produk['nama'], ENT_QUOTES, 'UTF-8') ?>
                                    </a>
                                </h3>
                                <div class="product-price"><?= formatRupiah($produk['harga']) ?></div>
                                <div class="product-stock">Stok: <?= (int) $produk['stok'] ?></div>
                                <div class="product-actions">
                                    <a href="<?= htmlspecialchars($detailUrl, ENT_QUOTES, 'UTF-8') ?>" class="btn btn-outline">Detail</a>
<<<<<<< HEAD
                                    <form action="proses/proses_cart.php" method="post">
                                        <input type="hidden" name="action" value="add">
                                        <input type="hidden" name="produk_id" value="<?= (int) $produk['id'] ?>">
                                        <input type="hidden" name="jumlah" value="1">
                                        <button type="submit" class="btn btn-primary">+ Keranjang</button>
                                    </form>
=======
                                    <a href="proses/proses_cart.php?aksi=tambah&amp;id=<?= (int) $produk['id'] ?>" class="btn btn-primary">+ Keranjang</a>
>>>>>>> ae7e33b89ac0a04007ceeceae2da2177530b51c0
                                </div>
                            </div>
                        </article>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </section>
</main>

<<<<<<< HEAD
<?php require __DIR__ . '/includes/footer.php'; ?>
=======
<?php require_once __DIR__ . '/includes/footer.php'; ?>
>>>>>>> ae7e33b89ac0a04007ceeceae2da2177530b51c0
