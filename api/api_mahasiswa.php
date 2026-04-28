<?php
header('Content-Type: application/json');
require_once 'koneksi.php';

// Menampilkan data terbaru di atas
$sql = "SELECT * FROM mahasiswa ORDER BY id DESC";
$result = mysqli_query($conn, $sql);

$mahasiswa = array();

if ($result) {
    while($row = mysqli_fetch_assoc($result)) {
        $mahasiswa[] = $row;
    }
}

// Menggunakan JSON_PRETTY_PRINT agar tampilan rapi di browser
echo json_encode($mahasiswa, JSON_PRETTY_PRINT);
mysqli_close($conn);
?>
