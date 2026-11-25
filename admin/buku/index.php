<?php
declare(strict_types=1);

require_once __DIR__ . '/../../includes/auth.php';
$pdo = require __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';

wajibLogin();

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

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

    <style>
        .features {
            max-width: 1200px;
            margin: 40px auto;
            padding: 25px;
        }

        .book-table-wrapper {
            width: 100%;
            overflow-x: auto;
            margin-top: 25px;
            border-radius: 10px;
        }

        .book-table {
            width: 100%;
            min-width: 950px;
            border-collapse: separate;
            border-spacing: 0;
            table-layout: auto;
        }

        .book-table th,
        .book-table td {
            padding: 15px 18px;
            text-align: left;
            vertical-align: middle;
            line-height: 1.5;
        }

        .book-table th {
            white-space: nowrap;
        }

        .book-table td {
            overflow-wrap: break-word;
        }

        .book-table th:nth-child(1),
        .book-table td:nth-child(1) {
            width: 55px;
            text-align: center;
        }

        .book-table th:nth-child(2),
        .book-table td:nth-child(2) {
            min-width: 120px;
        }

        .book-table th:nth-child(3),
        .book-table td:nth-child(3) {
            min-width: 210px;
        }

        .book-table th:nth-child(4),
        .book-table td:nth-child(4) {
            min-width: 150px;
        }

        .book-table th:nth-child(5),
        .book-table td:nth-child(5) {
            min-width: 130px;
        }

        .book-table th:nth-child(6),
        .book-table td:nth-child(6) {
            width: 75px;
            text-align: center;
        }

        .book-table th:nth-child(7),
        .book-table td:nth-child(7) {
            min-width: 130px;
        }

        .book-table th:nth-child(8),
        .book-table td:nth-child(8) {
            min-width: 140px;
            white-space: nowrap;
        }

        .book-action {
            display: inline-flex;
            align-items: center;
            gap: 10px;
        }

        .book-action a,
        .book-action button {
            padding: 8px 13px;
            border-radius: 6px;
            cursor: pointer;
            text-decoration: none;
            font-size: 14px;
        }

        .book-action form {
            display: inline;
            margin: 0;
        }

        @media (max-width: 768px) {
            .features {
                padding: 15px;
                margin: 20px auto;
            }

            .book-table th,
            .book-table td {
                padding: 12px 14px;
            }
        }
    </style>
</head>

<body>

<header>
    <nav class="navbar" aria-label="Navigasi admin">
        <div class="logo">📚 FINDLIB Admin</div>

        <ul class="nav-menu">
            <li><a href="/FindLib/index.php">Beranda</a></li>
            <li><a href="/FindLib/katalog.php">Katalog Buku</a></li>
            <li><a href="/FindLib/admin/dashboard.php">Dashboard Admin</a></li>
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

        <div class="book-table-wrapper">
            <table class="book-table">
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

                            <td><?= htmlspecialchars(
                                $book['no_katalog'],
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?></td>

                            <td><?= htmlspecialchars(
                                $book['judul'],
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?></td>

                            <td><?= htmlspecialchars(
                                $book['penulis'],
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?></td>

                            <td><?= htmlspecialchars(
                                $book['nama_kategori'],
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?></td>

                            <td><?= (int) $book['jumlah_stok'] ?></td>

                            <td>
                                <?= $available
                                    ? 'Tersedia'
                                    : 'Tidak Tersedia' ?>
                            </td>

                            <td>
                                <div class="book-action">
                                    <a href="edit.php?id=<?= (int) $book['id_buku'] ?>">
                                        Edit
                                    </a>

                                    <form
                                        method="POST"
                                        action="hapus.php"
                                        onsubmit="return confirm('Apakah kamu yakin ingin menghapus buku ini?');"
                                    >
                                        <input
                                            type="hidden"
                                            name="id_buku"
                                            value="<?= (int) $book['id_buku'] ?>"
                                        >

                                        <input
                                            type="hidden"
                                            name="csrf_token"
                                            value="<?= htmlspecialchars(
                                                $_SESSION['csrf_token'],
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>"
                                        >

                                        <button type="submit">Hapus</button>
                                    </form>
                                </div>
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
