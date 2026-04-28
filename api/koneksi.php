<?php
$host = "gateway01.ap-southeast-1.prod.alicloud.tidbcloud.com";
$user = "2JyXBCpdbGQ96PV.root";
$pass = "Ukkz7GGZOO03gON9"; 
$db   = "sys"; 
$port = 4000;

$conn = mysqli_connect($host, $user, $pass, $db, $port);

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
?>
