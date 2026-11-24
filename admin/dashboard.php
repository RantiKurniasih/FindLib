<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';

// Wajib login untuk mengakses dashboard
wajibLogin();

$nama = htmlspecialchars(
    $_SESSION['nama'],
    ENT_QUOTES,
    'UTF-8'
);

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
        <strong><?= $nama ?></strong>!
    </p>

    <p>Anda berhasil login sebagai admin.</p>

    <form action="/FindLib/logout.php" method="POST">
        <input
            type="hidden"
            name="csrf_token"
            value="<?= htmlspecialchars(
                $_SESSION['csrf_token'],
                ENT_QUOTES,
                'UTF-8'
            ) ?>"
        >

        <button type="submit">Logout</button>
    </form>

</body>
</html>
