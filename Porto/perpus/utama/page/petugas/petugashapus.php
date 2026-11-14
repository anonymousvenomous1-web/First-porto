<?php
require_once __DIR__ . '/guard.php';
require_once __DIR__ . '/../../connection.php';

$id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT);
if ($id === false || $id === null || $id < 1) {
    http_response_code(400);
    exit('Data Petugas tidak valid.');
}

$statement = $conn->prepare('DELETE FROM tbl_petugas WHERE idpetugas = ?');
$statement->bind_param('i', $id);
try {
    $statement->execute();
} catch (mysqli_sql_exception $exception) {
    error_log('Penghapusan petugas gagal: ' . $exception->getMessage());
    http_response_code(409);
    exit('Petugas tidak dapat dihapus karena masih tercatat pada transaksi.');
}

header('Location: ../../index.php?halaman=petugas');
exit;
?>