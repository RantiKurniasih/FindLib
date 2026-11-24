<?php
declare(strict_types=1);

require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../config/database.php';

wajibLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$token = $_POST['csrf_token'] ?? '';

if (!is_string($token) ||
    !hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
    http_response_code(403);
    exit('Permintaan tidak valid.');
}

$id = filter_var($_POST['id_rak'] ?? '', FILTER_VALIDATE_INT);

if ($id === false || $id === null || $id < 1) {
    header('Location: index.php');
    exit;
}

try {
    $stmt = $pdo->prepare(
        "DELETE FROM lokasi_rak
         WHERE id_rak = :id"
    );

    $stmt->execute([':id' => $id]);

    header('Location: index.php');
    exit;

} catch (PDOException $e) {
    error_log($e->getMessage());

    http_response_code(409);
    exit('Rak tidak dapat dihapus karena masih digunakan oleh data buku.');
}
