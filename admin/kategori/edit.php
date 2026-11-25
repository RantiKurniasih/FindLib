<?php
declare(strict_types=1);

require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../config/database.php';

wajibLogin();

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    header('Location: index.php');
    exit;
}

// Mengambil data kategori
$stmt = $pdo->prepare(
    "SELECT id_kategori, nama_kategori
     FROM kategori
     WHERE id_kategori = :id"
);
$stmt->execute([':id' => $id]);

$kategori = $stmt->fetch();

if (!$kategori) {
    header('Location: index.php');
    exit;
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
                $update = $pdo->prepare(
                    "UPDATE kategori
                     SET nama_kategori = :nama
                     WHERE id_kategori = :id"
                );

                $update->execute([
                    ':nama' => $nama_kategori,
                    ':id' => $id
                ]);

                header('Location: index.php');
                exit;
            } catch (PDOException $e) {
                if ($e->getCode() === '23000') {
                    $error = 'Nama kategori sudah digunakan.';
                } else {
                    error_log($e->getMessage());
                    $error = 'Terjadi kesalahan saat mengubah kategori.';
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
    <title>Edit Kategori - FINDLIB</title>
</head>
<body>

    <h1>Edit Kategori</h1>

    <?php if ($error !== ''): ?>
        <p style="color: red;">
            <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
        </p>
    <?php endif; ?>

    <form method="POST">
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
            value="<?= htmlspecialchars(
                $kategori['nama_kategori'],
                ENT_QUOTES,
                'UTF-8'
            ) ?>"
            required
        >

        <br><br>

        <button type="submit">Simpan Perubahan</button>
        <a href="index.php">Kembali</a>
    </form>

</body>
</html>
