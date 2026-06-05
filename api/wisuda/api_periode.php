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
    // GET - Tampilkan semua periode wisuda
    // =====================
    case 'GET':
        $sql = "SELECT * FROM tabel_periode_wisuda ORDER BY id_periode ASC";
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
            "message" => "Data periode wisuda berhasil diambil",
            "jumlah" => count($data),
            "data" => $data
        ]);
        break;

    // =====================
    // POST - Input periode wisuda baru
    // =====================
    case 'POST':
        $input = json_decode(file_get_contents("php://input"), true);

        // Validasi input
        if (empty($input['tahun_periode']) || empty($input['tanggal_pelaksanaan']) || !isset($input['kuota_maksimal'])) {
            http_response_code(400);
            echo json_encode([
                "status" => "error",
                "message" => "Data tidak lengkap. Field yang dibutuhkan: tahun_periode, tanggal_pelaksanaan, kuota_maksimal"
            ]);
            break;
        }

        $stmt = mysqli_prepare($conn, "INSERT INTO tabel_periode_wisuda (tahun_periode, tanggal_pelaksanaan, kuota_maksimal) VALUES (?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "isi", $input['tahun_periode'], $input['tanggal_pelaksanaan'], $input['kuota_maksimal']);

        if (mysqli_stmt_execute($stmt)) {
            $new_id = mysqli_insert_id($conn);
            http_response_code(201);
            echo json_encode([
                "status" => "success",
                "message" => "Periode wisuda berhasil ditambahkan",
                "id_periode" => $new_id
            ]);
        } else {
            http_response_code(500);
            echo json_encode(["status" => "error", "message" => "Gagal menambahkan data: " . mysqli_stmt_error($stmt)]);
        }
        mysqli_stmt_close($stmt);
        break;

    // =====================
    // PUT - Edit data periode berdasarkan id_periode
    // =====================
    case 'PUT':
        $input = json_decode(file_get_contents("php://input"), true);

        if (empty($input['id_periode'])) {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "id_periode wajib diisi untuk update"]);
            break;
        }

        if (empty($input['tahun_periode']) || empty($input['tanggal_pelaksanaan']) || !isset($input['kuota_maksimal'])) {
            http_response_code(400);
            echo json_encode([
                "status" => "error",
                "message" => "Data tidak lengkap. Field yang dibutuhkan: id_periode, tahun_periode, tanggal_pelaksanaan, kuota_maksimal"
            ]);
            break;
        }

        $stmt = mysqli_prepare($conn, "UPDATE tabel_periode_wisuda SET tahun_periode = ?, tanggal_pelaksanaan = ?, kuota_maksimal = ? WHERE id_periode = ?");
        mysqli_stmt_bind_param($stmt, "isii", $input['tahun_periode'], $input['tanggal_pelaksanaan'], $input['kuota_maksimal'], $input['id_periode']);

        if (mysqli_stmt_execute($stmt)) {
            $affected = mysqli_stmt_affected_rows($stmt);
            if ($affected > 0) {
                echo json_encode(["status" => "success", "message" => "Data periode wisuda berhasil diperbarui"]);
            } else {
                http_response_code(404);
                echo json_encode(["status" => "error", "message" => "Periode wisuda dengan id tersebut tidak ditemukan"]);
            }
        } else {
            http_response_code(500);
            echo json_encode(["status" => "error", "message" => "Gagal memperbarui data: " . mysqli_stmt_error($stmt)]);
        }
        mysqli_stmt_close($stmt);
        break;

    // =====================
    // DELETE - Hapus data periode berdasarkan id_periode
    // =====================
    case 'DELETE':
        $input = json_decode(file_get_contents("php://input"), true);

        if (empty($input['id_periode'])) {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "id_periode wajib diisi untuk menghapus data"]);
            break;
        }

        $stmt = mysqli_prepare($conn, "DELETE FROM tabel_periode_wisuda WHERE id_periode = ?");
        mysqli_stmt_bind_param($stmt, "i", $input['id_periode']);

        if (mysqli_stmt_execute($stmt)) {
            $affected = mysqli_stmt_affected_rows($stmt);
            if ($affected > 0) {
                echo json_encode(["status" => "success", "message" => "Periode wisuda berhasil dihapus"]);
            } else {
                http_response_code(404);
                echo json_encode(["status" => "error", "message" => "Periode wisuda dengan id tersebut tidak ditemukan"]);
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
