<?php
require_once __DIR__ . '/guard.php';
require_once __DIR__ . '/../../connection.php';

$namapetugas = isset($_POST['namapetugas']) && is_string($_POST['namapetugas']) ? trim($_POST['namapetugas']) : '';
$hppetugas = isset($_POST['hppetugas']) && is_string($_POST['hppetugas']) ? trim($_POST['hppetugas']) : '';
$username = isset($_POST['username']) && is_string($_POST['username']) ? trim($_POST['username']) : '';
$password = isset($_POST['password']) && is_string($_POST['password']) ? $_POST['password'] : '';

if ($namapetugas === '' || $hppetugas === '') {
    http_response_code(400);
    exit('Lengkapi nama dan nomor HP Petugas.');
}
if (!auth_username_is_valid($username)) {
    http_response_code(400);
    exit('Username tidak valid atau sudah dicadangkan.');
}
if (!auth_password_is_valid($password)) {
    http_response_code(400);
    exit('Kata sandi harus memiliki minimal 8 karakter dan maksimal 72 byte UTF-8.');
}

$usernameCheck = $conn->prepare(
    'SELECT username FROM tbl_petugas WHERE username = ?
     UNION ALL
     SELECT username FROM tbl_siswa WHERE username = ?
     LIMIT 1'
);
$usernameCheck->bind_param('ss', $username, $username);
$usernameCheck->execute();
if ($usernameCheck->get_result()->fetch_assoc()) {
    http_response_code(409);
    exit('Username tersebut sudah digunakan. Pilih username lain.');
}

$passwordHash = password_hash($password, PASSWORD_DEFAULT);
if ($passwordHash === false) {
    error_log('Gagal membuat hash kata sandi untuk akun petugas.');
    http_response_code(500);
    exit('Akun Petugas belum dapat dibuat. Silakan coba lagi nanti.');
}

$statement = $conn->prepare(
    'INSERT INTO tbl_petugas (namapetugas, hppetugas, username, password_hash)
     VALUES (?, ?, ?, ?)'
);
$statement->bind_param('ssss', $namapetugas, $hppetugas, $username, $passwordHash);
$statement->execute();

header("location:../../index.php?halaman=petugas");
exit;
?>