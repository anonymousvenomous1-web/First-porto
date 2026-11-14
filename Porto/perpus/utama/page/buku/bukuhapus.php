<?php
require_once __DIR__ . '/guard.php';
require_once __DIR__ . '/../../connection.php';

$id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT);
if ($id === false || $id === null || $id < 1) {
    http_response_code(400);
    exit('Data Buku tidak valid.');
}

$imgStmt = $conn->prepare('SELECT gambar FROM tbl_buku WHERE idbuku = ?');
$imgStmt->bind_param('i', $id);
$imgStmt->execute();
$imgData = $imgStmt->get_result()->fetch_assoc();
$gambarFile = $imgData['gambar'] ?? null;

$statement = $conn->prepare('DELETE FROM tbl_buku WHERE idbuku = ?');
$statement->bind_param('i', $id);
try {
    $statement->execute();
    if (!empty($gambarFile) && file_exists(__DIR__ . '/../../img/buku/' . $gambarFile)) {
        @unlink(__DIR__ . '/../../img/buku/' . $gambarFile);
    }
} catch (mysqli_sql_exception $exception) {
    error_log('Penghapusan buku gagal: ' . $exception->getMessage());
    http_response_code(409);
    exit('Buku tidak dapat dihapus karena masih digunakan dalam data perpustakaan.');
}

header('Location: ../../index.php?halaman=buku');
exit;
?>