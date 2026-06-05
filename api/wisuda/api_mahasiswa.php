<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

// Handle preflight request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once __DIR__ . '/koneksi.php';

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    // =====================
    // GET - Tampilkan semua data mahasiswa
    // =====================
    case 'GET':
        $sql = "SELECT * FROM tabel_mahasiswa ORDER BY id_mahasiswa ASC";
        $result = mysqli_query($conn, $sql);

        if (!$result) {
            http_response_code(500);
            echo json_encode(["status" => "error", "message" => "Query gagal: " . mysqli_error($conn)]);
            break;
        }

        $data = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $data[] = $row;
        }

        echo json_encode([
            "status" => "success",
            "message" => "Data mahasiswa berhasil diambil",
            "jumlah" => count($data),
            "data" => $data
        ]);
        break;

    // =====================
    // POST - Input data mahasiswa baru
    // =====================
    case 'POST':
        $input = json_decode(file_get_contents("php://input"), true);

        // Validasi input
        if (empty($input['nim']) || empty($input['nama_mahasiswa']) || empty($input['prodi']) || !isset($input['ipk'])) {
            http_response_code(400);
            echo json_encode([
                "status" => "error",
                "message" => "Data tidak lengkap. Field yang dibutuhkan: nim, nama_mahasiswa, prodi, ipk"
            ]);
            break;
        }

        $stmt = mysqli_prepare($conn, "INSERT INTO tabel_mahasiswa (nim, nama_mahasiswa, prodi, ipk) VALUES (?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "sssd", $input['nim'], $input['nama_mahasiswa'], $input['prodi'], $input['ipk']);

        if (mysqli_stmt_execute($stmt)) {
            $new_id = mysqli_insert_id($conn);
            http_response_code(201);
            echo json_encode([
                "status" => "success",
                "message" => "Data mahasiswa berhasil ditambahkan",
                "id_mahasiswa" => $new_id
            ]);
        } else {
            http_response_code(500);
            echo json_encode(["status" => "error", "message" => "Gagal menambahkan data: " . mysqli_stmt_error($stmt)]);
        }
        mysqli_stmt_close($stmt);
        break;

    // =====================
    // PUT - Edit data mahasiswa berdasarkan id_mahasiswa
    // =====================
    case 'PUT':
        $input = json_decode(file_get_contents("php://input"), true);

        if (empty($input['id_mahasiswa'])) {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "id_mahasiswa wajib diisi untuk update"]);
            break;
        }

        // Validasi input
        if (empty($input['nim']) || empty($input['nama_mahasiswa']) || empty($input['prodi']) || !isset($input['ipk'])) {
            http_response_code(400);
            echo json_encode([
                "status" => "error",
                "message" => "Data tidak lengkap. Field yang dibutuhkan: id_mahasiswa, nim, nama_mahasiswa, prodi, ipk"
            ]);
            break;
        }

        $stmt = mysqli_prepare($conn, "UPDATE tabel_mahasiswa SET nim = ?, nama_mahasiswa = ?, prodi = ?, ipk = ? WHERE id_mahasiswa = ?");
        mysqli_stmt_bind_param($stmt, "sssdi", $input['nim'], $input['nama_mahasiswa'], $input['prodi'], $input['ipk'], $input['id_mahasiswa']);

        if (mysqli_stmt_execute($stmt)) {
            $affected = mysqli_stmt_affected_rows($stmt);
            if ($affected > 0) {
                echo json_encode(["status" => "success", "message" => "Data mahasiswa berhasil diperbarui"]);
            } else {
                http_response_code(404);
                echo json_encode(["status" => "error", "message" => "Data mahasiswa dengan id tersebut tidak ditemukan"]);
            }
        } else {
            http_response_code(500);
            echo json_encode(["status" => "error", "message" => "Gagal memperbarui data: " . mysqli_stmt_error($stmt)]);
        }
        mysqli_stmt_close($stmt);
        break;

    // =====================
    // DELETE - Hapus data mahasiswa berdasarkan id_mahasiswa
    // =====================
    case 'DELETE':
        $input = json_decode(file_get_contents("php://input"), true);

        if (empty($input['id_mahasiswa'])) {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "id_mahasiswa wajib diisi untuk menghapus data"]);
            break;
        }

        $stmt = mysqli_prepare($conn, "DELETE FROM tabel_mahasiswa WHERE id_mahasiswa = ?");
        mysqli_stmt_bind_param($stmt, "i", $input['id_mahasiswa']);

        if (mysqli_stmt_execute($stmt)) {
            $affected = mysqli_stmt_affected_rows($stmt);
            if ($affected > 0) {
                echo json_encode(["status" => "success", "message" => "Data mahasiswa berhasil dihapus"]);
            } else {
                http_response_code(404);
                echo json_encode(["status" => "error", "message" => "Data mahasiswa dengan id tersebut tidak ditemukan"]);
            }
        } else {
            http_response_code(500);
            echo json_encode(["status" => "error", "message" => "Gagal menghapus data: " . mysqli_stmt_error($stmt)]);
        }
        mysqli_stmt_close($stmt);
        break;

    default:
        http_response_code(405);
        echo json_encode(["status" => "error", "message" => "Method tidak diizinkan. Gunakan GET, POST, PUT, atau DELETE"]);
        break;
}

mysqli_close($conn);
?>
