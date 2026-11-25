<?php
declare(strict_types=1);

require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../config/database.php';

wajibLogin();

// Mengambil seluruh data kategori
$stmt = $pdo->query(
    "SELECT id_kategori, nama_kategori
     FROM kategori
     ORDER BY nama_kategori"
);

$kategori = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Kategori - FINDLIB</title>
</head>
<body>

    <h1>Kelola Kategori</h1>
    <p>Daftar seluruh kategori buku FINDLIB.</p>

    <a href="tambah.php">+ Tambah Kategori</a>
    <a href="../dashboard.php">Kembali ke Dashboard</a>

    <br><br>

    <table border="1" cellpadding="10" cellspacing="0">
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
                    <td colspan="3">Belum ada kategori.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($kategori as $index => $item): ?>
                    <tr>
                        <td><?= $index + 1 ?></td>
                        <td>
                            <?= htmlspecialchars(
                                $item['nama_kategori'],
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </td>
                        <td>
                            <a href="edit.php?id=<?= (int) $item['id_kategori'] ?>">
                                Edit
                            </a>

                            |
                            
                            <form action="hapus.php" method="POST"
                                  style="display:inline;"
                                  onsubmit="return confirm('Yakin ingin menghapus kategori ini?');">

                                <input type="hidden" name="id_kategori"
                                       value="<?= (int) $item['id_kategori'] ?>">

                                <input type="hidden" name="csrf_token"
                                       value="<?= htmlspecialchars(
                                           $_SESSION['csrf_token'] ?? '',
                                           ENT_QUOTES,
                                           'UTF-8'
                                       ) ?>">

                                <button type="submit">Hapus</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

</body>
</html>
