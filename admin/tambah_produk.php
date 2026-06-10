<?php
require_once '../config/database.php';
require_once '../config/session.php';
requireAdmin();

$pageTitle = 'Tambah Produk';

// Ambil semua kategori untuk dropdown
$qKategori = mysqli_query($conn, "SELECT id, nama FROM kategori ORDER BY nama ASC");

include '../includes/admin_header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="fw-bold mb-0">Tambah Produk Baru</h5>
    <a href="produk.php" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> Kembali
    </a>
</div>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-body p-4">
                <form action="../proses/proses_tambah_produk.php" method="POST" enctype="multipart/form-data">

                    <div class="row g-3">
                        <!-- Nama Produk -->
                        <div class="col-12">
                            <label class="form-label fw-semibold">Nama Produk <span class="text-danger">*</span></label>
                            <input type="text" name="nama" class="form-control"
                                placeholder="Contoh: Earphone Bluetooth Pro" required
                                value="<?= htmlspecialchars($_GET['old_nama'] ?? '') ?>">
                        </div>

                        <!-- Kategori -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Kategori <span class="text-danger">*</span></label>
                            <select name="kategori_id" class="form-select" required>
                                <option value="">-- Pilih Kategori --</option>
                                <?php while ($kat = mysqli_fetch_assoc($qKategori)): ?>
                                    <option value="<?= $kat['id'] ?>">
                                    
                                        <?= htmlspecialchars($kat['nama']) ?>
                                        </option>
                                <?php endwhile; ?>
                            </select>
                            <div class="form-text">
                                Belum ada kategori yang sesuai?
                                <a href="tambah_kategori.php" target="_blank">Tambah kategori baru</a>
                            </div>
                        </div>

                        <!-- Harga -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Harga (Rp) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" name="harga" class="form-control" placeholder="0" min="0"
                                    step="500" required>
                            </div>
                        </div>

                        <!-- Stok -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Stok <span class="text-danger">*</span></label>
                            <input type="number" name="stok" class="form-control" placeholder="0" min="0" required
                                value="0">
                        </div>

                        <!-- Gambar -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Gambar Produk</label>
                            <input type="file" name="gambar" class="form-control" accept="image/*" id="inputGambar">
                            <div class="form-text">Format: JPG, PNG, WEBP. Maks: 2MB</div>
                        </div>

                        <!-- Preview Gambar -->
                        <div class="col-12 d-none" id="previewWrapper">
                            <label class="form-label fw-semibold">Preview</label>
                            <img id="previewGambar" src="#" class="d-block rounded border"
                                style="max-height:150px; object-fit:cover;" alt="Preview">
                        </div>

                        <!-- Deskripsi -->
                        <div class="col-12">
                            <label class="form-label fw-semibold">Deskripsi</label>
                            <textarea name="deskripsi" class="form-control" rows="4"
                                placeholder="Tulis deskripsi singkat produk..."></textarea>
                        </div>
                    </div>

                    <hr class="my-4">
                    <div class="d-flex justify-content-end gap-2">
                        <a href="produk.php" class="btn btn-secondary">Batal</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-1"></i> Simpan Produk
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    // Preview gambar sebelum upload
    document.getElementById('inputGambar').addEventListener('change', function () {
        const file = this.files[0];
        if (!file) return;

        const reader = new FileReader();
        reader.onload = (e) => {
            document.getElementById('previewGambar').src = e.target.result;
            document.getElementById('previewWrapper').classList.remove('d-none');
        };
        reader.readAsDataURL(file);
    });
</script>

<?php include '../includes/admin_footer.php'; ?>