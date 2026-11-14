<?php
require_once __DIR__ . '/guard.php';
include "../../../connection.php"; 

if(isset($_POST['simpan'])){
    $namapenulis = $_POST['namapenulis'];
    $hppenulis   = $_POST['hppenulis'];
    
    mysqli_query($conn, "INSERT INTO tbl_penulis (namapenulis, hppenulis) VALUES ('$namapenulis', '$hppenulis')");
}
header("location:../../../index.php?halaman=penulis");
?>