<?php
declare(strict_types=1);

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';

// Mengambil ID buku dari URL
$id = $_GET['id'] ?? '';

if (!is_string($id) || !ctype_digit($id) || (int) $id < 1) {
    http_response_code(404);
    $book = null;
} else {
    try {
        $book = getBookById($pdo, (int) $id);
    } catch (PDOException $e) {
        error_log('Book detail error: ' . $e->getMessage());
        http_response_code(500);
        exit('Terjadi kesalahan saat mengambil detail buku.');
    }

    if ($book === null) {
        http_response_code(404);
    }
}
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
    <nav class="navbar" aria-label="Navigasi utama">
        <div class="logo">📚 FINDLIB</div>

        <ul class="nav-menu">
            <li><a href="/FindLib/index.php">Beranda</a></li>
            <li><a href="/FindLib/katalog.php">Katalog Buku</a></li>
            <li><a href="/FindLib/login.php">Login Admin</a></li>
        </ul>
    </nav>
</header>

<main class="detail-page">
    <div class="detail-container">

        <a href="/FindLib/katalog.php" class="back-link">
            &larr; Kembali ke Katalog
        </a>

        <h1>Detail Buku FINDLIB</h1>

        <?php if ($book === null): ?>

            <div class="empty-state" role="status">
                <h2>Buku tidak ditemukan</h2>
                <p>
                    Buku yang kamu cari tidak tersedia
                    atau mungkin sudah tidak terdaftar.
                </p>

                <a href="/FindLib/katalog.php" class="book-detail-link">
                    Kembali ke Katalog Buku
                </a>
            </div>

        <?php else: ?>

            <article class="book-detail-card">
                <h2>
                    <?= htmlspecialchars(
                        (string) $book['judul'],
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>
                </h2>

                <dl class="book-detail-list">
                    <?php
                    $fields = [
                        'Nomor Katalog' => 'no_katalog',
                        'Judul Buku' => 'judul',
                        'Penulis' => 'penulis',
                        'Penerbit' => 'penerbit',
                        'Tahun Terbit' => 'tahun_terbit',
                        'Kategori' => 'nama_kategori',
                        'Kode Rak' => 'kode_rak',
                        'Nama Rak' => 'nama_rak',
                        'Jumlah Stok' => 'jumlah_stok',
                        'Pembaruan Stok Terakhir' => 'stock_updated_at'
                    ];
                    ?>

                    <?php foreach ($fields as $label => $key): ?>
                        <div class="detail-row">
                            <dt>
                                <?= htmlspecialchars(
                                    $label,
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>
                            </dt>

                            <dd>
                                <?= htmlspecialchars(
                                    (string) ($book[$key] ?? '-'),
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>
                            </dd>
                        </div>
                    <?php endforeach; ?>

                    <?php $available = (int) $book['jumlah_stok'] > 0; ?>

                    <div class="detail-row">
                        <dt>Status Ketersediaan</dt>
                        <dd>
                            <span class="status-badge <?= $available
                                ? 'status-available'
                                : 'status-unavailable' ?>">
                                <?= $available
                                    ? 'Tersedia'
                                    : 'Tidak Tersedia' ?>
                            </span>
                        </dd>
                    </div>
                </dl>
            </article>

        <?php endif; ?>

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
