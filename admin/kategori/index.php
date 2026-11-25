<?php
declare(strict_types=1);

require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../config/database.php';

wajibLogin();

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$stmt = $pdo->query(
    "SELECT id_kategori, nama_kategori
     FROM kategori
     ORDER BY nama_kategori"
);

$kategori = $stmt->fetchAll();

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
    <title>Kelola Kategori - FINDLIB</title>

    <link rel="stylesheet" href="/FindLib/assets/css/style.css">

    <style>
        .admin-content {
            max-width: 1100px;
            margin: 40px auto;
            padding: 20px;
        }

        .admin-heading {
            margin-bottom: 25px;
        }

        .admin-heading h1 {
            color: #173b70;
            margin-bottom: 8px;
        }

        .admin-heading p {
            color: #666;
        }

        .admin-actions {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            margin-bottom: 25px;
        }

        .add-button,
        .back-button {
            display: inline-block;
            padding: 11px 18px;
            border-radius: 7px;
            text-decoration: none;
            font-weight: bold;
        }

        .add-button {
            background: #2457a7;
            color: white;
        }

        .back-button {
            background: #e8edf4;
            color: #263b59;
        }

        .table-container {
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 3px 15px rgba(0,0,0,0.07);
            overflow-x: auto;
        }

        .category-table {
            width: 100%;
            border-collapse: collapse;
        }

        .category-table th,
        .category-table td {
            padding: 14px;
            border-bottom: 1px solid #e5eaf0;
            text-align: left;
        }

        .category-table th {
            background: #edf3fa;
            color: #173b70;
        }

        .action-edit {
            color: #2457a7;
            text-decoration: none;
            font-weight: bold;
        }

        .action-delete {
            background: #dc3545;
            color: white;
            padding: 7px 12px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .empty-state {
            text-align: center !important;
            color: #777;
            padding: 30px !important;
        }

        @media (max-width: 600px) {
            .admin-content {
                padding: 15px;
                margin: 20px auto;
            }

            .table-container {
                padding: 10px;
            }
        }
    </style>
</head>

<body>

<header>
    <nav class="navbar">
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
        <h1>Kelola Kategori</h1>
        <p>Daftar seluruh kategori buku FINDLIB.</p>
    </div>

    <div class="admin-actions">
        <a href="tambah.php" class="add-button">
            + Tambah Kategori
        </a>

        <a href="../dashboard.php" class="back-button">
            Kembali ke Dashboard
        </a>
    </div>

    <div class="table-container">

        <table class="category-table">
            <thead>
                <tr>
                    <th>No.</th>
                    <th>Nama Kategori</th>
                    <th>Aksi</th>
                </tr>
            </thead>

            <tbody>
                <?php if (empty($kategori)): ?>
                    <tr>
                        <td colspan="3" class="empty-state">
                            Belum ada kategori.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($kategori as $index => $item): ?>
                        <tr>
                            <td><?= $index + 1 ?></td>

                            <td><?= h($item['nama_kategori']) ?></td>

                            <td>
                                <a
                                    href="edit.php?id=<?= (int) $item['id_kategori'] ?>"
                                    class="action-edit"
                                >
                                    Edit
                                </a>

                                &nbsp; | &nbsp;

                                <form
                                    action="hapus.php"
                                    method="POST"
                                    style="display:inline;"
                                    onsubmit="return confirm('Yakin ingin menghapus kategori ini?');"
                                >
                                    <input
                                        type="hidden"
                                        name="id_kategori"
                                        value="<?= (int) $item['id_kategori'] ?>"
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
                <?php endif; ?>
            </tbody>
        </table>

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
