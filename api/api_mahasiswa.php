<?php
header('Content-Type: application/json');
require_once 'koneksi.php'; // Mengambil koneksi SSL yang sudah kita buat

$sql = "SELECT * FROM mahasiswa";
$result = mysqli_query($conn, $sql);

$mahasiswa = [];

if ($result) {
    while($row = mysqli_fetch_assoc($result)) {
        $mahasiswa[] = $row;
    }
}

echo json_encode($mahasiswa);
mysqli_close($conn);
?>
