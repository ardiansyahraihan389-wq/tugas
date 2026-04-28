<?php
header('Content-Type: application/json');
include 'koneksi.php';

// Pastikan koneksi tidak error
if (!$conn) {
    echo json_encode(["error" => "Koneksi gagal"]);
    exit;
}

$sql = "SELECT * FROM mahasiswa";
$result = mysqli_query($conn, $sql);

$mahasiswa = array();

if ($result) {
    while($row = mysqli_fetch_assoc($result)) {
        $mahasiswa[] = $row;
    }
}

// Ini yang akan tampil di /api-data
echo json_encode($mahasiswa);
mysqli_close($conn);
?>
