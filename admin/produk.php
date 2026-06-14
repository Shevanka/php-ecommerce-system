<?php
require_once '../config/database.php';
require_once '../config/session.php';
requireAdmin();

$pageTitle = 'Manajemen Produk';

// Filter stok menipis dari dashboard
$filterMenipis = isset($_GET['filter']) && $_GET['filter'] === 'menipis';

// Pencarian
$search = trim($_GET['q'] ?? '');
$searchSql = '';
$params = [];

$whereClause = $filterMenipis ? 'WHERE p.stok <= 5' : '';

if ($search !== '') {
    $searchEsc = mysqli_real_escape_string($conn, $search);
    $whereClause = $filterMenipis
        ? "WHERE p.stok <= 5 AND (p.nama LIKE '%$searchEsc%' OR k.nama LIKE '%$searchEsc%')"
        : "WHERE p.nama LIKE '%$searchEsc%' OR k.nama LIKE '%$searchEsc%'";
}

$query = mysqli_query($conn, "
    SELECT p.id, p.nama, p.harga, p.stok, p.gambar, p.created_at, k.nama AS kategori
    FROM produk p
    JOIN kategori k ON k.id = p.kategori_id
    $whereClause
    ORDER BY p.created_at DESC
");

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

<!-- Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h5 class="mb-0 fw-bold">Produk</h5>
        <?php if ($filterMenipis): ?>
            <span class="badge bg-danger-subtle text-danger border border-danger-subtle mt-1">Filter: Stok Menipis</span>
        <?php endif; ?>
    </div>
    <a href="tambah_produk.php" class="btn btn-primary btn-sm">
        <i class="bi bi-plus-lg me-1"></i> Tambah Produk
    </a>
</div>

<!-- Search bar -->
<form method="GET" class="mb-3 d-flex gap-2" style="max-width:400px;">
    <?php if ($filterMenipis): ?>
        <input type="hidden" name="filter" value="menipis">
    <?php endif; ?>
    <input type="text" name="q" class="form-control form-control-sm" placeholder="Cari nama produk / kategori..."
        value="<?= htmlspecialchars($search) ?>">
    <button class="btn btn-sm btn-outline-secondary" type="submit"><i class="bi bi-search"></i></button>
    <?php if ($search): ?>
        <a href="produk.php<?= $filterMenipis ? '?filter=menipis' : '' ?>" class="btn btn-sm btn-outline-danger">
            <i class="bi bi-x"></i>
        </a>
    <?php endif; ?>
</form>

<!-- Tabel -->
<div class="card table-card">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th style="width:50px;">#</th>
                    <th style="width:60px;">Gambar</th>
                    <th>Nama Produk</th>
                    <th>Kategori</th>
                    <th>Harga</th>
                    <th>Stok</th>
                    <th>Dibuat</th>
                    <th style="width:140px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $no = 1;
                $empty = true;
                while ($row = mysqli_fetch_assoc($query)):
                    $empty = false;
                    ?>
                    <tr>
                        <td class="text-muted">
                            <?= $no++ ?>
                        </td>
                        <td>
                            <?php if ($row['gambar'] && file_exists('../assets/img/produk/' . $row['gambar'])): ?>
                                <img src="../assets/img/produk/<?= htmlspecialchars($row['gambar']) ?>" class="rounded"
                                    style="width:40px;height:40px;object-fit:cover;" alt="">
                            <?php else: ?>
                                <div class="rounded bg-light d-flex align-items-center justify-content-center"
                                    style="width:40px;height:40px;font-size:1.2rem;">
                                    <i class="bi bi-image text-muted"></i>
                                </div>
                            <?php endif; ?>
                        </td>
                        <td class="fw-semibold">
                            <?= htmlspecialchars($row['nama']) ?>
                        </td>
                        <td>
                            <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle">
                                <?= htmlspecialchars($row['kategori']) ?>
                            </span>
                        </td>
                        <td>Rp
                            <?= number_format($row['harga'], 0, ',', '.') ?>
                        </td>
                        <td>
                            <?php if ($row['stok'] <= 5): ?>
                                <span class="badge bg-danger">
                                    <?= $row['stok'] ?>
                                </span>
                            <?php elseif ($row['stok'] <= 20): ?>
                                <span class="badge bg-warning text-dark">
                                    <?= $row['stok'] ?>
                                </span>
                            <?php else: ?>
                                <span class="badge bg-success">
                                    <?= $row['stok'] ?>
                                </span>
                            <?php endif; ?>
                        </td>
                        <td class="text-muted" style="font-size:.82rem;">
                            <?= date('d M Y', strtotime($row['created_at'])) ?>
                        </td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="edit_produk.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-outline-primary"
                                    title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <button type="button" class="btn btn-sm btn-outline-danger" title="Hapus"
                                    onclick="konfirmasiHapus(<?= $row['id'] ?>, '<?= htmlspecialchars(addslashes($row['nama'])) ?>')">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                <?php endwhile; ?>

                <?php if ($empty): ?>
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            <i class="bi bi-inbox fs-3 d-block mb-2"></i>
                            <?= $search ? "Tidak ada produk yang cocok dengan \"$search\"" : 'Belum ada produk. Tambah sekarang!' ?>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Konfirmasi Hapus -->
<div class="modal fade" id="modalHapus" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <h6 class="modal-title fw-bold text-danger">
                    <i class="bi bi-exclamation-triangle me-1"></i> Hapus Produk?
                </h6>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body pt-2">
                <p class="mb-0 text-muted" style="font-size:.9rem;">
                    Produk <strong id="namaProdukHapus"></strong> akan dihapus permanen.
                </p>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Batal</button>
                <a href="#" id="linkHapusProduk" class="btn btn-sm btn-danger">Ya, Hapus</a>
            </div>
        </div>
    </div>
</div>

<script>
    function konfirmasiHapus(id, nama) {
        document.getElementById('namaProdukHapus').textContent = nama;
        document.getElementById('linkHapusProduk').href = 'hapus_produk.php?id=' + id;
        new bootstrap.Modal(document.getElementById('modalHapus')).show();
    }
</script>

<?php include '../includes/admin_footer.php'; ?>
