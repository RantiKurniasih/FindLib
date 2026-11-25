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
    <link rel="stylesheet" href="../../assets/css/admin.css">
</head>
<body class="akun-page">

    <main class="akun-container">
        <h1>Kelola Akun FINDLIB</h1>

        <p class="akun-login">
            Login sebagai: <?= e($admin['nama']) ?>
        </p>

        <div class="akun-actions">
            <a href="../dashboard.php" class="akun-back">
                Kembali ke Dashboard
            </a>

            <a href="tambah.php" class="akun-add">
                + Tambah Akun
            </a>
        </div>

        <h2>Daftar Akun Pengelola</h2>

        <div class="akun-table-wrapper">
            <table class="akun-table">
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
                            <td colspan="6" class="akun-empty">
                                Belum ada akun pengelola.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($accounts as $i => $account): ?>
                            <?php
                            $id = (int) $account['id_pengguna'];
                            $aktif = (int) $account['status_aktif'] === 1;
                            $akunSendiri = $id === (int) $admin['id_pengguna'];
                            ?>
                            <tr>
                                <td><?= $i + 1 ?></td>
                                <td><?= e($account['nama']) ?></td>
                                <td><?= e($account['username']) ?></td>
                                <td><?= e($account['email']) ?></td>
                                <td>
                                    <span class="<?= $aktif ? 'akun-status-aktif' : 'akun-status-nonaktif' ?>">
                                        <?= $aktif ? 'Aktif' : 'Nonaktif' ?>
                                    </span>
                                </td>
                                <td class="akun-action-cell">
                                    <a href="edit.php?id=<?= $id ?>" class="akun-edit">
                                        Edit
                                    </a>

                                    <a href="password.php?id=<?= $id ?>" class="akun-password">
                                        Ganti Password
                                    </a>

                                    <?php if (!$akunSendiri): ?>
                                        <form method="POST" action="status.php">
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
                                            <button type="submit" class="<?= $aktif ? 'akun-disable' : 'akun-enable' ?>">
                                                <?= $aktif ? 'Nonaktifkan' : 'Aktifkan' ?>
                                            </button>
                                        </form>
                                    <?php else: ?>
                                        <small>Akun sendiri</small>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>

</body>
</html>
