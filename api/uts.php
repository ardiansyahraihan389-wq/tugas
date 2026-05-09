<?php
header('Content-Type: application/json');
require_once 'koneksi.php'; 

$soal = isset($_GET['soal']) ? $_GET['soal'] : '1';
$tahun = isset($_GET['tahun']) ? $_GET['tahun'] : '2022';

$sql = "";
if ($soal == '1') {
    $sql = "SELECT * FROM buku";
} elseif ($soal == '2') {
    $sql = "SELECT * FROM peminjaman";
} elseif ($soal == '3') {
    $sql = "SELECT p.nama_peminjam, b.judul_buku, b.penulis, p.tanggal_pinjam FROM peminjaman p JOIN buku b ON p.buku_id = b.id";
} elseif ($soal == '4') {
    $sql = "SELECT p.nama_peminjam, b.judul_buku, b.penulis, p.tanggal_pinjam FROM peminjaman p JOIN buku b ON p.buku_id = b.id WHERE b.tahun_terbit = '$tahun'";
} elseif ($soal == '5') {
    $sql = "SELECT b.judul_buku, COUNT(*) as total_dipinjam FROM peminjaman p JOIN buku b ON p.buku_id = b.id WHERE p.tanggal_pinjam BETWEEN '2026-05-01' AND '2026-05-07' GROUP BY b.judul_buku";
} else {
    echo json_encode(["message" => "Soal tidak ditemukan"]);
    exit;
}

$result = mysqli_query($conn, $sql);
$data = [];
while ($row = mysqli_fetch_assoc($result)) {
    $data[] = $row;
}
echo json_encode($data, JSON_PRETTY_PRINT);
mysqli_close($conn);
