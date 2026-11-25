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

try {
    $stmt = $pdo->prepare(
        'SELECT id_pengguna, nama, username
         FROM pengguna
         WHERE id_pengguna = :id
         LIMIT 1'
    );

    $stmt->execute(['id' => $id]);
    $account = $stmt->fetch(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    error_log('Ambil akun untuk ganti password error: ' . $e->getMessage());
    http_response_code(500);
    exit('Terjadi kesalahan sistem.');
}

if (!$account) {
    http_response_code(404);
    exit('Akun tidak ditemukan.');
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $csrfInput = $_POST['csrf_token'] ?? null;
    $passwordAdminInput = $_POST['password_admin'] ?? '';
    $passwordBaruInput = $_POST['password_baru'] ?? '';
    $konfirmasiInput = $_POST['konfirmasi'] ?? '';

    $csrf = is_string($csrfInput) ? $csrfInput : null;
    $passwordAdmin = is_string($passwordAdminInput) ? $passwordAdminInput : '';
    $passwordBaru = is_string($passwordBaruInput) ? $passwordBaruInput : '';
    $konfirmasi = is_string($konfirmasiInput) ? $konfirmasiInput : '';

    if (!verifyCsrf($csrf)) {
        $error = 'Token keamanan tidak valid. Silakan muat ulang halaman.';
    } elseif (
        $passwordAdmin === '' ||
        $passwordBaru === '' ||
        $konfirmasi === ''
    ) {
        $error = 'Semua kolom wajib diisi.';
    } elseif (strlen($passwordBaru) < 8) {
        $error = 'Password baru minimal 8 karakter.';
    } elseif (strlen($passwordBaru) > 1024) {
        $error = 'Password baru terlalu panjang.';
    } elseif ($passwordBaru !== $konfirmasi) {
        $error = 'Konfirmasi password tidak sesuai.';
    } else {
        try {
            $check = $pdo->prepare(
                'SELECT password_hash
                 FROM pengguna
                 WHERE id_pengguna = :id
                   AND status_aktif = 1
                 LIMIT 1'
            );

            $check->execute([
                'id' => (int) $admin['id_pengguna']
            ]);

            $hashAdmin = $check->fetchColumn();

            if (
                !is_string($hashAdmin) ||
                !password_verify($passwordAdmin, $hashAdmin)
            ) {
                $error = 'Password pengelola saat ini salah.';
            } else {
                $hashBaru = password_hash(
                    $passwordBaru,
                    PASSWORD_DEFAULT
                );

                if (!is_string($hashBaru)) {
                    $error = 'Gagal membuat password baru.';
                } else {
                    $update = $pdo->prepare(
                        'UPDATE pengguna
                         SET password_hash = :hash
                         WHERE id_pengguna = :id'
                    );

                    $update->execute([
                        'hash' => $hashBaru,
                        'id' => $id
                    ]);

                    $success = 'Password berhasil diperbarui.';
                }
            }

        } catch (PDOException $e) {
            error_log('Ganti password akun error: ' . $e->getMessage());
            $error = 'Terjadi kesalahan sistem. Gagal memperbarui password.';
        }
    }

    unset(
        $passwordAdmin,
        $passwordBaru,
        $konfirmasi,
        $hashBaru
    );
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Ganti Password - FINDLIB</title>
</head>
<body>

<h1>Ganti Password Akun</h1>

<p>
    Akun: <?= e($account['nama']) ?>
    (<?= e($account['username']) ?>)
</p>

<p>
    <a href="index.php">Kembali ke Daftar Akun</a>
</p>

<?php if ($error !== ''): ?>
    <p role="alert">
        <?= e($error) ?>
    </p>
<?php endif; ?>

<?php if ($success !== ''): ?>
    <p role="status">
        <?= e($success) ?>
    </p>
<?php endif; ?>

<form method="POST">

    <input
        type="hidden"
        name="csrf_token"
        value="<?= e(csrfToken()) ?>"
    >

    <p>
        <label for="password_admin">
            Password pengelola saat ini
        </label>

        <input
            type="password"
            id="password_admin"
            name="password_admin"
            autocomplete="current-password"
            required
        >
    </p>

    <p>
        <label for="password_baru">
            Password baru
        </label>

        <input
            type="password"
            id="password_baru"
            name="password_baru"
            minlength="8"
            maxlength="1024"
            autocomplete="new-password"
            required
        >
    </p>

    <p>
        <label for="konfirmasi">
            Konfirmasi password baru
        </label>

        <input
            type="password"
            id="konfirmasi"
            name="konfirmasi"
            minlength="8"
            maxlength="1024"
            autocomplete="new-password"
            required
        >
    </p>

    <button type="submit">
        Ganti Password
    </button>

</form>

</body>
</html>
