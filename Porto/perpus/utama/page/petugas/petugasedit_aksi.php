<?php
require_once __DIR__ . '/guard.php';
require_once __DIR__ . '/../../connection.php';

$idpetugas = filter_var($_POST['idpetugas'] ?? null, FILTER_VALIDATE_INT);
$namapetugas = isset($_POST['namapetugas']) && is_string($_POST['namapetugas']) ? trim($_POST['namapetugas']) : '';
$hppetugas = isset($_POST['hppetugas']) && is_string($_POST['hppetugas']) ? trim($_POST['hppetugas']) : '';
$username = isset($_POST['username']) && is_string($_POST['username']) ? trim($_POST['username']) : '';
$password = isset($_POST['password']) && is_string($_POST['password']) ? $_POST['password'] : '';

if ($idpetugas === false || $idpetugas === null || $namapetugas === '' || $hppetugas === '') {
    http_response_code(400);
    exit('Data Petugas tidak lengkap atau tidak valid.');
}

$currentStatement = $conn->prepare('SELECT username, password_hash FROM tbl_petugas WHERE idpetugas = ?');
$currentStatement->bind_param('i', $idpetugas);
$currentStatement->execute();
$current = $currentStatement->get_result()->fetch_assoc();
if (!$current) {
    http_response_code(404);
    exit('Data Petugas tidak ditemukan.');
}

if ($username !== '' && !auth_username_is_valid($username)) {
    http_response_code(400);
    exit('Username tidak valid atau sudah dicadangkan.');
}
if ($username === '' && $password !== '') {
    http_response_code(400);
    exit('Isi username jika ingin mengatur kata sandi akun.');
}
if ($username !== '' && $password !== '' && !auth_password_is_valid($password)) {
    http_response_code(400);
    exit('Kata sandi harus memiliki minimal 8 karakter dan maksimal 72 byte UTF-8.');
}

$passwordHash = null;
if ($username !== '') {
    $usernameCheck = $conn->prepare(
        'SELECT username FROM tbl_petugas WHERE username = ? AND idpetugas <> ?
         UNION ALL
         SELECT username FROM tbl_siswa WHERE username = ?
         LIMIT 1'
    );
    $usernameCheck->bind_param('sis', $username, $idpetugas, $username);
    $usernameCheck->execute();
    if ($usernameCheck->get_result()->fetch_assoc()) {
        http_response_code(409);
        exit('Username tersebut sudah digunakan. Pilih username lain.');
    }

    if ($password === '') {
        if (empty($current['username']) || empty($current['password_hash'])) {
            http_response_code(400);
            exit('Masukkan kata sandi untuk mengaktifkan akun ini.');
        }
        $passwordHash = $current['password_hash'];
    } else {
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        if ($passwordHash === false) {
            error_log('Gagal membuat hash kata sandi saat memperbarui akun petugas.');
            http_response_code(500);
            exit('Akun Petugas belum dapat diperbarui. Silakan coba lagi nanti.');
        }
    }
}

$statement = $conn->prepare(
    'UPDATE tbl_petugas
     SET namapetugas = ?, hppetugas = ?, username = ?, password_hash = ?
     WHERE idpetugas = ?'
);
$statement->bind_param('ssssi', $namapetugas, $hppetugas, $username, $passwordHash, $idpetugas);
$statement->execute();

header('Location: ../../index.php?halaman=petugas');
exit;
?>