-- Database: penjualan_online
-- Import via phpMyAdmin or: mysql -u root < database/penjualan_online.sql

CREATE DATABASE IF NOT EXISTS penjualan_online
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE penjualan_online;

CREATE TABLE users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    email VARCHAR(120) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('user', 'admin') NOT NULL DEFAULT 'user',
    remember_token VARCHAR(64) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE kategori (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL UNIQUE
) ENGINE=InnoDB;

CREATE TABLE produk (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    kategori_id INT UNSIGNED NOT NULL,
    nama VARCHAR(150) NOT NULL,
    deskripsi TEXT NULL,
    harga DECIMAL(12, 2) NOT NULL,
    stok INT UNSIGNED NOT NULL DEFAULT 0,
    gambar VARCHAR(255) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_produk_kategori FOREIGN KEY (kategori_id) REFERENCES kategori (id) ON DELETE RESTRICT
) ENGINE=InnoDB;

CREATE TABLE pesanan (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    total DECIMAL(12, 2) NOT NULL,
    status ENUM('pending', 'diproses', 'dikirim', 'selesai', 'batal') NOT NULL DEFAULT 'pending',
<<<<<<< HEAD
    nama_penerima VARCHAR(100) NOT NULL,
    alamat TEXT NOT NULL,
    telepon VARCHAR(20) NOT NULL,
    metode_pembayaran ENUM('transfer', 'cod', 'kartu_kredit') NOT NULL,
    catatan TEXT NULL,
=======
>>>>>>> ae7e33b89ac0a04007ceeceae2da2177530b51c0
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_pesanan_user FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE detail_pesanan (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    pesanan_id INT UNSIGNED NOT NULL,
    produk_id INT UNSIGNED NOT NULL,
    qty INT UNSIGNED NOT NULL,
    harga DECIMAL(12, 2) NOT NULL,
    CONSTRAINT fk_detail_pesanan FOREIGN KEY (pesanan_id) REFERENCES pesanan (id) ON DELETE CASCADE,
    CONSTRAINT fk_detail_produk FOREIGN KEY (produk_id) REFERENCES produk (id) ON DELETE RESTRICT
) ENGINE=InnoDB;

INSERT INTO kategori (nama) VALUES
    ('Elektronik'),
    ('Fashion'),
    ('Perabotan');

INSERT INTO produk (kategori_id, nama, deskripsi, harga, stok, gambar) VALUES
    (1, 'Earphone Bluetooth', 'Earphone nirkabel dengan noise cancelling dan baterai 24 jam.', 299000, 50, NULL),
    (1, 'Smart Watch', 'Jam tangan pintar dengan monitor detak jantung dan GPS.', 899000, 30, NULL),
    (2, 'Kaos Premium Cotton', 'Kaos katun combed 30s, nyaman untuk sehari-hari.', 89000, 100, NULL),
    (2, 'Jaket Hoodie', 'Hoodie fleece hangat, unisex, tersedia berbagai ukuran.', 249000, 45, NULL),
    (3, 'Lampu Meja LED', 'Lampu meja adjustable brightness, hemat energi.', 159000, 25, NULL),
    (3, 'Rak Buku Minimalis', 'Rak buku 3 tingkat, material kayu engineered.', 349000, 15, NULL);

-- Password for both: password
INSERT INTO users (nama, email, password, role) VALUES
    ('Admin Toko', 'admin@toko.local', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin'),
    ('Pelanggan Demo', 'user@toko.local', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user');
