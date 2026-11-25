<?php
declare(strict_types=1);

require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../config/database.php';

wajibLogin();

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

function h(mixed $value): string
{
    return htmlspecialchars(
        (string) $value,
        ENT_QUOTES | ENT_SUBSTITUTE,
        'UTF-8'
    );
}

$stmt = $pdo->query(
    "SELECT id_rak, kode_rak, nama_rak
     FROM lokasi_rak
     ORDER BY kode_rak"
);

$rak = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Rak - FINDLIB</title>
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
            margin-bottom: 8px;
            color: #1e293b;
        }

        .admin-heading p {
            color: #64748b;
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
            font-weight: 600;
        }

        .add-button {
            background: #2563eb;
            color: white;
        }

        .back-button {
            background: #e2e8f0;
            color: #334155;
        }

        .table-container {
            overflow-x: auto;
            background: white;
            border-radius: 12px;
            box-shadow: 0 3px 12px rgba(0, 0, 0, 0.06);
        }

        .admin-table {
            width: 100%;
            border-collapse: collapse;
        }

        .admin-table th,
        .admin-table td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #e2e8f0;
        }

        .admin-table th {
            background: #eff6ff;
            color: #1e3a8a;
        }

        .admin-table tbody tr:hover {
            background: #f8fafc;
        }

        .action-edit {
            color: #2563eb;
            text-decoration: none;
            font-weight: 600;
        }

        .action-delete {
            display: inline;
            margin: 0;
        }

        .delete-button {
            border: none;
            background: #dc2626;
            color: white;
            padding: 7px 12px;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 600;
        }

        .empty-state {
            text-align: center !important;
            padding: 30px !important;
            color: #64748b;
        }

        @media (max-width: 600px) {
            .admin-content {
                padding: 15px;
                margin: 20px auto;
            }

            .admin-actions {
                flex-direction: column;
            }

            .admin-actions a {
                text-align: center;
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
        <h1>Kelola Rak Buku</h1>
        <p>Daftar seluruh lokasi rak buku FINDLIB.</p>
    </div>

    <div class="admin-actions">
        <a href="tambah.php" class="add-button">
            + Tambah Rak
        </a>

        <a href="../dashboard.php" class="back-button">
            Kembali ke Dashboard
        </a>
    </div>

    <div class="table-container">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>No.</th>
                    <th>Kode Rak</th>
                    <th>Nama Rak</th>
                    <th>Aksi</th>
                </tr>
            </thead>

            <tbody>
                <?php if (empty($rak)): ?>
                    <tr>
                        <td colspan="4" class="empty-state">
                            Belum ada data rak.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($rak as $index => $item): ?>
                        <tr>
                            <td><?= $index + 1 ?></td>

                            <td>
                                <?= h($item['kode_rak']) ?>
                            </td>

                            <td>
                                <?= h($item['nama_rak']) ?>
                            </td>

                            <td>
                                <a
                                    href="edit.php?id=<?= (int) $item['id_rak'] ?>"
                                    class="action-edit"
                                >
                                    Edit
                                </a>

                                |

                                <form
                                    action="hapus.php"
                                    method="POST"
                                    class="action-delete"
                                    onsubmit="return confirm('Yakin ingin menghapus rak ini?');"
                                >
                                    <input
                                        type="hidden"
                                        name="id_rak"
                                        value="<?= (int) $item['id_rak'] ?>"
                                    >

                                    <input
                                        type="hidden"
                                        name="csrf_token"
                                        value="<?= h($_SESSION['csrf_token']) ?>"
                                    >

                                    <button
                                        type="submit"
                                        class="delete-button"
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
