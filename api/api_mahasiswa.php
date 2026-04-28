<?php
header('Content-Type: application/json');
require_once 'koneksi.php';

// Menggunakan DISTINCT agar data yang sama persis tidak muncul dua kali
$sql = "SELECT DISTINCT nama, nim, jurusan FROM mahasiswa ORDER BY nama ASC";
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
