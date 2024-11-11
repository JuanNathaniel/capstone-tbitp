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


// Koneksi ke database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "capstone_tpa"; // Ganti dengan nama database Anda

// Membuat koneksi
$conn = new mysqli($servername, $username, $password, $dbname);

// Memeriksa koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Ambil data dari form
    $id = $_POST['id']; // ID yang akan diupdate
    $id_siswa = $_POST['id_siswa']; // ID siswa yang dipilih dari dropdown
    $no_induk = $_POST['no_induk'];
    $nisn = $_POST['nisn'];
    $usia = $_POST['usia'];
    $semester = $_POST['semester'];
    $kelompok = $_POST['kelompok'];
    $tahun = $_POST['tahun'];
    $dokumen_baru = ""; // Menyimpan nama file baru

    // Query untuk mengambil data nama lengkap siswa berdasarkan ID yang dipilih
    $siswaDetailQuery = "SELECT nama FROM anak WHERE id = ?";
    $stmt = $conn->prepare($siswaDetailQuery);
    $stmt->bind_param("i", $id_siswa);
    $stmt->execute();
    $siswaResult = $stmt->get_result();
    $siswaData = $siswaResult->fetch_assoc(); // Mengambil data sebagai array asosiatif
    $nama = $siswaData['nama']; // Ambil nilai nama dari array

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
        $uploadDir = '../uploads/bukuinduk/';

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
        $sql = "UPDATE data_anak SET 
                    id_anak = ?,  
                    no_induk = ?, 
                    nama_lengkap = ?, 
                    nisn = ?, 
                    dokumen = ? 
                WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iisssi", $id_siswa, $no_induk, $nama, $nisn, $dokumen_baru, $id);
    } else {
        // Jika tidak ada file baru, update data tanpa mengganti dokumen
        $sql = "UPDATE data_anak SET 
                    id_anak = ?,  
                    no_induk = ?, 
                    nama_lengkap = ?, 
                    nisn = ? 
                WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iissi", $id_siswa, $no_induk, $nama, $nisn, $id);
    }

    $sql2 = "UPDATE anak SET 
                nama = ?, 
                usia = ?, 
                semester = ?, 
                kelompok = ?, 
                tahun = ? 
            WHERE id = ?";
    $stmt2 = $conn->prepare($sql2);
    $stmt2->bind_param("sssssi", $nama, $usia, $semester, $kelompok, $tahun, $id_siswa);

    // Menjalankan query pertama
    $conn->begin_transaction();
    try {
        if ($stmt->execute() && $stmt2->execute()) {
            // Commit transaksi jika kedua query berhasil
            $conn->commit();
            $_SESSION['status'] = 'success';
            header("Location: buku_induk_peserta_didik.php");
            exit;
        } else {
            // Rollback jika query gagal
            $conn->rollback();
            echo "Error: " . $conn->error;
        }
    } catch (Exception $e) {
        // Rollback jika terjadi exception
        $conn->rollback();
        echo "Exception: " . $e->getMessage();
    }
}
?>
