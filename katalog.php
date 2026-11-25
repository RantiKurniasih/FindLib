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

function h(mixed $value): string
{
    return htmlspecialchars(
        (string) $value,
        ENT_QUOTES | ENT_SUBSTITUTE,
        'UTF-8'
    );
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Katalog Buku - FINDLIB</title>
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

<main>
    <section class="features">
        <h2>Katalog Buku FINDLIB</h2>

        <form method="GET" action="/FindLib/katalog.php"
              class="category-filter">

            <input
                type="search"
                name="keyword"
                placeholder="Cari judul atau penulis buku..."
                value="<?= h($keyword) ?>"
            >

            <select name="category">
                <option value="">Semua Kategori</option>

                <?php foreach ($categories as $category): ?>
                    <option
                        value="<?= (int) $category['id_kategori'] ?>"
                        <?= $categoryId === (int) $category['id_kategori']
                            ? 'selected'
                            : '' ?>
                    >
                        <?= h($category['nama_kategori']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <button type="submit">Cari Buku</button>
            <a href="/FindLib/katalog.php">Reset</a>
        </form>

        <?php if (empty($books)): ?>

            <p>Buku tidak ditemukan.</p>

        <?php else: ?>

            <div class="book-grid">

                <?php foreach ($books as $book): ?>
                    <article class="book-card">

                        <h3><?= h($book['judul']) ?></h3>

                        <p>
                            <strong>Penulis:</strong>
                            <?= h($book['penulis']) ?>
                        </p>

                        <p>
                            <strong>Kategori:</strong>
                            <?= h($book['nama_kategori']) ?>
                        </p>

                        <p>
                            <strong>Stok:</strong>
                            <?= (int) $book['jumlah_stok'] ?>
                        </p>

                        <p>
                            <span class="status-badge">
                                <?= (int) $book['jumlah_stok'] > 0
                                    ? 'Tersedia'
                                    : 'Tidak Tersedia' ?>
                            </span>
                        </p>

                        <a
                            class="book-detail-link"
                            href="/FindLib/detail-buku.php?id=<?= (int) $book['id_buku'] ?>"
                        >
                            Lihat Detail
                        </a>

                    </article>
                <?php endforeach; ?>

            </div>

        <?php endif; ?>
    </section>
</main>

<footer>
    <p>
        &copy; <?= date('Y') ?> FINDLIB -
        Ruang Baca FMIPA Universitas Halu Oleo
    </p>
</footer>

</body>
</html>
