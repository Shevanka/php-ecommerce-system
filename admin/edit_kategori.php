<?php
require_once '../config/database.php';
require_once '../config/session.php';
requireAdmin();

$pageTitle = 'Edit Kategori';

$id = (int) ($_GET['id'] ?? 0);
if ($id <= 0) {
    setFlash('danger', 'ID kategori tidak valid.');
    header('Location: kategori.php');
    exit;
}

$stmt = mysqli_prepare($conn, "SELECT * FROM kategori WHERE id = ?");
mysqli_stmt_bind_param($stmt, 'i', $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$kategori = mysqli_fetch_assoc($result);

if (!$kategori) {
    setFlash('danger', 'Kategori tidak ditemukan.');
    header('Location: kategori.php');
    exit;
}

include '../includes/admin_header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="fw-bold mb-0">Edit Kategori</h5>
    <a href="kategori.php" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> Kembali
    </a>
</div>

<div class="row justify-content-center">
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-body p-4">
                <form action="../proses/proses_edit_kategori.php" method="POST">
                    <input type="hidden" name="id" value="<?= $kategori['id'] ?>">

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Kategori <span class="text-danger">*</span></label>
                        <input type="text" name="nama" class="form-control" required autofocus
                            value="<?= htmlspecialchars($kategori['nama']) ?>">
                        <div class="form-text">Nama kategori harus unik.</div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="kategori.php" class="btn btn-secondary">Batal</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-1"></i> Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/admin_footer.php'; ?>