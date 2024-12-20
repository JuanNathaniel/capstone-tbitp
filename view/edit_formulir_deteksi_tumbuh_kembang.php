<?php
session_start();

// Cek apakah pengguna sudah login
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

// Regenerasi ID sesi untuk keamanan ekstra
session_regenerate_id(true);
?>
<?php

// // Koneksi ke database
// $servername = "localhost";
// $username = "root";
// $password = "";
// $dbname = "capstone_tpa";

// $conn = new mysqli($servername, $username, $password, $dbname);

// if ($conn->connect_error) {
//     die("Koneksi gagal: " . $conn->connect_error);
// }
// Sertakan file koneksi
include '../includes/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Ambil data dari form
    $id = $_POST['id']; // ID yang akan diupdate
    $id_siswa = $_POST['id_siswa']; // ID yang akan diupdate
    $tahun_pelajaran = $_POST['tahun_pelajaran'];
    $keterangan = $_POST['keterangan'];
    $dokumen_baru = ""; // Menyimpan nama file baru

    // Query untuk mengambil nama file lama dari database
    $sql_get_file = "SELECT pengumpulan_dokumen FROM formulir_deteksi_tumbuh_kembang WHERE id = ?";
    $stmt = $conn->prepare($sql_get_file);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $oldFileName = $row['pengumpulan_dokumen']; // Nama file lama

    // Cek apakah ada file baru yang diupload
    if (isset($_FILES['file_upload']) && $_FILES['file_upload']['error'] == 0) {
        // Ambil informasi file
        $fileName = $_FILES['file_upload']['name'];
        $fileTmpName = $_FILES['file_upload']['tmp_name'];
        $fileSize = $_FILES['file_upload']['size'];
        $fileError = $_FILES['file_upload']['error'];

        // Ekstensi file
        $fileExt = pathinfo($fileName, PATHINFO_EXTENSION);

        // Nama file baru dengan menambahkan timestamp untuk mencegah duplikasi
        $dokumen_baru = uniqid('', true) . '.' . $fileExt;
        $uploadDir = '../uploads/formulir_deteksi_dan_tumbuh_kembang/';

        // Pindahkan file ke folder uploads
        if (move_uploaded_file($fileTmpName, $uploadDir . $dokumen_baru)) {
            // Jika ada file lama, hapus file lama
            if (!empty($oldFileName)) {
                $oldFilePath = $uploadDir . $oldFileName;
                if (file_exists($oldFilePath)) {
                    unlink($oldFilePath); // Menghapus file lama
                }
            }
        } else {
            echo "Gagal meng-upload file.";
            exit;
        }
    }

    // Update data anak ke database, termasuk dokumen baru jika ada
    if ($dokumen_baru) {
        // Jika ada file baru, update dokumen
        $sql = "UPDATE formulir_deteksi_tumbuh_kembang SET 
                    tahun_pelajaran = ?, 
                    keterangan = ?, 
                    pengumpulan_dokumen = ? 
                WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssi", $tahun_pelajaran, $keterangan, $dokumen_baru, $id);
    } else {
        // Jika tidak ada file baru, update data tanpa mengganti dokumen
        $sql = "UPDATE formulir_deteksi_tumbuh_kembang SET 
                    tahun_pelajaran = ?, 
                    keterangan = ? 
                WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssi", $tahun_pelajaran, $keterangan, $id);
    }

    // Menjalankan query
    if ($stmt->execute()) {
        $_SESSION['status'] = 'success';
        // Redirect atau tampilkan pesan berhasil
        header("Location: formulir_deteksi_tumbuh_kembang.php");
        exit;
    } else {
        echo "Error: " . $stmt->error;
    }

    // Menutup koneksi
    $stmt->close();
    $conn->close();
}
?>
