<?php
header('Content-Type: application/json');
require_once 'koneksi.php';

// Menggunakan DISTINCT agar lebih aman dan tidak menyebabkan error SQL
$sql = "SELECT DISTINCT nama, nim, jurusan FROM mahasiswa ORDER BY id DESC";
$result = mysqli_query($conn, $sql);

$mahasiswa = array();

if ($result) {
    while($row = mysqli_fetch_assoc($result)) {
        $mahasiswa[] = $row;
    }
}

// Tambahkan proteksi agar jika database error, JSON tetap valid
if (empty($mahasiswa) && mysqli_error($conn)) {
    http_response_code(500);
    echo json_encode(["error" => mysqli_error($conn)]);
} else {
    echo json_encode($mahasiswa, JSON_PRETTY_PRINT);
}

mysqli_close($conn);
?>
