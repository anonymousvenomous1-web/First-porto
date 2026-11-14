<?php

$dbHost = getenv('DB_HOST') ?: 'localhost';
$dbUsername = getenv('DB_USERNAME') ?: 'serverd4_travel';
$dbPassword = getenv('DB_PASSWORD') ?: 'k0kotOreaux^67284';
$dbName = getenv('DB_NAME') ?: 'serverd4_travel';
$dbPort = (int) (getenv('DB_PORT') ?: 3306);

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$conn = mysqli_connect($dbHost, $dbUsername, $dbPassword, $dbName, $dbPort);
if ($conn === false) {
    throw new RuntimeException('Koneksi database gagal. Periksa konfigurasi DB hosting.');
}
mysqli_set_charset($conn, 'utf8mb4');
