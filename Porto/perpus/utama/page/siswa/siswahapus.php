<?php
require_once __DIR__ . '/guard.php';
require_once __DIR__ . '/../../connection.php';

$id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT);
if ($id === false || $id === null || $id < 1) {
    http_response_code(400);
    exit('Data Siswa tidak valid.');
}

$imgStmt = $conn->prepare('SELECT foto FROM tbl_siswa WHERE idsiswa = ?');
$imgStmt->bind_param('i', $id);
$imgStmt->execute();
$imgData = $imgStmt->get_result()->fetch_assoc();
$fotoFile = $imgData['foto'] ?? null;

$statement = $conn->prepare('DELETE FROM tbl_siswa WHERE idsiswa = ?');
$statement->bind_param('i', $id);
try {
    $statement->execute();
    if (!empty($fotoFile) && file_exists(__DIR__ . '/../../img/siswa/' . $fotoFile)) {
        @unlink(__DIR__ . '/../../img/siswa/' . $fotoFile);
    }
} catch (mysqli_sql_exception $exception) {
    error_log('Penghapusan siswa gagal: ' . $exception->getMessage());
    http_response_code(409);
    exit('Siswa tidak dapat dihapus karena masih memiliki riwayat peminjaman.');
}

header('Location: ../../index.php?halaman=siswa');
exit;
?>