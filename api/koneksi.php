<?php
// Pastikan tidak ada spasi di awal atau akhir string
$host = "gateway01.ap-southeast-1.prod.alicloud.tidbcloud.com";
$user = "2JyXBCpdbGQ96PV.root"; 
$pass = "XC83dQLpkDBS5c3q"; 
$db   = "test";
$port = 4000;

$conn = mysqli_init();

// Pengaturan SSL wajib untuk TiDB Cloud
mysqli_ssl_set($conn, NULL, NULL, NULL, NULL, NULL);

// Mencoba koneksi
if (!mysqli_real_connect($conn, $host, $user, $pass, $db, $port, NULL, MYSQLI_CLIENT_SSL)) {
    header('Content-Type: application/json');
    die(json_encode([
        "status" => "error",
        "message" => mysqli_connect_error()
    ]));
}
// Jika berhasil, tidak akan menampilkan apa-apa (aman untuk include)
?>
