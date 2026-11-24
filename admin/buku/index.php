<?php
declare(strict_types=1);

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';

// Mengambil seluruh data buku
try {
    $books = getBooks($pdo);
} catch (PDOException $e) {
    error_log('Admin book list error: ' . $e->getMessage());
    http_response_code(500);
    exit('Terjadi kesalahan saat mengambil data buku.');
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Buku - Admin FINDLIB</title>
    <link rel="stylesheet" href="/FindLib/assets/css/style.css">
</head>
<body>

<header>
    <nav class="navbar" aria-label="Navigasi admin">
        <div class="logo">📚 FINDLIB Admin</div>

        <ul class="nav-menu">
            <li><a href="/FindLib/index.php">Beranda</a></li>
            <li><a href="/FindLib/katalog.php">Katalog Buku</a></li>
            <li><a href="/FindLib/login.php">Login Admin</a></li>
        </ul>
    </nav>
</header>

<main class="features">
    <h1>Kelola Data Buku</h1>

    <p>Daftar seluruh koleksi buku FINDLIB.</p>

    <a href="tambah.php" class="book-detail-link">
        + Tambah Buku
    </a>

    <?php if (!empty($books)): ?>

        <div class="catalog-results">
            <table>
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>No. Katalog</th>
                        <th>Judul Buku</th>
                        <th>Penulis</th>
                        <th>Kategori</th>
                        <th>Stok</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach ($books as $index => $book): ?>
                        <?php $available = (int) $book['jumlah_stok'] > 0; ?>

                        <tr>
                            <td><?= $index + 1 ?></td>

                            <td>
                                <?= htmlspecialchars(
                                    $book['no_katalog'],
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>
                            </td>

                            <td>
                                <?= htmlspecialchars(
                                    $book['judul'],
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>
                            </td>

                            <td>
                                <?= htmlspecialchars(
                                    $book['penulis'],
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>
                            </td>

                            <td>
                                <?= htmlspecialchars(
                                    $book['nama_kategori'],
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>
                            </td>

                            <td><?= (int) $book['jumlah_stok'] ?></td>

                            <td>
                                <?= $available
                                    ? 'Tersedia'
                                    : 'Tidak Tersedia' ?>
                            </td>

                            <td>
                                <a href="edit.php?id=<?= (int) $book['id_buku'] ?>">
                                    Edit
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

    <?php else: ?>

        <div class="empty-state">
            <h2>Belum Ada Data Buku</h2>
            <p>Silakan tambahkan buku ke dalam katalog.</p>
        </div>

    <?php endif; ?>
</main>

<footer>
    <p>
        &copy; <?= date('Y') ?> FINDLIB -
        Ruang Baca FMIPA Universitas Halu Oleo
    </p>
</footer>

</body>
</html>
