<?php
require_once __DIR__ . '/guard.php';
include '../../../connection.php'; 

if (isset($_POST['update'])) {
    $idpenerbit   = $_POST['idpenerbit'];
    $namapenerbit = $_POST['namapenerbit'];
    $kota         = $_POST['kota'];

    mysqli_query($conn, "UPDATE tbl_penerbit SET namapenerbit = '$namapenerbit', kota = '$kota' WHERE idpenerbit = '$idpenerbit'");
    
    header("Location: ../../../index.php?halaman=penerbit");
}
?>