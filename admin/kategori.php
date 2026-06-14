<?php
require_once '../config/database.php';
require_once '../config/session.php';
requireAdmin();

$pageTitle = 'Manajemen Kategori';

// Ambil semua kategori beserta jumlah produk
$qKategori = mysqli_query($conn, "
    SELECT k.id, k.nama, COUNT(p.id) AS jumlah_produk
    FROM kategori k
    LEFT JOIN produk p ON p.kategori_id = k.id
    GROUP BY k.id, k.nama
    ORDER BY k.nama ASC
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

<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="fw-bold mb-0">Kategori Produk</h5>
    <a href="tambah_kategori.php" class="btn btn-primary btn-sm">
        <i class="bi bi-plus-lg me-1"></i> Tambah Kategori
    </a>
</div>

<div class="row">
    <div class="col-lg-7">
        <div class="card table-card">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th style="width:50px;">#</th>
                            <th>Nama Kategori</th>
                            <th class="text-center">Jumlah Produk</th>
                            <th style="width:140px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $no = 1;
                        $empty = true;
                        while ($row = mysqli_fetch_assoc($qKategori)):
                            $empty = false;
                            ?>
                            <tr>
                                <td class="text-muted">
                                    <?= $no++ ?>
                                </td>
                                <td>
                                    <span class="fw-semibold">
                                        <?= htmlspecialchars($row['nama']) ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle">
                                        <?= $row['jumlah_produk'] ?> produk
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <a href="edit_kategori.php?id=<?= $row['id'] ?>"
                                            class="btn btn-sm btn-outline-primary" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <?php if ($row['jumlah_produk'] > 0): ?>
                                            <!-- Tidak bisa hapus jika masih ada produk -->
                                            <button class="btn btn-sm btn-outline-secondary"
                                                title="Tidak bisa dihapus (ada <?= $row['jumlah_produk'] ?> produk)" disabled>
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        <?php else: ?>
                                            <button type="button" class="btn btn-sm btn-outline-danger"
                                                onclick="konfirmasiHapus(<?= $row['id'] ?>, '<?= htmlspecialchars(addslashes($row['nama'])) ?>')"
                                                title="Hapus">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>

                        <?php if ($empty): ?>
                            <tr>
                                <td colspan="4" class="text-center py-5 text-muted">
                                    <i class="bi bi-tags fs-3 d-block mb-2"></i>
                                    Belum ada kategori. Tambah sekarang!
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
                Kategori yang masih memiliki produk tidak dapat dihapus.
            </small>
        </div>
    </div>
</div>

<!-- Modal Konfirmasi Hapus -->
<div class="modal fade" id="modalHapus" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <h6 class="modal-title fw-bold text-danger">
                    <i class="bi bi-exclamation-triangle me-1"></i> Hapus Kategori?
                </h6>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body pt-2">
                <p class="mb-0 text-muted" style="font-size:.9rem;">
                    Kategori <strong id="namaKategoriHapus"></strong> akan dihapus permanen.
                </p>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Batal</button>
                <a href="#" id="linkHapusKategori" class="btn btn-sm btn-danger">Ya, Hapus</a>
            </div>
        </div>
    </div>
</div>

<script>
    function konfirmasiHapus(id, nama) {
        document.getElementById('namaKategoriHapus').textContent = nama;
        document.getElementById('linkHapusKategori').href = 'hapus_kategori.php?id=' + id;
        new bootstrap.Modal(document.getElementById('modalHapus')).show();
    }
</script>

<?php include '../includes/admin_footer.php'; ?>
