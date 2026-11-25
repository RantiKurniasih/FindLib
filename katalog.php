<?php
declare(strict_types=1);

$pdo = require __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';

$keyword = $_GET['keyword'] ?? '';
$keyword = is_string($keyword) ? trim($keyword) : '';

$categoryId = $_GET['category'] ?? null;

if (
    !is_string($categoryId)
    || !ctype_digit($categoryId)
    || (int) $categoryId < 1
) {
    $categoryId = null;
} else {
    $categoryId = (int) $categoryId;
}

try {
    $categories = getCategories($pdo);
    $books = getBooks($pdo, $keyword, $categoryId);
} catch (PDOException $e) {
    error_log('Catalog error: ' . $e->getMessage());
    http_response_code(500);
    exit('Terjadi kesalahan saat memuat katalog buku.');
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Katalog Buku - FINDLIB</title>
</head>
<body>

    <h1>Katalog Buku FINDLIB</h1>

    <p>
        <a href="index.php">Beranda</a> |
        <a href="/FindLib/admin/login.php">Login Admin</a>
    </p>

    <form method="GET" action="katalog.php">
        <input
            type="text"
            name="keyword"
            placeholder="Cari judul atau penulis buku..."
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
        <a href="katalog.php">Reset</a>
    </form>

    <hr>

    <?php if (empty($books)): ?>
        <p>Buku tidak ditemukan.</p>
    <?php else: ?>
        <?php foreach ($books as $book): ?>
            <article>
                <h2>
                    <?= htmlspecialchars($book['judul'], ENT_QUOTES, 'UTF-8') ?>
                </h2>

                <p>
                    Penulis:
                    <?= htmlspecialchars($book['penulis'], ENT_QUOTES, 'UTF-8') ?>
                </p>

                <p>
                    Kategori:
                    <?= htmlspecialchars($book['nama_kategori'], ENT_QUOTES, 'UTF-8') ?>
                </p>

                <p>
                    Stok: <?= (int) $book['jumlah_stok'] ?>
                </p>

                <p>
                    Status:
                    <?= (int) $book['jumlah_stok'] > 0
                        ? 'Tersedia'
                        : 'Tidak tersedia' ?>
                </p>

                <a href="detail-buku.php?id=<?= (int) $book['id_buku'] ?>">
                    Lihat Detail
                </a>
            </article>
            <hr>
        <?php endforeach; ?>
    <?php endif; ?>

</body>
</html>
