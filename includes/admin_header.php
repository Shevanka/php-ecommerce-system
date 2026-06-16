<?php
$pageTitle = $pageTitle ?? 'Admin';

$adminNav = [
    ['href' => 'dashboard.php',  'label' => 'Dashboard'],
    ['href' => 'produk.php',     'label' => 'Produk'],
    ['href' => 'kategori.php',   'label' => 'Kategori'],
    ['href' => 'pesanan.php',    'label' => 'Pesanan'],
    ['href' => '../index.php',   'label' => 'Lihat Toko'],
    ['href' => '../logout.php',  'label' => 'Logout'],
];

$currentPage = basename($_SERVER['PHP_SELF']);
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8') ?> | Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="/ta2/assets/css/admin-theme.css" rel="stylesheet">
</head>
<body class="bg-light">
<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
    <div class="container">
        <a class="navbar-brand" href="dashboard.php">Admin Toko</a>
        <div class="navbar-nav flex-row gap-3">
            <?php foreach ($adminNav as $item):
                $isActive = ($currentPage === basename($item['href']));
            ?>
                <a class="nav-link<?= $isActive ? ' active fw-semibold' : '' ?>"
                   href="<?= $item['href'] ?>">
                    <?= $item['label'] ?>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</nav>
<main class="container pb-5">