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

function h(mixed $value): string
{
    return htmlspecialchars(
        (string) $value,
        ENT_QUOTES | ENT_SUBSTITUTE,
        'UTF-8'
    );
}

$available = (int) $book['jumlah_stok'] > 0;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Buku - FINDLIB</title>
    <link rel="stylesheet" href="/FindLib/assets/css/style.css">
</head>
<body>

<header>
    <nav class="navbar">
        <div class="logo">📚 FINDLIB</div>

        <ul class="nav-menu">
            <li><a href="/FindLib/index.php">Beranda</a></li>
            <li><a href="/FindLib/katalog.php">Katalog Buku</a></li>
            <li><a href="/FindLib/admin/login.php">Login Admin</a></li>
        </ul>
    </nav>
</header>

<main class="detail-page">
    <div class="detail-container">

        <a href="/FindLib/katalog.php" class="back-link">
            &larr; Kembali ke Katalog
        </a>

        <article class="book-detail-card">

            <h1><?= h($book['judul']) ?></h1>

            <span class="status-badge <?= $available
                ? 'status-available'
                : 'status-unavailable' ?>">
                <?= $available ? 'Tersedia' : 'Tidak Tersedia' ?>
            </span>

            <div class="detail-row">
                <strong>No. Katalog</strong>
                <span><?= h($book['no_katalog']) ?></span>
            </div>

            <div class="detail-row">
                <strong>Penulis</strong>
                <span><?= h($book['penulis']) ?></span>
            </div>

            <div class="detail-row">
                <strong>Penerbit</strong>
                <span><?= h($book['penerbit']) ?></span>
            </div>

            <div class="detail-row">
                <strong>Tahun Terbit</strong>
                <span><?= h($book['tahun_terbit']) ?></span>
            </div>

            <div class="detail-row">
                <strong>Kategori</strong>
                <span><?= h($book['nama_kategori']) ?></span>
            </div>

            <div class="detail-row">
                <strong>Kode Rak</strong>
                <span><?= h($book['kode_rak']) ?></span>
            </div>

            <div class="detail-row">
                <strong>Nama Rak</strong>
                <span><?= h($book['nama_rak']) ?></span>
            </div>

            <div class="detail-row">
                <strong>Jumlah Stok</strong>
                <span><?= (int) $book['jumlah_stok'] ?> buku</span>
            </div>

            <div class="detail-row">
                <strong>Terakhir Diperbarui</strong>
                <span>
                    <?= !empty($book['stock_updated_at'])
                        ? h($book['stock_updated_at'])
                        : '-' ?>
                </span>
            </div>

        </article>

        <a href="/FindLib/katalog.php" class="back-link">
            &larr; Kembali ke Katalog
        </a>

    </div>
</main>

<footer>
    <p>
        &copy; <?= date('Y') ?> FINDLIB -
        Ruang Baca FMIPA Universitas Halu Oleo
    </p>
</footer>

</body>
</html>
