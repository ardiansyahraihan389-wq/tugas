<?php
header('Content-Type: application/json');
// Perhatikan baris di bawah ini: ditambah 'api/' karena koneksi.php ada di dalam folder
require_once 'api/koneksi.php'; 

$soal = isset($_GET['soal']) ? $_GET['soal'] : '';
$tahun = isset($_GET['tahun']) ? $_GET['tahun'] : '';

switch ($soal) {
    case '1': 
        $sql = "SELECT * FROM buku";
        break;
    case '2': 
        $sql = "SELECT * FROM peminjaman";
        break;
    case '3': 
        $sql = "SELECT p.nama_peminjam, b.judul_buku, b.penulis, p.tanggal_pinjam 
                FROM peminjaman p 
                JOIN buku b ON p.buku_id = b.id";
        break;
    case '4': 
        $sql = "SELECT p.nama_peminjam, b.judul_buku, b.penulis, p.tanggal_pinjam 
                FROM peminjaman p 
                JOIN buku b ON p.buku_id = b.id 
                WHERE b.tahun_terbit = '$tahun'";
        break;
    case '5': 
        $sql = "SELECT b.judul_buku, COUNT(*) as total_dipinjam 
                FROM peminjaman p 
                JOIN buku b ON p.buku_id = b.id 
                WHERE p.tanggal_pinjam BETWEEN '2026-05-01' AND '2026-05-07'
                GROUP BY b.judul_buku";
        break;
    default:
        echo json_encode(["status" => "error", "message" => "Pilih soal 1-5 di URL. Contoh: ?soal=1"]);
        exit;
}

$result = mysqli_query($conn, $sql);
$data = array();

if ($result) {
    while($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }
    echo json_encode($data, JSON_PRETTY_PRINT);
} else {
    echo json_encode(["status" => "error", "message" => mysqli_error($conn)]);
}

mysqli_close($conn);
?>
