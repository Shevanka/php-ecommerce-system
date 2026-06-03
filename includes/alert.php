<?php
/**
 * Alert/Flash Message Display
 * Menampilkan pesan flash dari session
 */

if ($flash = getFlash()) {
    $alertType = $flash['type'] === 'success' ? 'alert-success' : 
                 ($flash['type'] === 'error' || $flash['type'] === 'danger' ? 'alert-danger' : 
                  ($flash['type'] === 'warning' ? 'alert-warning' : 'alert-info'));
    ?>
    <div class="alert <?php echo $alertType; ?> alert-dismissible fade show" role="alert">
        <strong><?php 
            if ($flash['type'] === 'success') echo 'Berhasil!';
            elseif ($flash['type'] === 'error' || $flash['type'] === 'danger') echo 'Error!';
            elseif ($flash['type'] === 'warning') echo 'Perhatian!';
            else echo 'Informasi!';
        ?></strong> 
        <?php echo htmlspecialchars($flash['message']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php
}
