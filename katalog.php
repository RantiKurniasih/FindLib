<?php
declare(strict_types=1);

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';

// Mengambil kata kunci pencarian
$keyword = $_GET['keyword'] ?? '';
$keyword = is_string($keyword) ? trim($keyword) : '';

// Mengambil filter kategori
$categoryId = $_GET['category'] ?? '';
$categoryId = is_string($categoryId) ? trim($categoryId) : '';

$categoryId = ($categoryId !== '' && ctype_digit($categoryId))
    ? (int) $categoryId
    : null;

try {
    // Mengambil daftar kategori dan buku dari database
    $categories = getCategories($pdo);
    $books = getBooks($pdo, $keyword, $categoryId);
} catch (PDOException $e) {
    error_log('Catalog error: ' . $e->getMessage());
    http_response_code(500);
    exit('Terjadi kesalahan saat mengambil data katalog.');
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
    <nav class="navbar" aria-label="Navigasi utama">
        <div class="logo">📚 FINDLIB</div>

        <ul class="nav-menu">
            <li><a href="/FindLib/index.php">Beranda</a></li>
            <li><a href="/FindLib/katalog.php" aria-current="page">Katalog Buku</a></li>
            <li><a href="/FindLib/login.php">Login Admin</a></li>
        </ul>
    </nav>
</header>

<main>
    <section class="features">
        <h1>Katalog Buku FINDLIB</h1>

        <p>
            Temukan koleksi buku Ruang Baca FMIPA
            Universitas Halu Oleo.
        </p>

        <form action="/FindLib/katalog.php" method="GET"
              role="search" class="search-box">

            <label for="keyword">Cari Buku</label>

            <div class="search-input">
                <input
                    type="search"
                    id="keyword"
                    name="keyword"
                    placeholder="Masukkan judul atau penulis..."
                    value="<?= htmlspecialchars($keyword, ENT_QUOTES, 'UTF-8') ?>"
                >

                <div class="category-filter">
                    <label for="category">Kategori Buku</label>

                    <select id="category" name="category">
                        <option value="">Semua Kategori</option>

                        <?php foreach ($categories as $category): ?>
                            <option
                                value="<?= (int) $category['id_kategori'] ?>"
                                <?= $categoryId === (int) $category['id_kategori']
                                    ? 'selected'
                                    : '' ?>
                            >
                                <?= htmlspecialchars(
                                    $category['nama_kategori'],
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <button type="submit">Cari Buku</button>
            </div>
        </form>

        <section class="catalog-results" aria-labelledby="catalog-title">
            <h2 id="catalog-title">Daftar Buku</h2>

            <?php if (!empty($books)): ?>
                <div class="book-grid">
                    <?php foreach ($books as $book): ?>
                        <?php
                        $stock = (int) $book['jumlah_stok'];
                        $available = $stock > 0;
                        ?>

                        <article class="book-card">
                            <div class="book-cover" aria-hidden="true">
                                📘
                            </div>

                            <div class="book-info">
                                <span class="book-category">
                                    <?= htmlspecialchars(
                                        $book['nama_kategori'],
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>
                                </span>

                                <h3>
                                    <?= htmlspecialchars(
                                        $book['judul'],
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>
                                </h3>

                                <p class="book-author">
                                    Penulis:
                                    <?= htmlspecialchars(
                                        $book['penulis'],
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>
                                </p>

                                <p>Stok: <?= $stock ?> buku</p>

                                <span class="status-badge <?= $available
                                    ? 'status-available'
                                    : 'status-unavailable' ?>">
                                    <?= $available
                                        ? 'Tersedia'
                                        : 'Tidak Tersedia' ?>
                                </span>

                                <a
                                    class="book-detail-link"
                                    href="/FindLib/detail-buku.php?id=<?= (int) $book['id_buku'] ?>"
                                >
                                    Lihat Detail Buku
                                </a>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>

            <?php else: ?>
                <div class="empty-state" role="status">
                    <h3>Buku tidak ditemukan</h3>
                    <p>
                        Belum ada buku yang sesuai dengan pencarian.
                    </p>
                </div>
            <?php endif; ?>
        </section>
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
