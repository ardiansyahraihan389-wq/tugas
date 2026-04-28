<?php
header('Content-Type: application/json');
require_once 'koneksi.php';

// Menambahkan GROUP BY agar nama yang sama hanya muncul satu kali
$sql = "SELECT * FROM mahasiswa GROUP BY nama, nim ORDER BY id DESC";
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
