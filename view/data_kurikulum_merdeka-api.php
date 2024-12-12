<?php
// Menyertakan file koneksi database
include '../includes/koneksi.php';

// Mengatur header untuk API (format JSON)
header('Content-Type: application/json');

// Menangani request berdasarkan metode
switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        // Mengambil daftar file dari database
        $sql = "SELECT * FROM data_kurikulum_merdeka";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $files = [];
            while ($row = $result->fetch_assoc()) {
                $files[] = $row;
            }
            echo json_encode([
                'status' => 'success',
                'data' => $files
            ]);
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'Tidak ada file ditemukan.'
            ]);
        }
        break;

    case 'POST':
        // Meng-upload file baru
        if (isset($_FILES['file_upload'])) {
            $fileName = $_FILES['file_upload']['name'];
            $fileTmpName = $_FILES['file_upload']['tmp_name'];
            $fileExt = pathinfo($fileName, PATHINFO_EXTENSION);
            $newFileName = uniqid('', true) . '.' . $fileExt;
            $uploadDir = '../uploads/data_kurikulum_merdeka/';

            if (move_uploaded_file($fileTmpName, $uploadDir . $newFileName)) {
                $sql = "INSERT INTO data_kurikulum_merdeka (pengumpulan_dokumen) VALUES ('$newFileName')";
                if ($conn->query($sql) === TRUE) {
                    echo json_encode([
                        'status' => 'success',
                        'message' => 'File berhasil di-upload!',
                        'file_name' => $newFileName
                    ]);
                } else {
                    echo json_encode([
                        'status' => 'error',
                        'message' => 'Terjadi kesalahan saat menyimpan data ke database.'
                    ]);
                }
            } else {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Gagal meng-upload file.'
                ]);
            }
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'File tidak ditemukan dalam request.'
            ]);
        }
        break;

    case 'PUT':
        // Mengupdate data file
        $inputData = json_decode(file_get_contents('php://input'), true); // Ambil data JSON dari body
        if (isset($inputData['id']) && isset($inputData['file_name'])) {
            $id = $inputData['id'];
            $newFileName = $inputData['file_name'];

            $sql = "UPDATE data_kurikulum_merdeka SET pengumpulan_dokumen = '$newFileName' WHERE id = $id";
            if ($conn->query($sql) === TRUE) {
                echo json_encode([
                    'status' => 'success',
                    'message' => 'Data berhasil di-update!'
                ]);
            } else {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Terjadi kesalahan saat mengupdate data.'
                ]);
            }
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'Data yang dibutuhkan tidak lengkap.'
            ]);
        }
        break;

    case 'DELETE':
        // Menghapus data file
        $inputData = json_decode(file_get_contents('php://input'), true); // Ambil data JSON dari body
        if (isset($inputData['id'])) {
            $id = $inputData['id'];

            // Pertama, kita perlu mengambil nama file yang ada untuk dihapus dari folder
            $sql = "SELECT pengumpulan_dokumen FROM data_kurikulum_merdeka WHERE id = $id";
            $result = $conn->query($sql);
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $fileToDelete = '../uploads/data_kurikulum_merdeka/' . $row['pengumpulan_dokumen'];

                // Menghapus file fisik dari folder
                if (file_exists($fileToDelete)) {
                    unlink($fileToDelete); // Menghapus file
                }

                // Menghapus data file dari database
                $sqlDelete = "DELETE FROM data_kurikulum_merdeka WHERE id = $id";
                if ($conn->query($sqlDelete) === TRUE) {
                    echo json_encode([
                        'status' => 'success',
                        'message' => 'File berhasil dihapus!'
                    ]);
                } else {
                    echo json_encode([
                        'status' => 'error',
                        'message' => 'Terjadi kesalahan saat menghapus data.'
                    ]);
                }
            } else {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'File tidak ditemukan.'
                ]);
            }
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'ID file tidak ditemukan.'
            ]);
        }
        break;

        default:
        // Method request tidak valid
        echo json_encode([
            'status' => 'error',
            'message' => 'Metode request tidak valid.'
        ]);
        break;
}

// Menutup koneksi
$conn->close();
?>
