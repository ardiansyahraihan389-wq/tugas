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
    // GET - Ambil semua data pendaftaran dengan JOIN
    // Menampilkan informasi lengkap (nama, bukan ID mentah)
    // =====================
    case 'GET':
        $sql = "SELECT 
                    rp.id_pendaftaran,
                    m.id_mahasiswa,
                    m.nim,
                    m.nama_mahasiswa,
                    m.prodi,
                    m.ipk,
                    pw.id_periode,
                    pw.tahun_periode,
                    pw.tanggal_pelaksanaan,
                    pw.kuota_maksimal,
                    sv.id_staf,
                    sv.nama_staf,
                    sv.divisi,
                    rp.nomor_kursi,
                    rp.status_verifikasi
                FROM tabel_rekap_pendaftaran rp
                INNER JOIN tabel_mahasiswa m ON rp.id_mahasiswa = m.id_mahasiswa
                INNER JOIN tabel_periode_wisuda pw ON rp.id_periode = pw.id_periode
                LEFT JOIN tabel_staf_verifikator sv ON rp.id_staf = sv.id_staf
                ORDER BY rp.id_pendaftaran ASC";

        $result = mysqli_query($conn, $sql);

        if (!$result) {
            http_response_code(500);
            echo json_encode(["status" => "error", "message" => "Query gagal: " . mysqli_error($conn)]);
            break;
        }

        $data = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $data[] = [
                "id_pendaftaran" => $row['id_pendaftaran'],
                "mahasiswa" => [
                    "id_mahasiswa" => $row['id_mahasiswa'],
                    "nim" => $row['nim'],
                    "nama_mahasiswa" => $row['nama_mahasiswa'],
                    "prodi" => $row['prodi'],
                    "ipk" => $row['ipk']
                ],
                "periode_wisuda" => [
                    "id_periode" => $row['id_periode'],
                    "tahun_periode" => $row['tahun_periode'],
                    "tanggal_pelaksanaan" => $row['tanggal_pelaksanaan'],
                    "kuota_maksimal" => $row['kuota_maksimal']
                ],
                "staf_verifikator" => $row['id_staf'] ? [
                    "id_staf" => $row['id_staf'],
                    "nama_staf" => $row['nama_staf'],
                    "divisi" => $row['divisi']
                ] : null,
                "nomor_kursi" => $row['nomor_kursi'],
                "status_verifikasi" => $row['status_verifikasi']
            ];
        }

        echo json_encode([
            "status" => "success",
            "message" => "Data pendaftaran wisuda berhasil diambil",
            "jumlah" => count($data),
            "data" => $data
        ]);
        break;

    // =====================
    // POST - Mahasiswa mendaftar wisuda baru
    // Input: id_mahasiswa, id_periode
    // id_staf & nomor_kursi otomatis NULL, status_verifikasi default 'Pending'
    // =====================
    case 'POST':
        $input = json_decode(file_get_contents("php://input"), true);

        // Validasi input
        if (empty($input['id_mahasiswa']) || empty($input['id_periode'])) {
            http_response_code(400);
            echo json_encode([
                "status" => "error",
                "message" => "Data tidak lengkap. Field yang dibutuhkan: id_mahasiswa, id_periode"
            ]);
            break;
        }

        // Cek apakah mahasiswa sudah terdaftar di periode yang sama
        $check_stmt = mysqli_prepare($conn, "SELECT id_pendaftaran FROM tabel_rekap_pendaftaran WHERE id_mahasiswa = ? AND id_periode = ?");
        mysqli_stmt_bind_param($check_stmt, "ii", $input['id_mahasiswa'], $input['id_periode']);
        mysqli_stmt_execute($check_stmt);
        $check_result = mysqli_stmt_get_result($check_stmt);

        if (mysqli_num_rows($check_result) > 0) {
            http_response_code(409);
            echo json_encode([
                "status" => "error",
                "message" => "Mahasiswa sudah terdaftar di periode wisuda ini"
            ]);
            mysqli_stmt_close($check_stmt);
            break;
        }
        mysqli_stmt_close($check_stmt);

        // Cek kuota periode
        $kuota_stmt = mysqli_prepare($conn, "SELECT pw.kuota_maksimal, COUNT(rp.id_pendaftaran) as jumlah_daftar
                                              FROM tabel_periode_wisuda pw
                                              LEFT JOIN tabel_rekap_pendaftaran rp ON pw.id_periode = rp.id_periode
                                              WHERE pw.id_periode = ?
                                              GROUP BY pw.id_periode");
        mysqli_stmt_bind_param($kuota_stmt, "i", $input['id_periode']);
        mysqli_stmt_execute($kuota_stmt);
        $kuota_result = mysqli_stmt_get_result($kuota_stmt);
        $kuota_data = mysqli_fetch_assoc($kuota_result);

        if (!$kuota_data) {
            http_response_code(404);
            echo json_encode(["status" => "error", "message" => "Periode wisuda tidak ditemukan"]);
            mysqli_stmt_close($kuota_stmt);
            break;
        }

        if ($kuota_data['jumlah_daftar'] >= $kuota_data['kuota_maksimal']) {
            http_response_code(409);
            echo json_encode(["status" => "error", "message" => "Kuota periode wisuda sudah penuh"]);
            mysqli_stmt_close($kuota_stmt);
            break;
        }
        mysqli_stmt_close($kuota_stmt);

        // Insert pendaftaran baru (id_staf = NULL, nomor_kursi = NULL, status = 'Pending')
        $stmt = mysqli_prepare($conn, "INSERT INTO tabel_rekap_pendaftaran (id_mahasiswa, id_periode, id_staf, nomor_kursi, status_verifikasi) VALUES (?, ?, NULL, NULL, 'Pending')");
        mysqli_stmt_bind_param($stmt, "ii", $input['id_mahasiswa'], $input['id_periode']);

        if (mysqli_stmt_execute($stmt)) {
            $new_id = mysqli_insert_id($conn);
            http_response_code(201);
            echo json_encode([
                "status" => "success",
                "message" => "Pendaftaran wisuda berhasil. Menunggu verifikasi.",
                "id_pendaftaran" => $new_id,
                "status_verifikasi" => "Pending"
            ]);
        } else {
            http_response_code(500);
            echo json_encode(["status" => "error", "message" => "Gagal mendaftarkan wisuda: " . mysqli_stmt_error($stmt)]);
        }
        mysqli_stmt_close($stmt);
        break;

    // =====================
    // PUT - Verifikasi oleh staf
    // Input: id_pendaftaran, status_verifikasi, nomor_kursi, id_staf
    // =====================
    case 'PUT':
        $input = json_decode(file_get_contents("php://input"), true);

        if (empty($input['id_pendaftaran'])) {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "id_pendaftaran wajib diisi untuk verifikasi"]);
            break;
        }

        if (empty($input['status_verifikasi']) || empty($input['id_staf'])) {
            http_response_code(400);
            echo json_encode([
                "status" => "error",
                "message" => "Data tidak lengkap. Field yang dibutuhkan: id_pendaftaran, status_verifikasi, nomor_kursi, id_staf"
            ]);
            break;
        }

        // Validasi status_verifikasi
        $valid_status = ['Pending', 'Disetujui', 'Ditolak'];
        if (!in_array($input['status_verifikasi'], $valid_status)) {
            http_response_code(400);
            echo json_encode([
                "status" => "error",
                "message" => "status_verifikasi harus berupa: Pending, Disetujui, atau Ditolak"
            ]);
            break;
        }

        $nomor_kursi = isset($input['nomor_kursi']) ? $input['nomor_kursi'] : null;

        $stmt = mysqli_prepare($conn, "UPDATE tabel_rekap_pendaftaran SET status_verifikasi = ?, nomor_kursi = ?, id_staf = ? WHERE id_pendaftaran = ?");
        mysqli_stmt_bind_param($stmt, "siii", $input['status_verifikasi'], $nomor_kursi, $input['id_staf'], $input['id_pendaftaran']);

        if (mysqli_stmt_execute($stmt)) {
            $affected = mysqli_stmt_affected_rows($stmt);
            if ($affected > 0) {
                echo json_encode([
                    "status" => "success",
                    "message" => "Verifikasi pendaftaran berhasil diperbarui",
                    "status_verifikasi" => $input['status_verifikasi']
                ]);
            } else {
                http_response_code(404);
                echo json_encode(["status" => "error", "message" => "Pendaftaran dengan id tersebut tidak ditemukan"]);
            }
        } else {
            http_response_code(500);
            echo json_encode(["status" => "error", "message" => "Gagal memperbarui verifikasi: " . mysqli_stmt_error($stmt)]);
        }
        mysqli_stmt_close($stmt);
        break;

    // =====================
    // DELETE - Batalkan pendaftaran wisuda
    // =====================
    case 'DELETE':
        $input = json_decode(file_get_contents("php://input"), true);

        if (empty($input['id_pendaftaran'])) {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "id_pendaftaran wajib diisi untuk membatalkan pendaftaran"]);
            break;
        }

        $stmt = mysqli_prepare($conn, "DELETE FROM tabel_rekap_pendaftaran WHERE id_pendaftaran = ?");
        mysqli_stmt_bind_param($stmt, "i", $input['id_pendaftaran']);

        if (mysqli_stmt_execute($stmt)) {
            $affected = mysqli_stmt_affected_rows($stmt);
            if ($affected > 0) {
                echo json_encode(["status" => "success", "message" => "Pendaftaran wisuda berhasil dibatalkan"]);
            } else {
                http_response_code(404);
                echo json_encode(["status" => "error", "message" => "Pendaftaran dengan id tersebut tidak ditemukan"]);
            }
        } else {
            http_response_code(500);
            echo json_encode(["status" => "error", "message" => "Gagal membatalkan pendaftaran: " . mysqli_stmt_error($stmt)]);
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
