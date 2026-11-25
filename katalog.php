<?php
declare(strict_types=1);

$pdo = require __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';

$keyword = $_GET['keyword'] ?? '';
$keyword = is_string($keyword) ? trim($keyword) : '';

$categoryId = $_GET['category'] ?? null;

if ($categoryId !== null && $categoryId !== '' && ctype_digit((string) $categoryId)) {
    $categoryId = (int) $categoryId;
} else {
    $categoryId = null;
}

try {
    $categories = getCategories($pdo);
    $books = getBooks($pdo, $keyword, $categoryId);
} catch (PDOException $e) {
    error_log('Catalog error: ' . $e->getMessage());

    $categories = [];
    $books = [];
    $error = 'Terjadi kesalahan saat mengambil data buku.';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Katalog Buku - FINDLIB</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

    <header class="navbar">
        <a href="index.php" class="logo">FINDLIB</a>
        <nav class="nav-menu">
            <a href="index.php">Beranda</a>
            <a href="katalog.php">Katalog Buku</a>
            <a href="admin/login.php">Login Admin</a>
        </nav>
    </header>

    <main class="features">
        <h1>Jelajahi Katalog Buku</h1>
        <p>Temukan informasi ketersediaan buku di Ruang Baca FMIPA UHO.</p>

        <div class="search-box">
            <form method="GET" action="katalog.php">
                <label for="keyword">Cari Buku</label>

                <div class="search-input">
                    <input
                        type="text"
                        id="keyword"
                        name="keyword"
                        placeholder="Masukkan judul atau nama penulis..."
                        value="<?= htmlspecialchars($keyword, ENT_QUOTES, 'UTF-8') ?>"
                    >

                    <select name="category">
                        <option value="">Semua Kategori</option>
                        <?php foreach ($categories as $category): ?>
                            <option
                                value="<?= (int) $category['id_kategori'] ?>"
                                <?= $categoryId === (int) $category['id_kategori'] ? 'selected' : '' ?>
                            >
                                <?= htmlspecialchars($category['nama_kategori'], ENT_QUOTES, 'UTF-8') ?>
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <button type="submit">Cari</button>
                </div>
            </form>

            <a href="katalog.php">Reset Pencarian</a>
        </div>

        <?php if (isset($error)): ?>
            <p><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></p>

        <?php elseif (empty($books)): ?>
            <div class="empty-state">
                <p>Buku tidak ditemukan.</p>
                <a href="katalog.php" class="book-detail-link">Lihat Semua Buku</a>
            </div>

        <?php else: ?>
            <div class="book-grid">
                <?php foreach ($books as $book): ?>
                    <article class="book-card">
                        <div class="book-info">
                            <span class="book-category">
                                <?= htmlspecialchars($book['nama_kategori'], ENT_QUOTES, 'UTF-8') ?>
                            </span>

                            <h3>
                                <?= htmlspecialchars($book['judul'], ENT_QUOTES, 'UTF-8') ?>
                            </h3>

                            <p class="book-author">
                                Penulis:
                                <?= htmlspecialchars($book['penulis'], ENT_QUOTES, 'UTF-8') ?>
                            </p>

                            <p>Stok: <?= (int) $book['jumlah_stok'] ?></p>

                            <p>
                                Status:
                                <?= (int) $book['jumlah_stok'] > 0
                                    ? 'Tersedia'
                                    : 'Tidak tersedia' ?>
                            </p>

                            <a
                                href="detail-buku.php?id=<?= (int) $book['id_buku'] ?>"
                                class="book-detail-link"
                            >
                                Lihat Detail
                            </a>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </main>

</body>
</html>