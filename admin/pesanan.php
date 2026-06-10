<?php
require_once '../config/database.php';
require_once '../config/session.php';
requireAdmin();

$pageTitle = 'Manajemen Pesanan';

// Filter status dari URL
$statusFilter = $_GET['status'] ?? '';
$validStatus = ['pending', 'diproses', 'dikirim', 'selesai', 'batal'];

$whereClause = '';
if ($statusFilter && in_array($statusFilter, $validStatus)) {
    $statusEsc = mysqli_real_escape_string($conn, $statusFilter);
    $whereClause = "WHERE p.status = '$statusEsc'";
}

// Pencarian nama user
$search = trim($_GET['q'] ?? '');
if ($search !== '') {
    $searchEsc = mysqli_real_escape_string($conn, $search);
    $whereClause = $whereClause
        ? "$whereClause AND u.nama LIKE '%$searchEsc%'"
        : "WHERE u.nama LIKE '%$searchEsc%'";
}

$qPesanan = mysqli_query($conn, "
    SELECT p.id, p.total, p.status, p.created_at,
           u.nama AS nama_user, u.email,
           COUNT(dp.id) AS jumlah_item
    FROM pesanan p
    JOIN users u ON u.id = p.user_id
    LEFT JOIN detail_pesanan dp ON dp.pesanan_id = p.id
    $whereClause
    GROUP BY p.id
    ORDER BY p.created_at DESC
");

// Hitung per status untuk tab counter
$statusCounts = [];
$countResult = mysqli_query($conn, "SELECT status, COUNT(*) AS total FROM pesanan GROUP BY status");
while ($row = mysqli_fetch_assoc($countResult)) {
    $statusCounts[$row['status']] = $row['total'];
}
$totalAll = array_sum($statusCounts);

$flash = getFlash();
include '../includes/admin_header.php';
?>

<!-- Flash message -->
<?php if ($flash): ?>
    <div class="alert alert-<?= $flash['type'] ?> alert-autohide alert-dismissible fade show mb-3" role="alert">
        <?= htmlspecialchars($flash['message']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="fw-bold mb-0">Pesanan</h5>
</div>

<!-- Filter Tab Status -->
<div class="mb-3">
    <div class="d-flex flex-wrap gap-2">
        <?php
        $tabs = [
            '' => ['label' => 'Semua', 'color' => 'secondary'],
            'pending' => ['label' => 'Pending', 'color' => 'warning'],
            'diproses' => ['label' => 'Diproses', 'color' => 'info'],
            'dikirim' => ['label' => 'Dikirim', 'color' => 'primary'],
            'selesai' => ['label' => 'Selesai', 'color' => 'success'],
            'batal' => ['label' => 'Batal', 'color' => 'danger'],
        ];
        foreach ($tabs as $val => $tab):
            $isActive = $statusFilter === $val;
            $count = $val === '' ? $totalAll : ($statusCounts[$val] ?? 0);
            ?>
            <a href="pesanan.php?status=<?= $val ?><?= $search ? '&q=' . urlencode($search) : '' ?>"
                class="btn btn-sm <?= $isActive ? "btn-{$tab['color']}" : "btn-outline-{$tab['color']}" ?>">
                <?= $tab['label'] ?>
                <span class="badge <?= $isActive ? 'bg-white text-dark' : "bg-{$tab['color']}-subtle text-{$tab['color']}" ?> ms-1">
                    <?= $count ?>
                </span>
            </a>
        <?php endforeach; ?>
    </div>
</div>

<!-- Search -->
<form method="GET" class="mb-3 d-flex gap-2" style="max-width:380px;">
    <?php if ($statusFilter): ?>
        <input type="hidden" name="status" value="<?= htmlspecialchars($statusFilter) ?>">
    <?php endif; ?>
    <input type="text" name="q" class="form-control form-control-sm" placeholder="Cari nama pelanggan..."
        value="<?= htmlspecialchars($search) ?>">
    <button class="btn btn-sm btn-outline-secondary" type="submit"><i class="bi bi-search"></i></button>
    <?php if ($search): ?>
        <a href="pesanan.php<?= $statusFilter ? '?status=' . $statusFilter : '' ?>" class="btn btn-sm btn-outline-danger"><i
                class="bi bi-x"></i></a>
    <?php endif; ?>
</form>

<!-- Tabel -->
<div class="card table-card">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th>#ID</th>
                    <th>Pelanggan</th>
                    <th class="text-center">Item</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Tanggal</th>
                    <th style="width:180px;">Ubah Status</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $empty = true;
                while ($row = mysqli_fetch_assoc($qPesanan)):
                    $empty = false;
                    $badgeMap = [
                        'pending' => 'warning',
                        'diproses' => 'info',
                        'dikirim' => 'primary',
                        'selesai' => 'success',
                        'batal' => 'danger',
                    ];
                    $badge = $badgeMap[$row['status']] ?? 'secondary';
                    ?>
                    <tr>
                        <td>
                            <span class="fw-semibold text-primary">#
                                <?= $row['id'] ?>
                            </span>
                        </td>
                        <td>
                            <div class="fw-semibold" style="font-size:.9rem;">
                                <?= htmlspecialchars($row['nama_user']) ?>
                            </div>
                            <div class="text-muted" style="font-size:.78rem;">
                                <?= htmlspecialchars($row['email']) ?>
                            </div>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle">
                                <?= $row['jumlah_item'] ?> item
                            </span>
                        </td>
                        <td class="fw-semibold">Rp
                            <?= number_format($row['total'], 0, ',', '.') ?>
                        </td>
                        <td>
                            <span
                                class="badge bg-<?= $badge ?>-subtle text-<?= $badge ?> border border-<?= $badge ?>-subtle">
                                <?= ucfirst($row['status']) ?>
                            </span>
                        </td>
                        <td class="text-muted" style="font-size:.82rem;">
                            <?= date('d M Y, H:i', strtotime($row['created_at'])) ?>
                        </td>
                        <td>
                            <?php if ($row['status'] !== 'selesai' && $row['status'] !== 'batal'): ?>
                                <form action="../proses/proses_update_status_pesanan.php" method="POST" class="d-flex gap-1">
                                    <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                    <select name="status" class="form-select form-select-sm" style="font-size:.8rem;">
                                        <?php foreach (['pending', 'diproses', 'dikirim', 'selesai', 'batal'] as $s): ?>
                                            <option value="<?= $s ?>" <?= $s === $row['status'] ? 'selected' : '' ?>>
                                                <?= ucfirst($s) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <button type="submit" class="btn btn-sm btn-primary" title="Simpan">
                                        <i class="bi bi-check-lg"></i>
                                    </button>
                                </form>
                            <?php else: ?>
                                <span class="text-muted" style="font-size:.82rem;">—</span>
                            <?php endif; ?>
                        </td>
                    </tr>

                    <!-- Detail Item (expandable row) -->
                    <tr class="table-light" id="detail-<?= $row['id'] ?>">
                        <td colspan="7" class="p-0">
                            <div class="px-3 py-2">
                                <?php
                                $qDetail = mysqli_prepare($conn, "
                            SELECT pr.nama, dp.qty, dp.harga
                            FROM detail_pesanan dp
                            JOIN produk pr ON pr.id = dp.produk_id
                            WHERE dp.pesanan_id = ?
                        ");
                                mysqli_stmt_bind_param($qDetail, 'i', $row['id']);
                                mysqli_stmt_execute($qDetail);
                                $rDetail = mysqli_stmt_get_result($qDetail);
                                ?>
                                <div class="d-flex flex-wrap gap-2">
                                    <?php while ($d = mysqli_fetch_assoc($rDetail)): ?>
                                        <span class="badge bg-light text-dark border" style="font-weight:400;font-size:.8rem;">
                                            <?= htmlspecialchars($d['nama']) ?>
                                            &times;
                                            <?= $d['qty'] ?>
                                            <span class="text-muted">(Rp
                                                <?= number_format($d['harga'], 0, ',', '.') ?>)
                                            </span>
                                        </span>
                                    <?php endwhile; ?>
                                </div>
                            </div>
                        </td>
                    </tr>

                <?php endwhile; ?>

                <?php if ($empty): ?>
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="bi bi-receipt fs-3 d-block mb-2"></i>
                            <?= $statusFilter ? "Tidak ada pesanan dengan status \"$statusFilter\"." : 'Belum ada pesanan.' ?>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="mt-2">
    <small class="text-muted">
        <i class="bi bi-info-circle me-1"></i>
        Detail item pesanan tampil di baris abu-abu di bawah setiap pesanan.
        Pesanan <strong>Selesai</strong> dan <strong>Batal</strong> tidak dapat diubah statusnya.
    </small>
</div>

<?php include '../includes/admin_footer.php'; ?>