<?php
require_once '../config/database.php';
require_once '../config/session.php';
requireAdmin();

$pageTitle = 'Edit Produk';

// Ambil ID dari URL
$id = (int) ($_GET['id'] ?? 0);
if ($id <= 0) {
    setFlash('danger', 'ID produk tidak valid.');
    header('Location: produk.php');
    exit;
}

// Ambil data produk
$stmt = mysqli_prepare($conn, "SELECT * FROM produk WHERE id = ?");
mysqli_stmt_bind_param($stmt, 'i', $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$produk = mysqli_fetch_assoc($result);

if (!$produk) {
    setFlash('danger', 'Produk tidak ditemukan.');
    header('Location: produk.php');
    exit;
}

// Ambil semua kategori
$qKategori = mysqli_query($conn, "SELECT id, nama FROM kategori ORDER BY nama ASC");

include '../includes/admin_header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="fw-bold mb-0">Edit Produk</h5>
    <a href="produk.php" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> Kembali
    </a>
</div>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-body p-4">
                <form action="../proses/proses_edit_produk.php" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="id" value="<?= $produk['id'] ?>">

                    <div class="row g-3">
                        <!-- Nama Produk -->
                        <div class="col-12">
                            <label class="form-label fw-semibold">Nama Produk <span class="text-danger">*</span></label>
                            <input type="text" name="nama" class="form-control" required
                                value="<?= htmlspecialchars($produk['nama']) ?>">
                        </div>

                        <!-- Kategori -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Kategori <span class="text-danger">*</span></label>
                            <select name="kategori_id" class="form-select" required>
                                <option value="">-- Pilih Kategori --</option>
                                <?php while ($kat = mysqli_fetch_assoc($qKategori)): ?>
                                    <option value="<?= $kat['id'] ?>" <?= $kat['id'] == $produk['kategori_id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($kat['nama']) ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                        <!-- Harga -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Harga (Rp) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" name="harga" class="form-control" min="0" step="500" required
                                    value="<?= $produk['harga'] ?>">
                            </div>
                        </div>

                        <!-- Stok -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Stok <span class="text-danger">*</span></label>
                            <input type="number" name="stok" class="form-control" min="0" required
                                value="<?= $produk['stok'] ?>">
                        </div>

                        <!-- Gambar -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Ganti Gambar</label>
                            <input type="file" name="gambar" class="form-control" accept="image/*" id="inputGambar">
                            <div class="form-text">Kosongkan jika tidak ingin mengganti gambar.</div>
                        </div>

                        <!-- Gambar saat ini -->
                        <div class="col-12">
                            <label class="form-label fw-semibold">Gambar Saat Ini</label>
                            <div>
                                <?php if ($produk['gambar'] && file_exists('../assets/img/produk/' . $produk['gambar'])): ?>
                                    <img id="previewGambar"
                                        src="../assets/img/produk/<?= htmlspecialchars($produk['gambar']) ?>"
                                        class="rounded border" style="max-height:150px; object-fit:cover;"
                                        alt="Gambar produk">
                                <?php else: ?>
                                    <div class="rounded border bg-light d-flex align-items-center justify-content-center"
                                        style="width:120px;height:100px;" id="previewGambar">
                                        <i class="bi bi-image text-muted fs-3"></i>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Deskripsi -->
                        <div class="col-12">
                            <label class="form-label fw-semibold">Deskripsi</label>
                            <textarea name="deskripsi" class="form-control" rows="4"
                                placeholder="Tulis deskripsi singkat produk..."><?= htmlspecialchars($produk['deskripsi'] ?? '') ?></textarea>
                        </div>
                    </div>

                    <hr class="my-4">
                    <div class="d-flex justify-content-end gap-2">
                        <a href="produk.php" class="btn btn-secondary">Batal</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-1"></i> Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    // Preview gambar baru sebelum diupload
    document.getElementById('inputGambar').addEventListener('change', function () {
        const file = this.files[0];
        if (!file) return;

        const reader = new FileReader();
        reader.onload = (e) => {
            const el = document.getElementById('previewGambar');
            // Jika elemen adalah div placeholder, ganti dengan img
            if (el.tagName === 'DIV') {
                const img = document.createElement('img');
                img.id = 'previewGambar';
                img.className = 'rounded border';
                img.style = 'max-height:150px; object-fit:cover;';
                img.alt = 'Preview';
                img.src = e.target.result;
                el.replaceWith(img);
            } else {
                el.src = e.target.result;
            }
        };
        reader.readAsDataURL(file);
    });
</script>

<?php include '../includes/admin_footer.php'; ?>