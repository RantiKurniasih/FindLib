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
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - FINDLIB</title>
</head>
<body>

    <h1>Dashboard FINDLIB</h1>

    <p>
        Selamat datang,
        <?= e($admin['nama']) ?>!
    </p>

    <p>
        Anda berhasil login sebagai
        pengelola FINDLIB.
    </p>

    <p>
        Statistik buku akan ditambahkan
        pada tahap berikutnya.
    </p>

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