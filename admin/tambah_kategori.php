<?php
require_once '../config/database.php';
require_once '../config/session.php';
requireAdmin();

$pageTitle = 'Tambah Kategori';

include '../includes/admin_header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="fw-bold mb-0">Tambah Kategori Baru</h5>
    <a href="kategori.php" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> Kembali
    </a>
</div>

<div class="row justify-content-center">
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-body p-4">
                <form action="../proses/proses_tambah_kategori.php" method="POST">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Kategori <span class="text-danger">*</span></label>
                        <input type="text" name="nama" class="form-control"
                            placeholder="Contoh: Elektronik, Fashion, Perabotan..." required autofocus
                            value="<?= htmlspecialchars($_GET['old'] ?? '') ?>">
                        <div class="form-text">Nama kategori harus unik.</div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="kategori.php" class="btn btn-secondary">Batal</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-1"></i> Simpan Kategori
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/admin_footer.php'; ?>