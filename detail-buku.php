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
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Buku - FINDLIB</title>
</head>
<body>

    <h1>Detail Buku</h1>

    <p>
        <a href="katalog.php">Kembali ke Katalog</a> |
        <a href="/FindLib/admin/login.php">Login Admin</a>
    </p>

    <hr>

    <h2><?= htmlspecialchars($book['judul'], ENT_QUOTES, 'UTF-8') ?></h2>

    <p>
        <strong>No. Katalog:</strong>
        <?= htmlspecialchars($book['no_katalog'], ENT_QUOTES, 'UTF-8') ?>
    </p>

    <p>
        <strong>Penulis:</strong>
        <?= htmlspecialchars($book['penulis'], ENT_QUOTES, 'UTF-8') ?>
    </p>

    <p>
        <strong>Penerbit:</strong>
        <?= htmlspecialchars($book['penerbit'], ENT_QUOTES, 'UTF-8') ?>
    </p>

    <p>
        <strong>Tahun Terbit:</strong>
        <?= htmlspecialchars((string) $book['tahun_terbit'], ENT_QUOTES, 'UTF-8') ?>
    </p>

    <p>
        <strong>Kategori:</strong>
        <?= htmlspecialchars($book['nama_kategori'], ENT_QUOTES, 'UTF-8') ?>
    </p>

    <p>
        <strong>Kode Rak:</strong>
        <?= htmlspecialchars($book['kode_rak'], ENT_QUOTES, 'UTF-8') ?>
    </p>

    <p>
        <strong>Nama Rak:</strong>
        <?= htmlspecialchars($book['nama_rak'], ENT_QUOTES, 'UTF-8') ?>
    </p>

    <p>
        <strong>Jumlah Stok:</strong>
        <?= (int) $book['jumlah_stok'] ?>
    </p>

    <p>
        <strong>Status:</strong>
        <?= (int) $book['jumlah_stok'] > 0
            ? 'Tersedia'
            : 'Tidak tersedia' ?>
    </p>

    <p>
        <strong>Terakhir Diperbarui:</strong>
        <?= htmlspecialchars((string) $book['stock_updated_at'], ENT_QUOTES, 'UTF-8') ?>
    </p>

</body>
</html>
