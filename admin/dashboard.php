<?php
declare(strict_types=1);

$pdo = require __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';

$admin = requireAdmin($pdo);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - FINDLIB</title>
</head>
<body>

    <h1>Dashboard Admin FINDLIB</h1>

    <p>
        Selamat datang,
        <strong><?= e($admin['nama']) ?></strong>!
    </p>

    <p>Anda berhasil login sebagai pengelola FINDLIB.</p>

    <hr>

    <h2>Menu Pengelolaan</h2>

    <ul>
        <li><a href="buku/index.php">Kelola Data Buku</a></li>
        <li><a href="kategori/index.php">Kelola Kategori</a></li>
        <li><a href="rak/index.php">Kelola Rak Buku</a></li>
        <li><a href="akun/index.php">Kelola Akun Admin</a></li>
    </ul>

    <hr>

    <form method="POST" action="logout.php">
        <input
            type="hidden"
            name="csrf_token"
            value="<?= e(csrfToken()) ?>"
        >

        <button type="submit">Logout</button>
    </form>

</body>
</html>
