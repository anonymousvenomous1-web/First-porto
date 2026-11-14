<?php
require_once __DIR__ . '/guard.php';
require_once __DIR__ . '/../../connection.php';

$idpinjam = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT);
if ($idpinjam === false || $idpinjam === null || $idpinjam < 1) {
    http_response_code(400);
    exit('Transaksi peminjaman tidak valid.');
}

 $transactionStarted = false;
try {
    $conn->begin_transaction();
    $transactionStarted = true;

    $loanStatement = $conn->prepare('SELECT idpinjam FROM tbl_pinjam WHERE idpinjam = ? FOR UPDATE');
    $loanStatement->bind_param('i', $idpinjam);
    $loanStatement->execute();
    if (!$loanStatement->get_result()->fetch_assoc()) {
        $conn->rollback();
        $transactionStarted = false;
        http_response_code(404);
        exit('Transaksi peminjaman tidak ditemukan.');
    }

    $detailStatement = $conn->prepare('SELECT idbuku FROM tbl_pinjamdetail WHERE idpinjam = ? FOR UPDATE');
    $detailStatement->bind_param('i', $idpinjam);
    $detailStatement->execute();
    $details = $detailStatement->get_result();
    $stockStatement = $conn->prepare('UPDATE tbl_buku SET stok = stok + 1 WHERE idbuku = ?');
    while ($detail = $details->fetch_assoc()) {
        $idbuku = (int) $detail['idbuku'];
        $stockStatement->bind_param('i', $idbuku);
        $stockStatement->execute();
    }

    $deleteDetails = $conn->prepare('DELETE FROM tbl_pinjamdetail WHERE idpinjam = ?');
    $deleteDetails->bind_param('i', $idpinjam);
    $deleteDetails->execute();

    $deleteLoan = $conn->prepare('DELETE FROM tbl_pinjam WHERE idpinjam = ?');
    $deleteLoan->bind_param('i', $idpinjam);
    $deleteLoan->execute();
    $conn->commit();
    $transactionStarted = false;
} catch (mysqli_sql_exception $exception) {
    if ($transactionStarted) {
        $conn->rollback();
    }
    error_log('Penghapusan peminjaman gagal: ' . $exception->getMessage());
    http_response_code(500);
    exit('Transaksi peminjaman belum dapat dihapus. Silakan coba lagi nanti.');
}

header('Location: ../../index.php?halaman=peminjaman');
exit;
