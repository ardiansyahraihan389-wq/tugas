<?php
header('Content-Type: application/json');
require_once 'koneksi.php';

// Cara paling aman: Ambil semua data dulu, nanti kita rapikan
$sql = "SELECT * FROM mahasiswa ORDER BY id DESC";
$result = mysqli_query($conn, $sql);

$semua_data = array();
if ($result) {
    while($row = mysqli_fetch_assoc($result)) {
        $semua_data[] = $row;
    }
}

// Menghilangkan duplikat berdasarkan NIM menggunakan PHP (Lebih stabil daripada SQL)
$mahasiswa_unik = array();
$nim_tercatat = array();

foreach ($semua_data as $mhs) {
    if (!in_array($mhs['nim'], $nim_tercatat)) {
        $nim_tercatat[] = $mhs['nim'];
        $mahasiswa_unik[] = $mhs;
    }
}

// Kirim data yang sudah bersih dari duplikat
echo json_encode($mahasiswa_unik, JSON_PRETTY_PRINT);
mysqli_close($conn);
?>
