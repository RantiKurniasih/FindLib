-- Database FINDLIB
-- Sistem Informasi Ketersediaan Buku
-- Ruang Baca FMIPA Universitas Halu Oleo

CREATE DATABASE IF NOT EXISTS findlib
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE findlib;



-- =========================================
-- TABEL PENGGUNA
-- Menyimpan akun admin FINDLIB
-- =========================================

CREATE TABLE IF NOT EXISTS pengguna (
    id_pengguna INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    username VARCHAR(50) NOT NULL,
    email VARCHAR(150) NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    status_aktif TINYINT(1) NOT NULL DEFAULT 1,

    CONSTRAINT uq_pengguna_username UNIQUE (username),
    CONSTRAINT uq_pengguna_email UNIQUE (email),
    CONSTRAINT chk_pengguna_status
        CHECK (status_aktif IN (0, 1))
) ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_unicode_ci;

-- =========================================
-- TABEL KATEGORI
-- Menyimpan kategori buku FINDLIB
-- =========================================

CREATE TABLE IF NOT EXISTS kategori (
    id_kategori INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nama_kategori VARCHAR(100) NOT NULL,

    CONSTRAINT uq_kategori_nama
        UNIQUE (nama_kategori)
) ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_unicode_ci;

-- =========================================
-- TABEL LOKASI RAK
-- Menyimpan informasi lokasi rak buku
-- =========================================

CREATE TABLE IF NOT EXISTS lokasi_rak (
    id_rak INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    kode_rak VARCHAR(20) NOT NULL,
    nama_rak VARCHAR(100) NOT NULL,

    CONSTRAINT uq_lokasi_kode
        UNIQUE (kode_rak)
) ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_unicode_ci;


-- =========================================
-- TABEL LOKASI RAK
-- Menyimpan informasi lokasi rak buku
-- =========================================

CREATE TABLE IF NOT EXISTS lokasi_rak (
    id_rak INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    kode_rak VARCHAR(20) NOT NULL,
    nama_rak VARCHAR(100) NOT NULL,

    CONSTRAINT uq_lokasi_kode
        UNIQUE (kode_rak)
) ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_unicode_ci;


-- =========================================
-- TABEL BUKU
-- Menyimpan katalog dan stok buku FINDLIB
-- =========================================

CREATE TABLE IF NOT EXISTS buku (
    id_buku INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    no_katalog VARCHAR(50) NOT NULL,
    judul VARCHAR(255) NOT NULL,
    penulis VARCHAR(150) NOT NULL,
    penerbit VARCHAR(150) NOT NULL,
    tahun_terbit SMALLINT UNSIGNED NOT NULL,
    id_kategori INT UNSIGNED NOT NULL,
    id_rak INT UNSIGNED NOT NULL,
    jumlah_stok INT UNSIGNED NOT NULL DEFAULT 0,

    updated_at TIMESTAMP NOT NULL
        DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    stock_updated_at TIMESTAMP NULL DEFAULT NULL,

    CONSTRAINT uq_buku_katalog
        UNIQUE (no_katalog),

    CONSTRAINT fk_buku_kategori
        FOREIGN KEY (id_kategori)
        REFERENCES kategori(id_kategori)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,

    CONSTRAINT fk_buku_rak
        FOREIGN KEY (id_rak)
        REFERENCES lokasi_rak(id_rak)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,

    CONSTRAINT chk_buku_stok
        CHECK (jumlah_stok >= 0),

    CONSTRAINT chk_buku_tahun
        CHECK (tahun_terbit BETWEEN 1000 AND 9999),

    INDEX idx_buku_judul (judul),
    INDEX idx_buku_penulis (penulis),
    INDEX idx_buku_kategori (id_kategori),
    INDEX idx_buku_rak (id_rak),
    INDEX idx_buku_stok (jumlah_stok)

) ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_unicode_ci;


-- =========================================
-- TABEL RIWAYAT STATUS
-- Mencatat perubahan stok buku FINDLIB
-- =========================================

CREATE TABLE IF NOT EXISTS riwayat_status (
    id_riwayat BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_buku INT UNSIGNED NOT NULL,
    id_pengguna INT UNSIGNED NOT NULL,
    stok_sebelum INT UNSIGNED NOT NULL,
    stok_sesudah INT UNSIGNED NOT NULL,

    waktu_perubahan TIMESTAMP NOT NULL
        DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_riwayat_buku
        FOREIGN KEY (id_buku)
        REFERENCES buku(id_buku)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,

    CONSTRAINT fk_riwayat_pengguna
        FOREIGN KEY (id_pengguna)
        REFERENCES pengguna(id_pengguna)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,

    CONSTRAINT chk_riwayat_stok_sebelum
        CHECK (stok_sebelum >= 0),

    CONSTRAINT chk_riwayat_stok_sesudah
        CHECK (stok_sesudah >= 0),

    INDEX idx_riwayat_buku_waktu
        (id_buku, waktu_perubahan),

    INDEX idx_riwayat_pengguna
        (id_pengguna)

) ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_unicode_ci;

-- =========================================
-- TRIGGER PEMBARUAN STOK BUKU
-- Memperbarui waktu hanya jika stok berubah
-- =========================================

DELIMITER $$

CREATE TRIGGER trg_buku_before_update
BEFORE UPDATE ON buku
FOR EACH ROW
BEGIN
    IF NEW.jumlah_stok <> OLD.jumlah_stok THEN
        SET NEW.stock_updated_at = CURRENT_TIMESTAMP;
    ELSE
        SET NEW.stock_updated_at = OLD.stock_updated_at;
    END IF;
END$$

DELIMITER ;

-- =========================================
-- VIEW KATALOG BUKU FINDLIB
-- Menampilkan informasi dan status buku
-- =========================================

CREATE OR REPLACE VIEW view_katalog AS
SELECT
    b.id_buku,
    b.no_katalog,
    b.judul,
    b.penulis,
    b.penerbit,
    b.tahun_terbit,
    k.nama_kategori,
    r.kode_rak,
    r.nama_rak,
    b.jumlah_stok,

    CASE
        WHEN b.jumlah_stok > 0 THEN 'Tersedia'
        ELSE 'Tidak Tersedia'
    END AS status_ketersediaan,

    b.updated_at,
    b.stock_updated_at

FROM buku b

INNER JOIN kategori k
    ON b.id_kategori = k.id_kategori

INNER JOIN lokasi_rak r
    ON b.id_rak = r.id_rak;