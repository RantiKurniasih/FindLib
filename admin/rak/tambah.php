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

    if (!is_string($token) ||
        !hash_equals($_SESSION['csrf_token'], $token)) {
        $error = 'Permintaan tidak valid. Muat ulang halaman.';
    } else {
        $kode_rak = trim($_POST['kode_rak'] ?? '');
        $nama_rak = trim($_POST['nama_rak'] ?? '');

        if ($kode_rak === '' || $nama_rak === '') {
            $error = 'Semua kolom wajib diisi.';
        } else {
            try {
                $stmt = $pdo->prepare(
                    "INSERT INTO lokasi_rak (kode_rak, nama_rak)
                     VALUES (:kode, :nama)"
                );

                $stmt->execute([
                    ':kode' => $kode_rak,
                    ':nama' => $nama_rak
                ]);

                header('Location: index.php');
                exit;
            } catch (PDOException $e) {
                if ($e->getCode() === '23000') {
                    $error = 'Kode rak sudah digunakan.';
                } else {
                    error_log($e->getMessage());
                    $error = 'Terjadi kesalahan saat menyimpan rak.';
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
    <title>Tambah Rak - FINDLIB</title>
</head>
<body>

    <h1>Tambah Rak Buku</h1>

    <?php if ($error !== ''): ?>
        <p style="color:red;">
            <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
        </p>
    <?php endif; ?>

    <form method="POST">
        <input type="hidden" name="csrf_token"
               value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>">

        <label for="kode_rak">Kode Rak</label><br>
        <input type="text" id="kode_rak" name="kode_rak"
               maxlength="50" required>

        <br><br>

        <label for="nama_rak">Nama Rak</label><br>
        <input type="text" id="nama_rak" name="nama_rak"
               maxlength="100" required>

        <br><br>

        <button type="submit">Simpan Rak</button>
        <a href="index.php">Kembali</a>
    </form>

</body>
</html>
