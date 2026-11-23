<?php
/**
 * FINDLIB - Setup Akun Admin Awal
 *
 * Jalankan hanya melalui terminal:
 * php database/setup-admin.php
 *
 * Password tidak disimpan dalam kode.
 */

declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
    exit("Script hanya boleh dijalankan melalui terminal.");
}

function input(string $pesan): string
{
    echo $pesan;
    return trim((string) fgets(STDIN));
}

function inputPassword(): string
{
    echo "Password admin: ";

    if (PHP_OS_FAMILY === 'Windows') {
        $password = shell_exec(
            'powershell -NoProfile -Command ' .
            '"$p=Read-Host -AsSecureString;' .
            '$b=[Runtime.InteropServices.Marshal]::SecureStringToBSTR($p);' .
            'try {' .
            '[Runtime.InteropServices.Marshal]::PtrToStringBSTR($b)' .
            '} finally {' .
            '[Runtime.InteropServices.Marshal]::ZeroFreeBSTR($b)' .
            '}"'
        );

        if ($password === null) {
            throw new RuntimeException(
                "Gagal membaca password secara aman."
            );
        }

        return rtrim($password, "\r\n");
    }

    throw new RuntimeException(
        "Input password tersembunyi belum tersedia pada OS ini."
    );
}

try {
    $nama = input("Nama lengkap admin: ");
    $username = input("Username admin: ");
    $email = input("Email admin: ");
    $password = inputPassword();

    if (
        $nama === '' ||
        $username === '' ||
        !filter_var($email, FILTER_VALIDATE_EMAIL) ||
        strlen($password) < 8
    ) {
        throw new RuntimeException(
            "Data tidak valid. Password minimal 8 karakter."
        );
    }

    $hash = password_hash($password, PASSWORD_DEFAULT);
    unset($password);

    $config = require __DIR__ . '/config.local.php';

    $pdo = new PDO(
        "mysql:host={$config['host']};dbname={$config['dbname']};charset={$config['charset']}",
        $config['username'],
        $config['password'],
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );

    $stmt = $pdo->prepare(
        "INSERT INTO pengguna
         (nama, username, email, password_hash, status_aktif)
         VALUES (?, ?, ?, ?, 1)"
    );

    $stmt->execute([
        $nama,
        $username,
        $email,
        $hash
    ]);

    echo PHP_EOL;
    echo "Akun admin berhasil dibuat!" . PHP_EOL;

} catch (Throwable $e) {
    echo PHP_EOL;
    echo "Gagal membuat admin: "
        . $e->getMessage() . PHP_EOL;

    exit(1);
}