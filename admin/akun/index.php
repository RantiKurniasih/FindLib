<?php
declare(strict_types=1);

$pdo = require __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/auth.php';

$admin = requireAdmin($pdo);

$stmt = $pdo->query(
    'SELECT id_pengguna, nama, username,
            email, status_aktif
     FROM pengguna
     ORDER BY id_pengguna ASC'
);

$accounts = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Akun - FINDLIB</title>
    <link rel="stylesheet" href="/FindLib/assets/css/style.css">

    <style>
        .admin-content {
            max-width: 1150px;
            margin: 40px auto;
            padding: 20px;
        }

        .admin-heading {
            margin-bottom: 25px;
        }

        .admin-heading h1 {
            color: #1e293b;
            margin-bottom: 8px;
        }

        .admin-heading p {
            color: #64748b;
        }

        .admin-info {
            background: #eff6ff;
            color: #1e40af;
            padding: 13px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
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
            padding: 14px;
            text-align: left;
            border-bottom: 1px solid #e2e8f0;
            white-space: nowrap;
        }

        .admin-table th {
            background: #eff6ff;
            color: #1e3a8a;
        }

        .admin-table tbody tr:hover {
            background: #f8fafc;
        }

        .status-badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
        }

        .status-active {
            background: #dcfce7;
            color: #166534;
        }

        .status-inactive {
            background: #fee2e2;
            color: #991b1b;
        }

        .action-links {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .action-edit,
        .action-password {
            text-decoration: none;
            font-weight: 600;
            color: #2563eb;
        }

        .action-password {
            color: #7c3aed;
        }

        .status-form {
            display: inline;
            margin: 0;
        }

        .status-button {
            border: none;
            padding: 7px 10px;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 600;
            color: white;
            background: #dc2626;
        }

        .status-button.activate {
            background: #16a34a;
        }

        .self-account {
            color: #64748b;
            font-size: 12px;
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
        <h1>Kelola Akun FINDLIB</h1>
        <p>Daftar seluruh akun pengelola sistem FINDLIB.</p>
    </div>

    <div class="admin-info">
        Login sebagai: <strong><?= e($admin['nama']) ?></strong>
    </div>

    <div class="admin-actions">
        <a href="tambah.php" class="add-button">
            + Tambah Akun
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
                    <th>Nama</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>

            <tbody>
                <?php if (empty($accounts)): ?>
                    <tr>
                        <td colspan="6" class="empty-state">
                            Belum ada akun pengelola.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($accounts as $i => $account): ?>
                        <?php
                        $id = (int) $account['id_pengguna'];
                        $aktif = (int) $account['status_aktif'] === 1;
                        $akunSendiri =
                            $id === (int) $admin['id_pengguna'];
                        ?>

                        <tr>
                            <td><?= $i + 1 ?></td>

                            <td><?= e($account['nama']) ?></td>

                            <td><?= e($account['username']) ?></td>

                            <td><?= e($account['email']) ?></td>

                            <td>
                                <?php if ($aktif): ?>
                                    <span class="status-badge status-active">
                                        Aktif
                                    </span>
                                <?php else: ?>
                                    <span class="status-badge status-inactive">
                                        Nonaktif
                                    </span>
                                <?php endif; ?>
                            </td>

                            <td>
                                <div class="action-links">
                                    <a
                                        href="edit.php?id=<?= $id ?>"
                                        class="action-edit"
                                    >
                                        Edit
                                    </a>

                                    <a
                                        href="password.php?id=<?= $id ?>"
                                        class="action-password"
                                    >
                                        Ganti Password
                                    </a>

                                    <?php if (!$akunSendiri): ?>
                                        <form
                                            method="POST"
                                            action="status.php"
                                            class="status-form"
                                        >
                                            <input
                                                type="hidden"
                                                name="csrf_token"
                                                value="<?= e(csrfToken()) ?>"
                                            >

                                            <input
                                                type="hidden"
                                                name="id"
                                                value="<?= $id ?>"
                                            >

                                            <button
                                                type="submit"
                                                class="status-button <?= $aktif ? '' : 'activate' ?>"
                                            >
                                                <?= $aktif
                                                    ? 'Nonaktifkan'
                                                    : 'Aktifkan' ?>
                                            </button>
                                        </form>
                                    <?php else: ?>
                                        <small class="self-account">
                                            (Akun sendiri)
                                        </small>
                                    <?php endif; ?>
                                </div>
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
