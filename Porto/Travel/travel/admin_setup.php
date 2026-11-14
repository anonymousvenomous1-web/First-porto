<?php

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

$password = getenv('TRAVEL_ADMIN_PASSWORD');
$passwordCharacters = is_string($password) ? preg_match_all('/./us', $password, $matches) : false;
if ($passwordCharacters === false || $passwordCharacters < 6 || strlen($password) > 72) {
    fwrite(STDERR, "Set TRAVEL_ADMIN_PASSWORD (minimal 6 karakter, maksimal 72 byte) sebelum membuat akun admin.\n");
    exit(1);
}

require_once __DIR__ . '/connection.php';

$username = 'kiki';
$lookup = $conn->prepare('SELECT idadmin FROM tbl_admin WHERE username = ? LIMIT 1');
$lookup->bind_param('s', $username);
$lookup->execute();
if ($lookup->get_result()->fetch_assoc()) {
    fwrite(STDOUT, "Akun admin kiki sudah ada; tidak ada perubahan.\n");
    exit(0);
}

$passwordHash = password_hash($password, PASSWORD_DEFAULT);
$insert = $conn->prepare('INSERT INTO tbl_admin (username, password_hash) VALUES (?, ?)');
$insert->bind_param('ss', $username, $passwordHash);
$insert->execute();
fwrite(STDOUT, "Akun admin kiki berhasil dibuat.\n");
