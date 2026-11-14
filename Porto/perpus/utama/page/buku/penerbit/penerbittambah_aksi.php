<?php
require_once __DIR__ . '/guard.php';
include "../../../connection.php"; 

if(isset($_POST['simpan'])){
    $namapenerbit = $_POST['namapenerbit'];
    $kota         = $_POST['kota'];

    mysqli_query($conn, "INSERT INTO tbl_penerbit (namapenerbit, kota) VALUES ('$namapenerbit', '$kota')");
}
header("location:../../../index.php?halaman=penerbit");
?>