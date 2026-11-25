<?php
declare(strict_types=1);

require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../config/database.php';
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
    <title>Kelola Buku - Admin FINDLIB</title>
    <link rel="stylesheet" href="/FindLib/assets/css/style.css">

    <style>
        .admin-content {
            max-width: 1200px;
            margin: 45px auto;
            padding: 20px;
        }

        .admin-heading {
            margin-bottom: 25px;
        }

        .admin-heading h1 {
            color: #173b70;
            margin-bottom: 10px;
        }

        .add-book-button {
            display: inline-block;
            background: #2457a7;
            color: white;
            padding: 12px 22px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: bold;
            margin-bottom: 25px;
        }

        .add-book-button:hover {
            background: #173b70;
        }

        .table-container {
            overflow-x: auto;
            background: white;
            border-radius: 12px;
            box-shadow: 0 3px 14px rgba(0,0,0,0.08);
        }

        .table-container table {
            width: 100%;
            border-collapse: collapse;
            min-width: 850px;
        }

        .table-container th {
            background: #173b70;
            color: white;
            padding: 15px;
            text-align: left;
        }

        .table-container td {
            padding: 14px 15px;
            border-bottom: 1px solid #e8edf4;
        }

        .table-container tr:hover td {
            background: #f5f8fc;
        }

        .stock-badge {
            display: inline-block;
            padding: 6px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
        }

        .available {
            background: #dff5e5;
            color: #217a3b;
        }

        .unavailable {
            background: #fde4e4;
            color: #b52b2b;
        }

        .action-edit,
        .action-delete {
            display: inline-block;
            padding: 7px 12px;
            border: none;
            border-radius: 6px;
            text-decoration: none;
            color: white;
            cursor: pointer;
            font-size: 13px;
        }

        .action-edit {
            background: #2878c7;
        }

        .action-delete {
            background: #c0392b;
        }

        .action-edit:hover {
            background: #185b9c;
        }

        .action-delete:hover {
            background: #962d22;
        }

        .empty-state {
            background: white;
            padding: 35px;
            border-radius: 12px;
            text-align: center;
            box-shadow: 0 3px 14px rgba(0,0,0,0.08);
        }

        @media (max-width: 600px) {
            .admin-content {
                padding: 15px;
                margin: 25px auto;
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

<main class="admin-content">

    <div class="admin-heading">
        <h1>Kelola Data Buku</h1>
        <p>Daftar seluruh koleksi buku FINDLIB.</p>
    </div>

    <a href="tambah.php" class="add-book-button">
        + Tambah Buku
    </a>

    <?php if (!empty($books)): ?>

        <div class="table-container">
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
                            <td><?= h($book['no_katalog']) ?></td>
                            <td><strong><?= h($book['judul']) ?></strong></td>
                            <td><?= h($book['penulis']) ?></td>
                            <td><?= h($book['nama_kategori']) ?></td>
                            <td><?= (int) $book['jumlah_stok'] ?></td>

                            <td>
                                <span class="stock-badge <?= $available
                                    ? 'available'
                                    : 'unavailable' ?>">
                                    <?= $available
                                        ? 'Tersedia'
                                        : 'Tidak Tersedia' ?>
                                </span>
                            </td>

                            <td>
                                <a
                                    href="edit.php?id=<?= (int) $book['id_buku'] ?>"
                                    class="action-edit"
                                >
                                    Edit
                                </a>

                                <form
                                    method="POST"
                                    action="hapus.php"
                                    style="display:inline;"
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
                                        value="<?= h($_SESSION['csrf_token']) ?>"
                                    >

                                    <button
                                        type="submit"
                                        class="action-delete"
                                    >
                                        Hapus
                                    </button>
                                </form>
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
