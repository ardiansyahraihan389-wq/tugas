<?php
header('Content-Type: application/json');
include 'koneksi.php';

try {
    // Mengambil data mahasiswa
    $sql = "SELECT * FROM mahasiswa ORDER BY id DESC";
    $result = $conn->query($sql);

    $mahasiswa = array();

    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $mahasiswa[] = $row;
        }
    }

    // Mengirimkan data dalam format JSON
    echo json_encode($mahasiswa);

} catch (Exception $e) {
    echo json_encode(["error" => $e->getMessage()]);
}

$conn->close();
?>
