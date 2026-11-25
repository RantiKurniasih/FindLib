<?php
declare(strict_types=1);

$pdo = require __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/auth.php';

$admin = requireAdmin($pdo);

$error = '';
$nama = '';
$username = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim((string) ($_POST['nama'] ?? ''));
    $username = trim((string) ($_POST['username'] ?? ''));
    $email = trim((string) ($_POST['email'] ?? ''));
    $password = (string) ($_POST['password'] ?? '');
    $konfirmasi = (string) ($_POST['konfirmasi'] ?? '');

    if (!verifyCsrf($_POST['csrf_token'] ?? null)) {
        $error = 'Token keamanan tidak valid.';
    } elseif (
        $nama === '' ||
        $username === '' ||
        $email === '' ||
        $password === ''
    ) {
        $error = 'Semua kolom wajib diisi.';
    } elseif (
        strlen($nama) > 100 ||
        strlen($username) > 50 ||
        strlen($email) > 150
    ) {
        $error = 'Data yang dimasukkan terlalu panjang.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Format email tidak valid.';
    } elseif (strlen($password) < 8) {
        $error = 'Password minimal 8 karakter.';
    } elseif ($password !== $konfirmasi) {
        $error = 'Konfirmasi password tidak sesuai.';
    } else {
        $stmt = $pdo->prepare(
            'SELECT COUNT(*) FROM pengguna
             WHERE username = :username
                OR email = :email'
        );

        $stmt->execute([
            'username' => $username,
            'email' => $email
        ]);

        if ((int) $stmt->fetchColumn() > 0) {
            $error = 'Username atau email sudah digunakan.';
        } else {
            $hash = password_hash(
                $password,
                PASSWORD_DEFAULT
            );

            try {
                $stmt = $pdo->prepare(
                    'INSERT INTO pengguna
                     (nama, username, email,
                      password_hash, status_aktif)
                     VALUES
                     (:nama, :username, :email,
                      :password_hash, 1)'
                );

                $stmt->execute([
                    'nama' => $nama,
                    'username' => $username,
                    'email' => $email,
                    'password_hash' => $hash
                ]);

                header('Location: index.php');
                exit;
            } catch (PDOException $e) {
                if ($e->getCode() === '23000') {
                    $error = 'Username atau email sudah digunakan.';
                } else {
                    error_log($e->getMessage());
                    $error = 'Gagal menyimpan akun.';
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
    <title>Tambah Akun - FINDLIB</title>
</head>
<body>

<h1>Tambah Akun Pengelola</h1>

<p><a href="index.php">Kembali ke Daftar Akun</a></p>

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
        <input
            type="text"
            id="nama"
            name="nama"
            value="<?= e($nama) ?>"
            maxlength="100"
            required
        >
    </p>

    <p>
        <label for="username">Username</label>
        <input
            type="text"
            id="username"
            name="username"
            value="<?= e($username) ?>"
            maxlength="50"
            required
        >
    </p>

    <p>
        <label for="email">Email</label>
        <input
            type="email"
            id="email"
            name="email"
            value="<?= e($email) ?>"
            maxlength="150"
            required
        >
    </p>

    <p>
        <label for="password">Password</label>
        <input
            type="password"
            id="password"
            name="password"
            minlength="8"
            autocomplete="new-password"
            required
        >
    </p>

    <p>
        <label for="konfirmasi">
            Konfirmasi password
        </label>
        <input
            type="password"
            id="konfirmasi"
            name="konfirmasi"
            autocomplete="new-password"
            required
        >
    </p>

    <button type="submit">Simpan Akun</button>
</form>

</body>
</html>