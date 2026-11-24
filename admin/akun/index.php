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
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">
    <title>Kelola Akun - FINDLIB</title>
</head>
<body>

<h1>Kelola Akun FINDLIB</h1>

<p>
    Login sebagai: <?= e($admin['nama']) ?>
</p>

<p>
    <a href="../dashboard.php">Kembali ke Dashboard</a>
</p>

<h2>Daftar Akun Pengelola</h2>

<p>
    <a href="tambah.php">+ Tambah Akun</a>
</p>

<table border="1" cellpadding="8">
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
                <td colspan="6">
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
                        <?= $aktif ? 'Aktif' : 'Nonaktif' ?>
                    </td>
                    <td>
                        <a href="edit.php?id=<?= $id ?>">
                            Edit
                        </a>

                        <a href="password.php?id=<?= $id ?>">
                            Ganti Password
                        </a>

                        <?php if (!$akunSendiri): ?>
                            <form method="POST"
                                  action="status.php"
                                  style="display:inline">
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
                                <button type="submit">
                                    <?= $aktif
                                        ? 'Nonaktifkan'
                                        : 'Aktifkan' ?>
                                </button>
                            </form>
                        <?php else: ?>
                            <small>(Akun sendiri)</small>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

</body>
</html>