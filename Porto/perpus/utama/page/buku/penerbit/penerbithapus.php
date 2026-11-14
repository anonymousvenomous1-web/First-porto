<?php
require_once __DIR__ . '/guard.php';
require_once __DIR__ . '/../../../connection.php';

$id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT);
if ($id === false || $id === null || $id < 1) {
    http_response_code(400);
    exit('Data Penerbit tidak valid.');
}

$statement = $conn->prepare('DELETE FROM tbl_penerbit WHERE idpenerbit = ?');
$statement->bind_param('i', $id);
try {
    $statement->execute();
} catch (mysqli_sql_exception $exception) {
    error_log('Penghapusan penerbit gagal: ' . $exception->getMessage());
    http_response_code(409);
    exit('Penerbit tidak dapat dihapus karena masih digunakan oleh data Buku.');
}

header('Location: ../../../index.php?halaman=penerbit');
exit;
?>