<?php
$host = "gateway01.ap-southeast-1.prod.alicloud.tidbcloud.com";
$user = "2JyXBCpdbGQ96PV.root"; // Pastikan ini username terbaru dari tombol 'Connect'
$pass = "XC83dQLpkDBS5c3q"; 
$db   = "test";
$port = 4000;

$conn = mysqli_init();
mysqli_ssl_set($conn, NULL, NULL, NULL, NULL, NULL);

// Tambahkan error reporting agar terlihat di log jika gagal
if (!mysqli_real_connect($conn, $host, $user, $pass, $db, $port, NULL, MYSQLI_CLIENT_SSL)) {
    header('Content-Type: application/json');
    die(json_encode(["error" => "Koneksi gagal: " . mysqli_connect_error()]));
}
?>
