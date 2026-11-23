<?php
/**
 * FINDLIB - Pengujian Password Admin Lokal
 * Jalankan melalui terminal saja.
 */

declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
    exit("File ini hanya boleh dijalankan melalui terminal.\n");
}

try {
    $pdo = new PDO(
        "mysql:host=127.0.0.1;dbname=findlib;charset=utf8mb4",
        'root',
        '',
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );

    echo "Username admin: ";
    $username = trim((string) fgets(STDIN));

    $stmt = $pdo->prepare(
        "SELECT password_hash, status_aktif
         FROM pengguna
         WHERE username = ?"
    );

    $stmt->execute([$username]);
    $admin = $stmt->fetch();

    if (!$admin) {
        exit("Username atau password salah.\n");
    }

    // Membaca password tanpa menampilkannya
    $command = 'powershell -NoProfile -Command ' .
        '"$p=Read-Host -AsSecureString;' .
        '$b=[Runtime.InteropServices.Marshal]::SecureStringToBSTR($p);' .
        'try {' .
        '[Runtime.InteropServices.Marshal]::PtrToStringBSTR($b)' .
        '} finally {' .
        '[Runtime.InteropServices.Marshal]::ZeroFreeBSTR($b)' .
        '}"';

    echo "Password admin: ";
    $password = shell_exec($command);

    if ($password === null) {
        throw new RuntimeException(
            "Gagal membaca password secara aman."
        );
    }

    $password = rtrim($password, "\r\n");

    $valid = password_verify(
        $password,
        $admin['password_hash']
    );

    unset($password);

    if ($valid && (int) $admin['status_aktif'] === 1) {
        echo PHP_EOL . "Pengujian BERHASIL: password valid!" . PHP_EOL;
    } else {
        echo PHP_EOL . "Pengujian GAGAL: username, password, atau status akun tidak valid." . PHP_EOL;
    }

} catch (Throwable $e) {
    echo PHP_EOL . "Terjadi kesalahan: "
        . $e->getMessage() . PHP_EOL;

    exit(1);
}