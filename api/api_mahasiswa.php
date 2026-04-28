<?php
header('Content-Type: application/json');
include 'koneksi.php';

$sql = "SELECT * FROM mahasiswa ORDER BY id DESC";
$result = mysqli_query($conn, $sql);

$mahasiswa = array();

if ($result && mysqli_num_rows($result) > 0) {
    while($row = mysqli_fetch_assoc($result)) {
        $mahasiswa[] = $row;
    }
}

echo json_encode($mahasiswa);
mysqli_close($conn);
?>
