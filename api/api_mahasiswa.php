<?php
header('Content-Type: application/json');
require_once 'koneksi.php';

// 1. Ambil SEMUA data tanpa filter SQL yang aneh-aneh (biar tidak error)
$sql = "SELECT * FROM mahasiswa ORDER BY id DESC";
$result = mysqli_query($conn, $sql);

$data_mentah = array();
if ($result) {
    while($row = mysqli_fetch_assoc($result)) {
        $data_mentah[] = $row;
    }
}

// 2. Filter duplikat pakai PHP (Sangat aman, tidak akan kena sql_mode error)
$hasil_akhir = array();
$nim_sudah_ada = array();

foreach ($data_mentah as $mhs) {
    // Jika NIM belum pernah tercatat, masukkan ke hasil
    if (!in_array($mhs['nim'], $nim_sudah_ada)) {
        $nim_sudah_ada[] = $mhs['nim'];
        $hasil_akhir[] = $mhs;
    }
}

// 3. Kirim data yang sudah bersih
echo json_encode($hasil_akhir, JSON_PRETTY_PRINT);
mysqli_close($conn);
?>
