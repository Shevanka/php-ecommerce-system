<?php
/**
 * Error Page
 * Menampilkan halaman error dengan pesan dari session
 */

require_once 'config/session.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error - Sistem Penjualan Online</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .error-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .error-card {
            background: white;
            padding: 3rem;
            border-radius: 10px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.3);
            text-align: center;
            max-width: 500px;
        }
        .error-code {
            font-size: 5rem;
            color: #dc3545;
            margin-bottom: 1rem;
        }
        .error-title {
            font-size: 2rem;
            color: #333;
            margin-bottom: 1rem;
        }
        .error-message {
            color: #666;
            margin-bottom: 2rem;
            line-height: 1.6;
        }
        .error-buttons {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
        }
        .error-buttons a {
            padding: 0.75rem 1.5rem;
            border-radius: 5px;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        .btn-home {
            background: #667eea;
            color: white;
        }
        .btn-home:hover {
            background: #5568d3;
            color: white;
        }
        .btn-back {
            background: #f8f9fa;
            color: #333;
            border: 1px solid #ddd;
        }
        .btn-back:hover {
            background: #e9ecef;
            color: #333;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-card">
            <div class="error-code">
                <?php
                    $error_code = isset($_GET['code']) ? htmlspecialchars($_GET['code']) : '500';
                    echo $error_code;
                ?>
            </div>
            
            <h1 class="error-title">
                <?php
                    switch ($error_code) {
                        case '403':
                            echo 'Akses Ditolak';
                            break;
                        case '404':
                            echo 'Halaman Tidak Ditemukan';
                            break;
                        case '500':
                            echo 'Error Server';
                            break;
                        default:
                            echo 'Terjadi Error';
                    }
                ?>
            </h1>
            
            <p class="error-message">
                <?php
                    $message = isset($_GET['message']) ? htmlspecialchars($_GET['message']) : 'Terjadi kesalahan pada sistem.';
                    echo $message;
                ?>
            </p>

            <?php
                // Tampilkan flash message jika ada
                if ($flash = getFlash()) {
                    $alertType = $flash['type'] === 'success' ? 'alert-success' : 
                                 ($flash['type'] === 'error' || $flash['type'] === 'danger' ? 'alert-danger' : 
                                  ($flash['type'] === 'warning' ? 'alert-warning' : 'alert-info'));
                    ?>
                    <div class="alert <?php echo $alertType; ?> mb-3">
                        <?php echo htmlspecialchars($flash['message']); ?>
                    </div>
                    <?php
                }
            ?>
            
            <div class="error-buttons">
                <a href="index.php" class="btn-home">Kembali ke Beranda</a>
                <a href="javascript:history.back()" class="btn-back">Kembali</a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
