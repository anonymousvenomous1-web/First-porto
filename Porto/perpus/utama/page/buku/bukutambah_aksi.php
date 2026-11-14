<?php
require_once __DIR__ . '/guard.php';
require_once __DIR__ . '/../../connection.php';

if (isset($_POST['simpan'])) {
    $judul      = trim($_POST['judul'] ?? '');
    $stok       = (int) ($_POST['stok'] ?? 0);
    $kategori   = trim($_POST['kategori'] ?? '');
    $idpenulis  = (int) ($_POST['idpenulis'] ?? 0);
    $idpenerbit = (int) ($_POST['idpenerbit'] ?? 0);

    $gambar = null;
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $fileTmp = $_FILES['gambar']['tmp_name'];
        $fileName = $_FILES['gambar']['name'];
        $fileSize = $_FILES['gambar']['size'];
        $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        if (in_array($ext, $allowedExtensions, true) && $fileSize <= 2 * 1024 * 1024) {
            $imageInfo = @getimagesize($fileTmp);
            if ($imageInfo !== false) {
                $newFileName = 'buku_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
                $uploadDir = __DIR__ . '/../../img/buku/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
                if (move_uploaded_file($fileTmp, $uploadDir . $newFileName)) {
                    $gambar = $newFileName;
                }
            }
        }
    }

    $stmt = $conn->prepare("INSERT INTO tbl_buku (judul, stok, kategori, idpenulis, idpenerbit, gambar) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sisiis", $judul, $stok, $kategori, $idpenulis, $idpenerbit, $gambar);
    $stmt->execute();
}

header("Location: ../../index.php?halaman=buku");
exit;