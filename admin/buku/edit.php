<?php
declare(strict_types=1);

require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../config/database.php';

wajibLogin();

// Validasi ID buku
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    http_response_code(400);
    exit('ID buku tidak valid.');
}

// Ambil data buku
$stmt = $pdo->prepare(
    "SELECT * FROM buku WHERE id_buku = :id"
);
$stmt->execute(['id' => $id]);
$buku = $stmt->fetch();

if (!$buku) {
    http_response_code(404);
    exit('Data buku tidak ditemukan.');
}

// Ambil kategori dan lokasi rak
$kategori = $pdo->query(
    "SELECT id_kategori, nama_kategori
     FROM kategori ORDER BY nama_kategori"
)->fetchAll();

$rak = $pdo->query(
    "SELECT id_rak, kode_rak, nama_rak
     FROM lokasi_rak ORDER BY kode_rak"
)->fetchAll();

// Token CSRF
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
        $no_katalog = trim($_POST['no_katalog'] ?? '');
        $judul = trim($_POST['judul'] ?? '');
        $penulis = trim($_POST['penulis'] ?? '');
        $penerbit = trim($_POST['penerbit'] ?? '');

        $tahun = filter_var(
            $_POST['tahun_terbit'] ?? '',
            FILTER_VALIDATE_INT
        );

        $id_kategori = filter_var(
            $_POST['id_kategori'] ?? '',
            FILTER_VALIDATE_INT
        );

        $id_rak = filter_var(
            $_POST['id_rak'] ?? '',
            FILTER_VALIDATE_INT
        );

        $stok = filter_var(
            $_POST['jumlah_stok'] ?? '',
            FILTER_VALIDATE_INT
        );

        if (
            $no_katalog === '' ||
            $judul === '' ||
            $penulis === '' ||
            $penerbit === '' ||
            $tahun === false ||
            $tahun < 1000 ||
            $tahun > (int) date('Y') ||
            $id_kategori === false ||
            $id_rak === false ||
            $stok === false ||
            $stok < 0
        ) {
            $error = 'Periksa kembali data yang dimasukkan.';
        } else {
            try {
                $stmt = $pdo->prepare(
                    "UPDATE buku SET
                        no_katalog = :no_katalog,
                        judul = :judul,
                        penulis = :penulis,
                        penerbit = :penerbit,
                        tahun_terbit = :tahun,
                        id_kategori = :kategori,
                        id_rak = :rak,
                        jumlah_stok = :stok
                     WHERE id_buku = :id"
                );

                $stmt->execute([
                    'no_katalog' => $no_katalog,
                    'judul' => $judul,
                    'penulis' => $penulis,
                    'penerbit' => $penerbit,
                    'tahun' => $tahun,
                    'kategori' => $id_kategori,
                    'rak' => $id_rak,
                    'stok' => $stok,
                    'id' => $id
                ]);

                header(
                    'Location: /FindLib/admin/buku/index.php?status=diubah'
                );
                exit;

            } catch (PDOException $e) {
                error_log('Edit buku gagal: ' . $e->getMessage());
                $error = 'Gagal memperbarui buku. Periksa nomor katalog dan data lainnya.';
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
    <title>Edit Buku - FINDLIB</title>
</head>
<body>

<h2>Edit Buku</h2>

<?php if ($error !== ''): ?>
    <p style="color:red;">
        <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
    </p>
<?php endif; ?>

<form method="POST">

    <input type="hidden" name="csrf_token"
        value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>">

    <p>
        <label>Nomor Katalog</label><br>
        <input type="text" name="no_katalog" required
            value="<?= htmlspecialchars($buku['no_katalog'], ENT_QUOTES, 'UTF-8') ?>">
    </p>

    <p>
        <label>Judul Buku</label><br>
        <input type="text" name="judul" required
            value="<?= htmlspecialchars($buku['judul'], ENT_QUOTES, 'UTF-8') ?>">
    </p>

    <p>
        <label>Penulis</label><br>
        <input type="text" name="penulis" required
            value="<?= htmlspecialchars($buku['penulis'], ENT_QUOTES, 'UTF-8') ?>">
    </p>

    <p>
        <label>Penerbit</label><br>
        <input type="text" name="penerbit" required
            value="<?= htmlspecialchars($buku['penerbit'], ENT_QUOTES, 'UTF-8') ?>">
    </p>

    <p>
        <label>Tahun Terbit</label><br>
        <input type="number" name="tahun_terbit"
            min="1000" max="<?= date('Y') ?>" required
            value="<?= (int) $buku['tahun_terbit'] ?>">
    </p>

    <p>
        <label>Kategori</label><br>
        <select name="id_kategori" required>
            <?php foreach ($kategori as $item): ?>
                <option value="<?= (int) $item['id_kategori'] ?>"
                    <?= (int) $buku['id_kategori'] === (int) $item['id_kategori'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($item['nama_kategori'], ENT_QUOTES, 'UTF-8') ?>
                </option>
            <?php endforeach; ?>
        </select>
    </p>

    <p>
        <label>Lokasi Rak</label><br>
        <select name="id_rak" required>
            <?php foreach ($rak as $item): ?>
                <option value="<?= (int) $item['id_rak'] ?>"
                    <?= (int) $buku['id_rak'] === (int) $item['id_rak'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($item['kode_rak'] . ' - ' . $item['nama_rak'], ENT_QUOTES, 'UTF-8') ?>
                </option>
            <?php endforeach; ?>
        </select>
    </p>

    <p>
        <label>Jumlah Stok</label><br>
        <input type="number" name="jumlah_stok"
            min="0" required
            value="<?= (int) $buku['jumlah_stok'] ?>">
    </p>

    <button type="submit">Simpan Perubahan</button>
    <a href="index.php">Kembali</a>

</form>

</body>
</html>
