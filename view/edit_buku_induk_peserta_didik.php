<?php
session_start();

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

    // // Query untuk mengambil nama file lama dari database
    // $sql_get_file = "SELECT dokumen FROM data_anak WHERE id = '$id'";
    // $result = $conn->query($sql_get_file);
    // $row = $result->fetch_assoc();
    // $oldFileName = $row['dokumen']; // Nama file lama

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
                    id_anak = '$id_siswa',  
                    no_induk = '$no_induk',
                    nama_lengkap = '$nama',
                    nisn = '$nisn',
                    dokumen = '$dokumen_baru'  
                WHERE id = '$id'";
    } else {
        // Jika tidak ada file baru, update data tanpa mengganti dokumen
        $sql = "UPDATE data_anak SET 
                    id_anak = '$id_siswa',  
                    no_induk = '$no_induk',
                    nama_lengkap = '$nama',
                    nisn = '$nisn' 
                WHERE id = '$id'";
    }

    $sql2 = "UPDATE anak SET 
        nama = '$nama',
        usia = '$usia',
        semester = '$semester',
        kelompok = '$kelompok',
        tahun = '$tahun'
    WHERE id = '$id_siswa'";

    // Menjalankan query pertama
    if ($conn->query($sql) === TRUE) {
        // Menjalankan query kedua jika query pertama berhasil
        if ($conn->query($sql2) === TRUE) {
            // Commit transaksi jika kedua query berhasil
            $conn->commit();
            $_SESSION['status'] = 'success';
            header("Location: buku_induk_peserta_didik.php");
            exit;
        } else {
            // Rollback jika query kedua gagal
            $conn->rollback();
            echo "Error on second query: " . $sql2 . "<br>" . $conn->error;
        }
    } else {
        // Rollback jika query pertama gagal
        $conn->rollback();
        echo "Error on first query: " . $sql . "<br>" . $conn->error;
    }
}
?>
