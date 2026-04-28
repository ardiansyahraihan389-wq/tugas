<?php
// 1. Set header agar browser tahu ini data JSON
header('Content-Type: application/json');

// 2. Panggil koneksi
require_once 'koneksi.php';

// 3. Cek apakah koneksi berhasil
if (!$conn) {
    echo json_encode(["error" => "Koneksi database gagal: " . mysqli_connect_error()]);
    exit;
}

// 4. Ambil data (pakai query yang paling sederhana agar tidak error)
$sql = "SELECT * FROM mahasiswa ORDER BY id DESC";
$result = mysqli_query($conn, $sql);

if (!$result) {
    echo json_encode(["error" => "Query gagal: " . mysqli_error($conn)]);
    exit;
}

// 5. Masukkan data ke array sambil menyaring duplikat NIM
$mahasiswa_unik = array();
$nim_tercatat = array();

while($row = mysqli_fetch_assoc($result)) {
    $nim = $row['nim'];
    // Jika NIM belum pernah masuk ke daftar, masukkan sekarang
    if (!in_array($nim, $nim_tercatat)) {
        $nim_tercatat[] = $nim;
        $mahasiswa_unik[] = $row;
    }
}

// 6. Tampilkan hasil
echo json_encode($mahasiswa_unik, JSON_PRETTY_PRINT);
mysqli_close($conn);
