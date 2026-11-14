<?php
require_once __DIR__ . '/guard.php';
require_once __DIR__ . '/../../connection.php'; 

if (isset($_POST['update'])) {
    $idbuku     = (int) ($_POST['idbuku'] ?? 0);
    $judul      = trim($_POST['judul'] ?? '');
    $stok       = (int) ($_POST['stok'] ?? 0);
    $kategori   = trim($_POST['kategori'] ?? '');
    $idpenulis  = (int) ($_POST['idpenulis'] ?? 0);
    $idpenerbit = (int) ($_POST['idpenerbit'] ?? 0);

    $currentStmt = $conn->prepare("SELECT gambar FROM tbl_buku WHERE idbuku = ?");
    $currentStmt->bind_param("i", $idbuku);
    $currentStmt->execute();
    $currentData = $currentStmt->get_result()->fetch_assoc();
    $oldGambar = $currentData['gambar'] ?? null;

    $newGambar = null;
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
                    $newGambar = $newFileName;

                    if (!empty($oldGambar) && file_exists($uploadDir . $oldGambar)) {
                        @unlink($uploadDir . $oldGambar);
                    }
                }
            }
        }
    }

    if ($newGambar !== null) {
        $stmt = $conn->prepare("UPDATE tbl_buku SET judul = ?, stok = ?, kategori = ?, idpenulis = ?, idpenerbit = ?, gambar = ? WHERE idbuku = ?");
        $stmt->bind_param("sisiisi", $judul, $stok, $kategori, $idpenulis, $idpenerbit, $newGambar, $idbuku);
    } else {
        $stmt = $conn->prepare("UPDATE tbl_buku SET judul = ?, stok = ?, kategori = ?, idpenulis = ?, idpenerbit = ? WHERE idbuku = ?");
        $stmt->bind_param("sisiii", $judul, $stok, $kategori, $idpenulis, $idpenerbit, $idbuku);
    }
    $stmt->execute();
    
    header("Location: ../../index.php?halaman=buku");
    exit;
}
?>