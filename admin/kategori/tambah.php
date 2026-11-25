<?php
declare(strict_types=1);

require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../config/database.php';

wajibLogin();

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf_token'] ?? '';

    if (
        !is_string($token) ||
        !hash_equals($_SESSION['csrf_token'], $token)
    ) {
        $error = 'Permintaan tidak valid. Muat ulang halaman.';
    } else {
        $nama_kategori = trim($_POST['nama_kategori'] ?? '');

        if ($nama_kategori === '') {
            $error = 'Nama kategori wajib diisi.';
        } else {
            try {
                $stmt = $pdo->prepare(
                    "INSERT INTO kategori (nama_kategori)
                     VALUES (:nama_kategori)"
                );

                $stmt->execute([
                    ':nama_kategori' => $nama_kategori
                ]);

                header('Location: index.php');
                exit;
            } catch (PDOException $e) {
                if ($e->getCode() === '23000') {
                    $error = 'Nama kategori sudah tersedia.';
                } else {
                    error_log($e->getMessage());
                    $error = 'Terjadi kesalahan saat menyimpan kategori.';
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Kategori - FINDLIB</title>
</head>
<body>

    <h1>Tambah Kategori</h1>

    <?php if ($error !== ''): ?>
        <p style="color: red;">
            <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
        </p>
    <?php endif; ?>

    <form method="POST" action="">
        <input
            type="hidden"
            name="csrf_token"
            value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>"
        >

        <label for="nama_kategori">Nama Kategori</label>
        <br>

        <input
            type="text"
            id="nama_kategori"
            name="nama_kategori"
            maxlength="100"
            required
        >

        <br><br>

        <button type="submit">Simpan Kategori</button>
        <a href="index.php">Kembali</a>
    </form>

</body>
</html>
