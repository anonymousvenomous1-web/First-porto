<?php
require_once __DIR__ . '/guard.php';
require_once __DIR__ . '/../../connection.php';
date_default_timezone_set('Asia/Jakarta');

$fail = static function (string $message): never {
    $_SESSION['peminjaman_error'] = $message;
    header('Location: ../../index.php?halaman=peminjamantambah');
    exit;
};

$idbuku = filter_var($_POST['idbuku'] ?? null, FILTER_VALIDATE_INT);
$dueDateValue = isset($_POST['batas_peminjaman']) && is_string($_POST['batas_peminjaman'])
    ? $_POST['batas_peminjaman']
    : '';
$dueDate = DateTimeImmutable::createFromFormat('!Y-m-d', $dueDateValue);
$dateErrors = DateTimeImmutable::getLastErrors();

if ($idbuku === false || $idbuku === null || $idbuku < 1) {
    $fail('Pilih buku yang valid.');
}
if ($dueDate === false
    || ($dateErrors !== false && ($dateErrors['warning_count'] > 0 || $dateErrors['error_count'] > 0))
    || $dueDate->format('Y-m-d') !== $dueDateValue
    || $dueDateValue < date('Y-m-d')) {
    $fail('Pilih batas peminjaman hari ini atau setelahnya.');
}

$isStudent = $_SESSION['role'] === 'siswa';
$borrowedAt = date('Y-m-d H:i:s');
$idpetugas = null;
if ($isStudent) {
    $idsiswa = (int) $_SESSION['user_id'];
} else {
    $idpetugas = filter_var($_POST['idpetugas'] ?? null, FILTER_VALIDATE_INT);
    $idsiswa = filter_var($_POST['idsiswa'] ?? null, FILTER_VALIDATE_INT);
    if ($idpetugas === false || $idpetugas === null || $idpetugas < 1
        || $idsiswa === false || $idsiswa === null || $idsiswa < 1) {
        $fail('Pilih Petugas dan Siswa yang valid.');
    }
}

 $transactionStarted = false;
try {
    $conn->begin_transaction();
    $transactionStarted = true;

    $bookStatement = $conn->prepare('SELECT stok FROM tbl_buku WHERE idbuku = ? FOR UPDATE');
    $bookStatement->bind_param('i', $idbuku);
    $bookStatement->execute();
    $book = $bookStatement->get_result()->fetch_assoc();
    if (!$book) {
        $conn->rollback();
        $fail('Buku yang dipilih tidak ditemukan.');
    }
    if ((int) $book['stok'] < 1) {
        $conn->rollback();
        $fail('Stok buku sudah habis. Pilih buku lain.');
    }

    if ($isStudent) {
        $loanStatement = $conn->prepare(
            'INSERT INTO tbl_pinjam (idpetugas, idsiswa, waktupinjam, batas_peminjaman)
             VALUES (NULL, ?, ?, ?)'
        );
        $loanStatement->bind_param('iss', $idsiswa, $borrowedAt, $dueDateValue);
    } else {
        $loanStatement = $conn->prepare(
            'INSERT INTO tbl_pinjam (idpetugas, idsiswa, waktupinjam, batas_peminjaman)
             VALUES (?, ?, ?, ?)'
        );
        $loanStatement->bind_param('iiss', $idpetugas, $idsiswa, $borrowedAt, $dueDateValue);
    }
    $loanStatement->execute();
    $idpinjam = (int) $conn->insert_id;

    $detailStatement = $conn->prepare('INSERT INTO tbl_pinjamdetail (idpinjam, idbuku) VALUES (?, ?)');
    $detailStatement->bind_param('ii', $idpinjam, $idbuku);
    $detailStatement->execute();

    $stockStatement = $conn->prepare('UPDATE tbl_buku SET stok = stok - 1 WHERE idbuku = ? AND stok > 0');
    $stockStatement->bind_param('i', $idbuku);
    $stockStatement->execute();
    if ($stockStatement->affected_rows !== 1) {
        $conn->rollback();
        $fail('Stok buku berubah saat transaksi dibuat. Silakan coba lagi.');
    }

    $conn->commit();
    $transactionStarted = false;
} catch (mysqli_sql_exception $exception) {
    if ($transactionStarted) {
        $conn->rollback();
    }
    error_log('Pembuatan peminjaman gagal: ' . $exception->getMessage());
    $fail('Peminjaman belum dapat dibuat. Periksa pilihan data lalu coba lagi.');
}

header('Location: ../../index.php?halaman=peminjaman');
exit;
