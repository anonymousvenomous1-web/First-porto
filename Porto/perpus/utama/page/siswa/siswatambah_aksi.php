<?php
require_once __DIR__ . '/guard.php';
require_once __DIR__ . '/../../connection.php';

$postText = static function (string $key): string {
    return isset($_POST[$key]) && is_string($_POST[$key]) ? trim($_POST[$key]) : '';
};
$nis = $postText('nis');
$nisn = $postText('nisn');
$namasiswa = $postText('namasiswa');
$alamat = $postText('alamat');
$kelas = $postText('kelas');
$hpsiswa = $postText('hpsiswa');
$username = $postText('username');
$password = isset($_POST['password']) && is_string($_POST['password']) ? $_POST['password'] : '';

if ($nis === '' || $nisn === '' || $namasiswa === '' || $alamat === '' || $kelas === '' || $hpsiswa === '') {
    http_response_code(400);
    exit('Lengkapi seluruh data Siswa.');
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
    error_log('Gagal membuat hash kata sandi untuk akun siswa.');
    http_response_code(500);
    exit('Akun Siswa belum dapat dibuat. Silakan coba lagi nanti.');
}

$foto = null;
if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
    $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    $fileTmp = $_FILES['foto']['tmp_name'];
    $fileName = $_FILES['foto']['name'];
    $fileSize = $_FILES['foto']['size'];
    $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

    if (in_array($ext, $allowedExtensions, true) && $fileSize <= 2 * 1024 * 1024) {
        $imageInfo = @getimagesize($fileTmp);
        if ($imageInfo !== false) {
            $newFileName = 'siswa_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
            $uploadDir = __DIR__ . '/../../img/siswa/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            if (move_uploaded_file($fileTmp, $uploadDir . $newFileName)) {
                $foto = $newFileName;
            }
        }
    }
}

$statement = $conn->prepare(
    'INSERT INTO tbl_siswa (nis, nisn, namasiswa, alamat, kelas, hpsiswa, username, password_hash, foto)
     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)'
);
$statement->bind_param('sssssssss', $nis, $nisn, $namasiswa, $alamat, $kelas, $hpsiswa, $username, $passwordHash, $foto);
$statement->execute();

header('Location: ../../index.php?halaman=siswa');
exit;
?>