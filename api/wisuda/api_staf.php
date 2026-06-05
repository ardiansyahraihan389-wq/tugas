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
    // GET - Tampilkan semua data staf verifikator
    // =====================
    case 'GET':
        $sql = "SELECT * FROM tabel_staf_verifikator ORDER BY id_staf ASC";
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
            "message" => "Data staf verifikator berhasil diambil",
            "jumlah" => count($data),
            "data" => $data
        ]);
        break;

    // =====================
    // POST - Input data staf baru
    // =====================
    case 'POST':
        $input = json_decode(file_get_contents("php://input"), true);

        // Validasi input
        if (empty($input['nama_staf']) || empty($input['divisi'])) {
            http_response_code(400);
            echo json_encode([
                "status" => "error",
                "message" => "Data tidak lengkap. Field yang dibutuhkan: nama_staf, divisi"
            ]);
            break;
        }

        $stmt = mysqli_prepare($conn, "INSERT INTO tabel_staf_verifikator (nama_staf, divisi) VALUES (?, ?)");
        mysqli_stmt_bind_param($stmt, "ss", $input['nama_staf'], $input['divisi']);

        if (mysqli_stmt_execute($stmt)) {
            $new_id = mysqli_insert_id($conn);
            http_response_code(201);
            echo json_encode([
                "status" => "success",
                "message" => "Data staf verifikator berhasil ditambahkan",
                "id_staf" => $new_id
            ]);
        } else {
            http_response_code(500);
            echo json_encode(["status" => "error", "message" => "Gagal menambahkan data: " . mysqli_stmt_error($stmt)]);
        }
        mysqli_stmt_close($stmt);
        break;

    // =====================
    // PUT - Edit data staf berdasarkan id_staf
    // =====================
    case 'PUT':
        $input = json_decode(file_get_contents("php://input"), true);

        if (empty($input['id_staf'])) {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "id_staf wajib diisi untuk update"]);
            break;
        }

        if (empty($input['nama_staf']) || empty($input['divisi'])) {
            http_response_code(400);
            echo json_encode([
                "status" => "error",
                "message" => "Data tidak lengkap. Field yang dibutuhkan: id_staf, nama_staf, divisi"
            ]);
            break;
        }

        $stmt = mysqli_prepare($conn, "UPDATE tabel_staf_verifikator SET nama_staf = ?, divisi = ? WHERE id_staf = ?");
        mysqli_stmt_bind_param($stmt, "ssi", $input['nama_staf'], $input['divisi'], $input['id_staf']);

        if (mysqli_stmt_execute($stmt)) {
            $affected = mysqli_stmt_affected_rows($stmt);
            if ($affected > 0) {
                echo json_encode(["status" => "success", "message" => "Data staf verifikator berhasil diperbarui"]);
            } else {
                http_response_code(404);
                echo json_encode(["status" => "error", "message" => "Staf verifikator dengan id tersebut tidak ditemukan"]);
            }
        } else {
            http_response_code(500);
            echo json_encode(["status" => "error", "message" => "Gagal memperbarui data: " . mysqli_stmt_error($stmt)]);
        }
        mysqli_stmt_close($stmt);
        break;

    // =====================
    // DELETE - Hapus data staf berdasarkan id_staf
    // =====================
    case 'DELETE':
        $input = json_decode(file_get_contents("php://input"), true);

        if (empty($input['id_staf'])) {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "id_staf wajib diisi untuk menghapus data"]);
            break;
        }

        $stmt = mysqli_prepare($conn, "DELETE FROM tabel_staf_verifikator WHERE id_staf = ?");
        mysqli_stmt_bind_param($stmt, "i", $input['id_staf']);

        if (mysqli_stmt_execute($stmt)) {
            $affected = mysqli_stmt_affected_rows($stmt);
            if ($affected > 0) {
                echo json_encode(["status" => "success", "message" => "Data staf verifikator berhasil dihapus"]);
            } else {
                http_response_code(404);
                echo json_encode(["status" => "error", "message" => "Staf verifikator dengan id tersebut tidak ditemukan"]);
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
