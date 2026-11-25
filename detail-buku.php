<?php
declare(strict_types=1);

$pdo = require __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';

$id = $_GET['id'] ?? '';

if (!is_string($id) || !ctype_digit($id) || (int) $id < 1) {
    http_response_code(404);
    exit('Buku tidak ditemukan.');
}

try {
    $book = getBookById($pdo, (int) $id);
} catch (PDOException $e) {
    error_log('Book detail error: ' . $e->getMessage());
    http_response_code(500);
    exit('Terjadi kesalahan saat memuat detail buku.');
}

if (!$book) {
    http_response_code(404);
    exit('Buku tidak ditemukan.');
}

$stok = (int) $book['jumlah_stok'];
$tersedia = $stok > 0;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>
        <?= htmlspecialchars($book['judul'], ENT_QUOTES, 'UTF-8') ?> - FINDLIB
    </title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            background: #f3f6fa;
            color: #334155;
        }

        .navbar {
            width: 100%;
            min-height: 75px;
            padding: 0 7%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #17365d;
            color: white;
        }

        .logo {
            font-size: 27px;
            font-weight: bold;
            letter-spacing: 1px;
        }

        .logo span {
            color: #8db9e5;
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 25px;
        }

        .nav-links a {
            color: #e2e8f0;
            text-decoration: none;
            font-size: 14px;
            transition: .2s;
        }

        .nav-links a:hover {
            color: white;
        }

        .hero {
            padding: 45px 7% 55px;
            background: linear-gradient(120deg, #17365d, #285786);
            color: white;
        }

        .hero .breadcrumb {
            margin-bottom: 20px;
            color: #cbd9e9;
            font-size: 13px;
        }

        .hero .breadcrumb a {
            color: white;
            text-decoration: none;
        }

        .hero h1 {
            font-size: 32px;
            margin-bottom: 10px;
        }

        .hero p {
            color: #d5e2ef;
            font-size: 14px;
            line-height: 1.7;
        }

        .container {
            width: 86%;
            max-width: 1150px;
            margin: -25px auto 60px;
            position: relative;
        }

        .book-card {
            background: white;
            border-radius: 13px;
            padding: 35px;
            box-shadow: 0 8px 30px rgba(15, 23, 42, .08);
        }

        .book-heading {
            padding-bottom: 25px;
            border-bottom: 1px solid #e5eaf0;
            margin-bottom: 28px;
        }

        .book-heading .label {
            display: inline-block;
            padding: 7px 12px;
            border-radius: 5px;
            background: #eaf1f8;
            color: #24558a;
            font-size: 12px;
            font-weight: bold;
            margin-bottom: 15px;
        }

        .book-heading h2 {
            color: #17365d;
            font-size: 27px;
            line-height: 1.4;
            margin-bottom: 12px;
        }

        .book-heading p {
            color: #64748b;
            font-size: 14px;
        }

        .section-title {
            color: #17365d;
            font-size: 18px;
            margin-bottom: 20px;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 18px;
        }

        .info-item {
            padding: 18px;
            border: 1px solid #e6ebf1;
            border-radius: 9px;
            background: #fbfcfe;
        }

        .info-item .info-label {
            display: block;
            margin-bottom: 9px;
            color: #8491a3;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: .4px;
        }

        .info-item .info-value {
            color: #334155;
            font-size: 14px;
            font-weight: 600;
            line-height: 1.5;
            overflow-wrap: anywhere;
        }

        .stock-section {
            margin-top: 30px;
            padding: 23px;
            border-radius: 10px;
            background: #f5f8fc;
        }

        .stock-section h3 {
            color: #17365d;
            font-size: 17px;
            margin-bottom: 17px;
        }

        .stock-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 15px;
        }

        .stock-box {
            padding: 17px;
            background: white;
            border: 1px solid #e5eaf0;
            border-radius: 8px;
        }

        .stock-box span {
            display: block;
            color: #8491a3;
            font-size: 12px;
            margin-bottom: 9px;
        }

        .stock-box strong {
            color: #17365d;
            font-size: 20px;
        }

        .status {
            display: inline-block;
            padding: 7px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
        }

        .status.available {
            background: #dcfce7;
            color: #15803d;
        }

        .status.unavailable {
            background: #fee2e2;
            color: #b91c1c;
        }

        .updated {
            margin-top: 20px;
            color: #94a3b8;
            font-size: 12px;
        }

        .bottom-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 15px;
            margin-top: 30px;
            padding-top: 25px;
            border-top: 1px solid #e5eaf0;
        }

        .btn {
            display: inline-flex;
            justify-content: center;
            align-items: center;
            min-height: 44px;
            padding: 0 20px;
            border-radius: 7px;
            text-decoration: none;
            font-size: 13px;
            font-weight: bold;
            transition: .2s;
        }

        .btn-primary {
            background: #24558a;
            color: white;
        }

        .btn-primary:hover {
            background: #17365d;
        }

        .btn-secondary {
            background: #e8edf3;
            color: #334155;
        }

        .btn-secondary:hover {
            background: #d9e2ec;
        }

        footer {
            padding: 25px 7%;
            background: #17365d;
            color: #cbd5e1;
            text-align: center;
            font-size: 13px;
            line-height: 1.7;
        }

        @media (max-width: 700px) {
            .navbar {
                padding: 18px 6%;
                flex-direction: column;
                gap: 15px;
            }

            .nav-links {
                gap: 18px;
            }

            .hero {
                padding: 35px 6% 50px;
            }

            .hero h1 {
                font-size: 25px;
            }

            .container {
                width: 92%;
            }

            .book-card {
                padding: 23px 18px;
            }

            .book-heading h2 {
                font-size: 22px;
            }

            .info-grid {
                grid-template-columns: 1fr;
            }

            .stock-grid {
                grid-template-columns: 1fr;
            }

            .bottom-actions {
                flex-direction: column;
                align-items: stretch;
            }

            .bottom-actions .btn {
                width: 100%;
            }
        }
    </style>
</head>

<body>

    <header class="navbar">
        <div class="logo">FIND<span>LIB</span></div>

        <nav class="nav-links">
            <a href="index.php">Beranda</a>
            <a href="katalog.php">Katalog Buku</a>
            <a href="/FindLib/admin/login.php">Login Admin</a>
        </nav>
    </header>

    <section class="hero">
        <div class="breadcrumb">
            <a href="index.php">Beranda</a>
            &nbsp; / &nbsp;
            <a href="katalog.php">Katalog Buku</a>
            &nbsp; / &nbsp;
            Detail Buku
        </div>

        <h1>Detail Buku</h1>
        <p>Informasi lengkap koleksi buku Ruang Baca FMIPA.</p>
    </section>

    <main class="container">

        <section class="book-card">

            <div class="book-heading">
                <span class="label">INFORMASI KOLEKSI BUKU</span>

                <h2>
                    <?= htmlspecialchars($book['judul'], ENT_QUOTES, 'UTF-8') ?>
                </h2>

                <p>
                    Nomor katalog:
                    <?= htmlspecialchars($book['no_katalog'], ENT_QUOTES, 'UTF-8') ?>
                </p>
            </div>

            <h3 class="section-title">Informasi Bibliografi</h3>

            <div class="info-grid">

                <div class="info-item">
                    <span class="info-label">Penulis</span>
                    <div class="info-value">
                        <?= htmlspecialchars($book['penulis'], ENT_QUOTES, 'UTF-8') ?>
                    </div>
                </div>

                <div class="info-item">
                    <span class="info-label">Penerbit</span>
                    <div class="info-value">
                        <?= htmlspecialchars($book['penerbit'], ENT_QUOTES, 'UTF-8') ?>
                    </div>
                </div>

                <div class="info-item">
                    <span class="info-label">Tahun Terbit</span>
                    <div class="info-value">
                        <?= htmlspecialchars((string) $book['tahun_terbit'], ENT_QUOTES, 'UTF-8') ?>
                    </div>
                </div>

                <div class="info-item">
                    <span class="info-label">Kategori</span>
                    <div class="info-value">
                        <?= htmlspecialchars($book['nama_kategori'], ENT_QUOTES, 'UTF-8') ?>
                    </div>
                </div>

                <div class="info-item">
                    <span class="info-label">Kode Rak</span>
                    <div class="info-value">
                        <?= htmlspecialchars($book['kode_rak'], ENT_QUOTES, 'UTF-8') ?>
                    </div>
                </div>

                <div class="info-item">
                    <span class="info-label">Nama Rak</span>
                    <div class="info-value">
                        <?= htmlspecialchars($book['nama_rak'], ENT_QUOTES, 'UTF-8') ?>
                    </div>
                </div>

            </div>

            <div class="stock-section">
                <h3>Informasi Ketersediaan Buku</h3>

                <div class="stock-grid">

                    <div class="stock-box">
                        <span>Jumlah Stok</span>
                        <strong><?= $stok ?></strong>
                    </div>

                    <div class="stock-box">
                        <span>Status Buku</span>

                        <strong>
                            <span class="status <?= $tersedia ? 'available' : 'unavailable' ?>">
                                <?= $tersedia ? 'Tersedia' : 'Tidak tersedia' ?>
                            </span>
                        </strong>
                    </div>

                    <div class="stock-box">
                        <span>Lokasi Penyimpanan</span>
                        <strong style="font-size:15px;">
                            <?= htmlspecialchars($book['kode_rak'], ENT_QUOTES, 'UTF-8') ?>
                        </strong>
                    </div>

                </div>

                <p class="updated">
                    Terakhir diperbarui:
                    <?= htmlspecialchars((string) $book['stock_updated_at'], ENT_QUOTES, 'UTF-8') ?>
                </p>
            </div>

            <div class="bottom-actions">
                <a href="katalog.php" class="btn btn-secondary">
                    &larr; Kembali ke Katalog
                </a>

                <a href="/FindLib/admin/login.php" class="btn btn-primary">
                    Login Admin
                </a>
            </div>

        </section>

    </main>

    <footer>
        <strong>FINDLIB</strong> - Find Library<br>
        Sistem Informasi Ketersediaan Buku Ruang Baca FMIPA
    </footer>

</body>
</html>
