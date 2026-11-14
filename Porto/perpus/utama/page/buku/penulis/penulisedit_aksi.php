<?php
require_once __DIR__ . '/guard.php';
include '../../../connection.php'; 

if (isset($_POST['update'])) {
    $idpenulis   = $_POST['idpenulis'];
    $namapenulis = $_POST['namapenulis'];
    $hppenulis   = $_POST['hppenulis'];
    
    mysqli_query($conn, "UPDATE tbl_penulis SET namapenulis = '$namapenulis', hppenulis = '$hppenulis' WHERE idpenulis = '$idpenulis'");
    header("Location: ../../../index.php?halaman=penulis");
}
?>