<?php
include 'koneksi.php';

// Data 3 mahasiswa yang diminta
$data_mahasiswa = [
    ['nama' => 'Muhammad Raihan', 'nim' => '240103001', 'jurusan' => 'Teknik Informatika'],
    ['nama' => 'Andini Putri', 'nim' => '240103002', 'jurusan' => 'Sistem Informasi'],
    ['nama' => 'Bagus Saputra', 'nim' => '240103003', 'jurusan' => 'Teknik Informatika']
];

$pesan = "";

if (isset($_POST['input_otomatis'])) {
    $berhasil = 0;
    foreach ($data_mahasiswa as $mhs) {
        $nama = $mhs['nama'];
        $nim = $mhs['nim'];
        $jurusan = $mhs['jurusan'];

        $sql = "INSERT INTO mahasiswa (nama, nim, jurusan) VALUES ('$nama', '$nim', '$jurusan')";
        if (mysqli_query($conn, $sql)) {
            $berhasil++;
        }
    }
    
    if ($berhasil > 0) {
        header("Location: /tampil");
        exit();
    } else {
        $pesan = "Gagal menambahkan data: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Data Otomatis</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #121212; color: white; padding: 50px; text-align: center; }
        .card { background: #1e1e1e; padding: 20px; border-radius: 10px; display: inline-block; border: 1px solid #333; }
        .btn { padding: 10px 20px; background: #00d4ff; border: none; cursor: pointer; font-weight: bold; border-radius: 5px; color: #121212; }
        ul { text-align: left; }
    </style>
</head>
<body>

    <h2>Konfirmasi Tambah 3 Mahasiswa</h2>
    <div class="card">
        <p>Data yang akan dimasukkan:</p>
        <ul>
            <?php foreach ($data_mahasiswa as $mhs): ?>
                <li><strong><?php echo $mhs['nama']; ?></strong> (<?php echo $mhs['nim']; ?>) - <?php echo $mhs['jurusan']; ?></li>
            <?php endforeach; ?>
        </ul>
        
        <form method="POST">
            <button type="submit" name="input_otomatis" class="btn">Klik untuk Masukkan Semua Data</button>
        </form>
        
        <?php if($pesan) echo "<p style='color:red'>$pesan</p>"; ?>
    </div>

</body>
</html>
