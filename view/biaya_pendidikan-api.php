<?php
// Koneksi ke database
include '../includes/koneksi.php';

// Set header untuk JSON
header("Content-Type: application/json");

// Ambil metode request
$method = $_SERVER['REQUEST_METHOD'];

// Ambil input JSON jika ada
$input = json_decode(file_get_contents('php://input'), true);

// Hasil respons
$response = [
    'status' => 'error',
    'message' => 'Invalid request.',
];

switch ($method) {
    case 'GET':
        // Ambil data rincian biaya berdasarkan tahun ajaran (jika diberikan)
        $tahunAjaran = isset($_GET['tahun_ajaran']) ? $_GET['tahun_ajaran'] : '';
        $idJenis = isset($_GET['id_jenis']) ? intval($_GET['id_jenis']) : null;

        if ($tahunAjaran) {
            $query = "SELECT rbp.id, rbp.id_jenis, rbp.uraian, rbp.biaya, rbp.tahun_ajaran, jrbp.jenis_pendidikan, jrbp.keterangan
                      FROM rincian_biaya_pendidikan rbp
                      INNER JOIN jenis_rincian_biaya_pendidikan jrbp ON rbp.id_jenis = jrbp.id
                      WHERE rbp.tahun_ajaran = '" . $conn->real_escape_string($tahunAjaran) . "'";

            if ($idJenis) {
                $query .= " AND rbp.id_jenis = " . $idJenis;
            }

            $result = $conn->query($query);

            if ($result) {
                $data = $result->fetch_all(MYSQLI_ASSOC);
                $response = [
                    'status' => 'success',
                    'data' => $data,
                ];
            } else {
                $response['message'] = 'Gagal mengambil data.';
            }
        } else {
            $response['message'] = 'Tahun ajaran tidak diberikan.';
        }
        break;

    case 'POST':
        // Tambah data baru
        if (isset($input['id_jenis'], $input['uraian'], $input['biaya'], $input['tahun_ajaran'])) {
            $idJenis = intval($input['id_jenis']);
            $uraian = $conn->real_escape_string($input['uraian']);
            $biaya = floatval($input['biaya']);
            $tahunAjaran = $conn->real_escape_string($input['tahun_ajaran']);

            $query = "INSERT INTO rincian_biaya_pendidikan (id_jenis, uraian, biaya, tahun_ajaran) 
                      VALUES ('$idJenis', '$uraian', '$biaya', '$tahunAjaran')";

            if ($conn->query($query)) {
                $response = [
                    'status' => 'success',
                    'message' => 'Data berhasil ditambahkan.'
                ];
            } else {
                $response['message'] = 'Gagal menambahkan data.';
            }
        } else {
            $response['message'] = 'Input tidak lengkap.';
        }
        break;

    case 'PUT':
        // Perbarui data
        if (isset($input['id'], $input['id_jenis'], $input['uraian'], $input['biaya'], $input['tahun_ajaran'])) {
            $id = intval($input['id']);
            $idJenis = intval($input['id_jenis']);
            $uraian = $conn->real_escape_string($input['uraian']);
            $biaya = floatval($input['biaya']);
            $tahunAjaran = $conn->real_escape_string($input['tahun_ajaran']);

            $query = "UPDATE rincian_biaya_pendidikan 
                      SET id_jenis = '$idJenis', uraian = '$uraian', biaya = '$biaya' 
                      WHERE id = '$id' AND tahun_ajaran = '$tahunAjaran'";

            if ($conn->query($query)) {
                $response = [
                    'status' => 'success',
                    'message' => 'Data berhasil diperbarui.'
                ];
            } else {
                $response['message'] = 'Gagal memperbarui data.';
            }
        } else {
            $response['message'] = 'Input tidak lengkap.';
        }
        break;

    case 'DELETE':
        // Hapus data
        if (isset($input['id'], $input['tahun_ajaran'])) {
            $id = intval($input['id']);
            $tahunAjaran = $conn->real_escape_string($input['tahun_ajaran']);

            $query = "DELETE FROM rincian_biaya_pendidikan WHERE id = '$id' AND tahun_ajaran = '$tahunAjaran'";

            if ($conn->query($query)) {
                $response = [
                    'status' => 'success',
                    'message' => 'Data berhasil dihapus.'
                ];
            } else {
                $response['message'] = 'Gagal menghapus data.';
            }
        } else {
            $response['message'] = 'Input tidak lengkap.';
        }
        break;

    default:
        $response['message'] = 'Metode request tidak valid.';
        break;
}

// Kembalikan hasil dalam format JSON
echo json_encode($response);
?>
