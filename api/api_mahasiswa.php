<?php
header('Content-Type: application/json');
// Menggunakan error reporting agar kita tahu jika ada masalah lain
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'koneksi.php';

if (!$conn) {
    echo json_encode(["error" => "Koneksi database gagal"]);
    exit;
}

// Ambil data dengan nama unik saja
$sql = "SELECT nama, nim, jurusan FROM mahasiswa GROUP BY nim ORDER BY id DESC";
$result = mysqli_query($conn, $sql);

$mahasiswa = array();

if ($result) {
    while($row = mysqli_fetch_assoc($result)) {
        $mahasiswa[] = $row;
    }
}

echo json_encode($mahasiswa, JSON_PRETTY_PRINT);
mysqli_close($conn);
?>
