<?php
require_once '../config/database.php';
require_once '../config/session.php';
requireAdmin();

$pageTitle = 'Dashboard';

// ── Statistik ringkas ──
$totalProduk = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM produk"))[0];
$totalKategori = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM kategori"))[0];
$totalPesanan = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM pesanan"))[0];
$totalUser = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM users WHERE role = 'user'"))[0];

// Pendapatan dari pesanan yang selesai
$pendapatan = mysqli_fetch_row(mysqli_query($conn, "SELECT COALESCE(SUM(total),0) FROM pesanan WHERE status = 'selesai'"))[0];

// Pesanan pending (butuh tindakan)
$pesananPending = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM pesanan WHERE status = 'pending'"))[0];

// Stok menipis (stok <= 5)
$stokMenipis = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM produk WHERE stok <= 5"))[0];

// Pesanan terbaru (10 data)
$qPesananTerbaru = mysqli_query($conn, "
    SELECT p.id, u.nama AS nama_user, p.total, p.status, p.created_at
    FROM pesanan p
    JOIN users u ON u.id = p.user_id
    ORDER BY p.created_at DESC
    LIMIT 10
");

// Produk stok menipis
$qStokMenipis = mysqli_query($conn, "
    SELECT nama, stok FROM produk WHERE stok <= 5 ORDER BY stok ASC LIMIT 5
");

$flash = getFlash();

include '../includes/admin_header.php';
?>

<!-- Flash message -->
<?php if ($flash): ?>
    <div class="alert alert-<?= $flash['type'] ?> alert-dismissible alert-autohide fade show mb-3" role="alert">
        <?= htmlspecialchars($flash['message']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<!-- ── Stat Cards ── -->
<div class="row g-3 mb-4">
    <!-- Total Produk -->
    <div class="col-6 col-lg-3">
        <div class="card stat-card p-3">
            <div class="d-flex align-items-center gap-3">
                <div class="icon-box bg-primary bg-opacity-10 text-primary">
                    <i class="bi bi-box-seam"></i>
                </div>
                <div>
                    <div class="fw-bold fs-4 lh-1">
                        <?= $totalProduk ?>
                    </div>
                    <div class="text-muted" style="font-size:.8rem;">Total Produk</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Kategori -->
    <div class="col-6 col-lg-3">
        <div class="card stat-card p-3">
            <div class="d-flex align-items-center gap-3">
                <div class="icon-box bg-success bg-opacity-10 text-success">
                    <i class="bi bi-tags"></i>
                </div>
                <div>
                    <div class="fw-bold fs-4 lh-1">
                        <?= $totalKategori ?>
                    </div>
                    <div class="text-muted" style="font-size:.8rem;">Kategori</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Pesanan -->
    <div class="col-6 col-lg-3">
        <div class="card stat-card p-3">
            <div class="d-flex align-items-center gap-3">
                <div class="icon-box bg-warning bg-opacity-10 text-warning">
                    <i class="bi bi-receipt"></i>
                </div>
                <div>
                    <div class="fw-bold fs-4 lh-1">
                        <?= $totalPesanan ?>
                    </div>
                    <div class="text-muted" style="font-size:.8rem;">Total Pesanan</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Total User -->
    <div class="col-6 col-lg-3">
        <div class="card stat-card p-3">
            <div class="d-flex align-items-center gap-3">
                <div class="icon-box bg-info bg-opacity-10 text-info">
                    <i class="bi bi-people"></i>
                </div>
                <div>
                    <div class="fw-bold fs-4 lh-1">
                        <?= $totalUser ?>
                    </div>
                    <div class="text-muted" style="font-size:.8rem;">Pelanggan</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ── Pendapatan + Alert ── -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card stat-card p-3 border-start border-success border-4">
            <div class="text-muted mb-1" style="font-size:.8rem;">Total Pendapatan (Selesai)</div>
            <div class="fw-bold fs-5 text-success">Rp
                <?= number_format($pendapatan, 0, ',', '.') ?>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <a href="pesanan.php?status=pending" class="text-decoration-none">
            <div class="card stat-card p-3 border-start border-warning border-4">
                <div class="text-muted mb-1" style="font-size:.8rem;">Pesanan Pending</div>
                <div class="fw-bold fs-5 text-warning">
                    <?= $pesananPending ?> pesanan
                </div>
            </div>
        </a>
    </div>
    <div class="col-md-4">
        <a href="produk.php?filter=menipis" class="text-decoration-none">
            <div class="card stat-card p-3 border-start border-danger border-4">
                <div class="text-muted mb-1" style="font-size:.8rem;">Stok Menipis (≤5)</div>
                <div class="fw-bold fs-5 text-danger">
                    <?= $stokMenipis ?> produk
                </div>
            </div>
        </a>
    </div>
</div>

<!-- ── Tabel Bawah ── -->
<div class="row g-3">
    <!-- Pesanan Terbaru -->
    <div class="col-lg-8">
        <div class="card table-card">
            <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                <span class="fw-semibold">Pesanan Terbaru</span>
                <a href="pesanan.php" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>#ID</th>
                            <th>Pelanggan</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Tanggal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = mysqli_fetch_assoc($qPesananTerbaru)): ?>
                            <tr>
                                <td><span class="text-muted">#
                                        <?= $row['id'] ?>
                                    </span></td>
                                <td>
                                    <?= htmlspecialchars($row['nama_user']) ?>
                                </td>
                                <td>Rp
                                    <?= number_format($row['total'], 0, ',', '.') ?>
                                </td>
                                <td>
                                    <?php
                                    $badgeMap = [
                                        'pending' => 'warning',
                                        'diproses' => 'info',
                                        'dikirim' => 'primary',
                                        'selesai' => 'success',
                                        'batal' => 'danger',
                                    ];
                                    $badge = $badgeMap[$row['status']] ?? 'secondary';
                                    ?>
                                    <span
                                        class="badge bg-<?= $badge ?>-subtle text-<?= $badge ?> border border-<?= $badge ?>-subtle">
                                        <?= ucfirst($row['status']) ?>
                                    </span>
                                </td>
                                <td class="text-muted" style="font-size:.85rem;">
                                    <?= date('d M Y', strtotime($row['created_at'])) ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Stok Menipis -->
    <div class="col-lg-4">
        <div class="card table-card">
            <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                <span class="fw-semibold">⚠️ Stok Menipis</span>
                <a href="produk.php" class="btn btn-sm btn-outline-danger">Kelola</a>
            </div>
            <ul class="list-group list-group-flush">
                <?php
                $hasStokMenipis = false;
                while ($row = mysqli_fetch_assoc($qStokMenipis)):
                    $hasStokMenipis = true;
                    ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span style="font-size:.88rem;">
                            <?= htmlspecialchars($row['nama']) ?>
                        </span>
                        <span class="badge bg-danger rounded-pill">
                            <?= $row['stok'] ?>
                        </span>
                    </li>
                <?php endwhile; ?>
                <?php if (!$hasStokMenipis): ?>
                    <li class="list-group-item text-muted text-center py-4" style="font-size:.88rem;">
                        <i class="bi bi-check-circle text-success me-1"></i> Semua stok aman
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</div>

<?php include '../includes/admin_footer.php'; ?>