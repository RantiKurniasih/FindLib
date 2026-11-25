<?php
declare(strict_types=1);

$pdo = require __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/auth.php';

$admin = requireAdmin($pdo);

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    http_response_code(400);
    exit('ID akun tidak valid.');
}

$stmt = $pdo->prepare(
    'SELECT id_pengguna, nama, username, email
     FROM pengguna
     WHERE id_pengguna = :id'
);
$stmt->execute(['id' => $id]);

$account = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$account) {
    http_response_code(404);
    exit('Akun tidak ditemukan.');
}

$error = '';
$nama = $account['nama'];
$username = $account['username'];
$email = $account['email'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim((string) ($_POST['nama'] ?? ''));
    $username = trim((string) ($_POST['username'] ?? ''));
    $email = trim((string) ($_POST['email'] ?? ''));

    if (!verifyCsrf($_POST['csrf_token'] ?? null)) {
        $error = 'Token keamanan tidak valid.';
    } elseif ($nama === '' || $username === '' || $email === '') {
        $error = 'Semua kolom wajib diisi.';
    } elseif (
        mb_strlen($nama) > 100 ||
        mb_strlen($username) > 50 ||
        strlen($email) > 150
    ) {
        $error = 'Data terlalu panjang.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Format email tidak valid.';
    } else {
        $check = $pdo->prepare(
            'SELECT COUNT(*)
             FROM pengguna
             WHERE (username = :username OR email = :email)
               AND id_pengguna <> :id'
        );

        $check->execute([
            'username' => $username,
            'email' => $email,
            'id' => $id
        ]);

        if ((int) $check->fetchColumn() > 0) {
            $error = 'Username atau email sudah digunakan.';
        } else {
            try {
                $update = $pdo->prepare(
                    'UPDATE pengguna
                     SET nama = :nama,
                         username = :username,
                         email = :email
                     WHERE id_pengguna = :id'
                );

                $update->execute([
                    'nama' => $nama,
                    'username' => $username,
                    'email' => $email,
                    'id' => $id
                ]);

                header('Location: index.php');
                exit;
            } catch (PDOException $e) {
                if ($e->getCode() === '23000') {
                    $error = 'Username atau email sudah digunakan.';
                } else {
                    error_log($e->getMessage());
                    $error = 'Gagal memperbarui akun.';
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
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">
    <title>Edit Akun - FINDLIB</title>
</head>
<body>

<h1>Edit Akun Pengelola</h1>

<p>
    <a href="index.php">Kembali ke Daftar Akun</a>
</p>

<?php if ($error !== ''): ?>
    <p role="alert"><?= e($error) ?></p>
<?php endif; ?>

<form method="POST">
    <input
        type="hidden"
        name="csrf_token"
        value="<?= e(csrfToken()) ?>"
    >

    <p>
        <label for="nama">Nama lengkap</label>
        <input type="text"
               id="nama"
               name="nama"
               value="<?= e($nama) ?>"
               maxlength="100"
               required>
    </p>

    <p>
        <label for="username">Username</label>
        <input type="text"
               id="username"
               name="username"
               value="<?= e($username) ?>"
               maxlength="50"
               required>
    </p>

    <p>
        <label for="email">Email</label>
        <input type="email"
               id="email"
               name="email"
               value="<?= e($email) ?>"
               maxlength="150"
               required>
    </p>

    <button type="submit">Simpan Perubahan</button>
</form>

</body>
</html>