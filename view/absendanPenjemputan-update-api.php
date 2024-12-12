<?php
// Koneksi ke database menggunakan PDO
include '../includes/koneksi.php';

// Menangani permintaan POST (untuk menambah data aturan penjemputan)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil data yang dikirim melalui body request
    $nama = $_POST['nama'];
    $keterlambatan = $_POST['keterlambatan'];

    try {
        // Ambil detail charge dari tabel aturan_penjemputan
        $sqlDetail = "SELECT charge FROM aturan_penjemputan WHERE id = :keterlambatan";
        $stmt = $conn->prepare($sqlDetail);
        $stmt->bindParam(':keterlambatan', $keterlambatan, PDO::PARAM_INT);
        $stmt->execute();

        $resultDetail = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($resultDetail) {
            $charge = $resultDetail["charge"]; // Nilai charge yang diambil

            // Periksa apakah data anak sudah ada di tabel laporan_dana
            $sqlCheck = "SELECT keterlambatan FROM laporan_dana WHERE nama = :nama";
            $stmtCheck = $conn->prepare($sqlCheck);
            $stmtCheck->bindParam(':nama', $nama, PDO::PARAM_STR);
            $stmtCheck->execute();

            if ($stmtCheck->rowCount() > 0) {
                // Data anak ditemukan, update kolom keterlambatan
                $sqlUpdate = "UPDATE laporan_dana 
                              SET keterlambatan = keterlambatan + :charge 
                              WHERE nama = :nama";
                $stmtUpdate = $conn->prepare($sqlUpdate);
                $stmtUpdate->bindParam(':charge', $charge, PDO::PARAM_INT);
                $stmtUpdate->bindParam(':nama', $nama, PDO::PARAM_STR);
                $stmtUpdate->execute();

                echo json_encode(['status' => 'success', 'message' => 'Data keterlambatan berhasil diperbarui!']);
            } else {
                // Data anak tidak ditemukan
                echo json_encode(['status' => 'error', 'message' => 'Data anak tidak ditemukan di laporan_dana']);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Detail keterlambatan tidak ditemukan']);
        }
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Terjadi kesalahan: ' . $e->getMessage()]);
    }
}

// Menangani permintaan DELETE (untuk menghapus data aturan penjemputan)
elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    parse_str(file_get_contents("php://input"), $_DELETE);
    $id = isset($_DELETE['id']) ? intval($_DELETE['id']) : 0;

    if ($id > 0) {
        try {
            $sqlDelete = "DELETE FROM aturan_penjemputan WHERE id = :id";
            $stmtDelete = $conn->prepare($sqlDelete);
            $stmtDelete->bindParam(':id', $id, PDO::PARAM_INT);
            $stmtDelete->execute();

            echo json_encode(['status' => 'success', 'message' => 'Aturan penjemputan berhasil dihapus']);
        } catch (PDOException $e) {
            echo json_encode(['status' => 'error', 'message' => 'Penghapusan aturan penjemputan gagal: ' . $e->getMessage()]);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'ID tidak valid']);
    }
}

// Menangani permintaan PUT (untuk mengedit data aturan penjemputan)
elseif ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    parse_str(file_get_contents("php://input"), $_PUT);
    $id = isset($_PUT['id']) ? intval($_PUT['id']) : 0;
    $waktu = isset($_PUT['waktu']) ? $_PUT['waktu'] : '';
    $charge = isset($_PUT['charge']) ? $_PUT['charge'] : 0;

    if ($id > 0 && !empty($waktu) && $charge > 0) {
        try {
            $sqlUpdate = "UPDATE aturan_penjemputan 
                          SET waktu_keterlambatan_penjemputan = :waktu, charge = :charge 
                          WHERE id = :id";
            $stmtUpdate = $conn->prepare($sqlUpdate);
            $stmtUpdate->bindParam(':waktu', $waktu, PDO::PARAM_STR);
            $stmtUpdate->bindParam(':charge', $charge, PDO::PARAM_INT);
            $stmtUpdate->bindParam(':id', $id, PDO::PARAM_INT);
            $stmtUpdate->execute();

            echo json_encode(['status' => 'success', 'message' => 'Aturan penjemputan berhasil diperbarui']);
        } catch (PDOException $e) {
            echo json_encode(['status' => 'error', 'message' => 'Gagal memperbarui aturan penjemputan: ' . $e->getMessage()]);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Data tidak lengkap atau ID tidak valid']);
    }
}

// Menutup koneksi (otomatis dengan PDO)
$conn = null;
?>
