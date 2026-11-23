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
            <li>
                <a href="/FindLib/index.php">Beranda</a>
            </li>
            <li>
                <a href="/FindLib/katalog.php" aria-current="page">
                    Katalog Buku
                </a>
            </li>
            <li>
                <a href="/FindLib/admin/login.php">
                    Login Admin
                </a>
            </li>
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

        <form
            action="/FindLib/katalog.php"
            method="GET"
            role="search"
            class="search-box"
        >
            <label for="keyword">Cari Buku</label>

            <div class="search-input">
                <input
                    type="search"
                    id="keyword"
                    name="keyword"
                    placeholder="Masukkan judul atau penulis..."
                    value="<?= htmlspecialchars(
                        (string)($_GET['keyword'] ?? ''),
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>"
                >

                <div class="category-filter">
    <label for="category">Kategori Buku</label>

    <select id="category" name="category">
        <option value="">Semua Kategori</option>

        <!-- Pilihan kategori akan diambil
             dari database melalui backend Rangga -->
    </select>
</div>
                <button type="submit">
                    Cari Buku
                </button>
            </div>
        </form>

 <section class="catalog-results" aria-labelledby="catalog-title">
    <h2 id="catalog-title">Daftar Buku</h2>

    <?php
    // Nanti data buku diambil melalui:
    // $books = getBooks($pdo, $keyword, $categoryId);

    // Belum menggunakan database pada tahap ini.
    $books = [];
    ?>

    <?php if (!empty($books)): ?>
    <div class="book-grid">
        <?php foreach ($books as $book): ?>
            <?php
            $stock = (int)($book['jumlah_stok'] ?? 0);
            $available = $stock > 0;
            ?>

            <article class="book-card">
                <div class="book-cover" aria-hidden="true">
                    📘
                </div>

                <div class="book-info">
                    <span class="book-category">
                        <?= htmlspecialchars(
                            (string)($book['nama_kategori'] ?? '-'),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </span>

                    <h3>
                        <?= htmlspecialchars(
                            (string)($book['judul'] ?? ''),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </h3>

                    <p class="book-author">
                        Penulis:
                        <?= htmlspecialchars(
                            (string)($book['penulis'] ?? '-'),
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
                        href="/FindLib/detail-buku.php?id=<?= (int)($book['id_buku'] ?? 0) ?>"
                    >
                        Lihat Detail Buku
                    </a>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
<?php else: ?>
        <div class="empty-state" role="status">
            <h3>Data buku belum tersedia</h3>
            <p>
                Katalog akan menampilkan koleksi buku
                setelah terhubung dengan backend FINDLIB.
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