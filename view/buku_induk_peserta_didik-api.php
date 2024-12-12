<?php

// Sertakan file koneksi
include '../includes/koneksi.php';

// Set header untuk format JSON
header('Content-Type: application/json');

// Periksa method request
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        // Ambil data dengan pencarian opsional
        $search = isset($_GET['search']) ? $_GET['search'] : '';
        $sql = "SELECT data_anak.id, data_anak.id_anak, data_anak.no_induk, data_anak.nisn, anak.nama, anak.usia, anak.semester, anak.kelompok, anak.tahun, dokumen FROM data_anak JOIN anak ON data_anak.id_anak = anak.id";

        if ($search != '') {
            $sql .= " WHERE anak.nama LIKE '%" . $conn->real_escape_string($search) . "%'";
        }

        $result = $conn->query($sql);
        $data = [];

        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
        }

        echo json_encode(['status' => 'success', 'data' => $data]);
        break;

    case 'POST':
        // Tambah data baru
        $data = json_decode(file_get_contents('php://input'), true);

        if (isset($data['id_anak'], $data['no_induk'], $data['nisn'], $data['dokumen'])) {
            $id_anak = $data['id_anak'];
            $no_induk = $data['no_induk'];
            $nisn = $data['nisn'];
            $dokumen = $data['dokumen'];

            $sql = "INSERT INTO data_anak (id_anak, no_induk, nisn, dokumen) VALUES ('$id_anak', '$no_induk', '$nisn', '$dokumen')";

            if ($conn->query($sql)) {
                echo json_encode(['status' => 'success', 'message' => 'Data berhasil ditambahkan']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Gagal menambahkan data']);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Data tidak lengkap']);
        }
        break;

    case 'PUT':
        // Update data
        $data = json_decode(file_get_contents('php://input'), true);

        if (isset($data['id'], $data['id_anak'], $data['no_induk'], $data['nisn'], $data['dokumen'])) {
            $id = $data['id'];
            $id_anak = $data['id_anak'];
            $no_induk = $data['no_induk'];
            $nisn = $data['nisn'];
            $dokumen = $data['dokumen'];

            $sql = "UPDATE data_anak SET id_anak = '$id_anak', no_induk = '$no_induk', nisn = '$nisn', dokumen = '$dokumen' WHERE id = '$id'";

            if ($conn->query($sql)) {
                echo json_encode(['status' => 'success', 'message' => 'Data berhasil diubah']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Gagal mengubah data']);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Data tidak lengkap']);
        }
        break;

    case 'DELETE':
        // Hapus data
        parse_str(file_get_contents('php://input'), $data);

        if (isset($data['id'])) {
            $id = $data['id'];
            $sql = "DELETE FROM data_anak WHERE id = '$id'";

            if ($conn->query($sql)) {
                echo json_encode(['status' => 'success', 'message' => 'Data berhasil dihapus']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Gagal menghapus data']);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'ID tidak ditemukan']);
        }
        break;

    default:
        echo json_encode(['status' => 'error', 'message' => 'Method tidak valid']);
        break;
}

// Tutup koneksi
$conn->close();
