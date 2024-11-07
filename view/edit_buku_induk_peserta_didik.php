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
    $id_dataanak = $_POST['id_dataanak']; // ID yang akan diupdate
    $no_induk = $_POST['no_induk'];
    $nisn = $_POST['nisn'];
    $nama = $_POST['nama'];
    $dokumen_baru = ""; // Menyimpan nama file baru

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
        $uploadDir = '../uploads/';

        // Pindahkan file ke folder uploads
        if (move_uploaded_file($fileTmpName, $uploadDir . $dokumen_baru)) {
            // File berhasil diupload
        } else {
            echo "Gagal meng-upload file.";
            exit;
        }
    }

    // Update data anak ke database, termasuk dokumen baru jika ada
    if ($dokumen_baru) {
        // Jika ada file baru, update dokumen
        $sql = "UPDATE data_anak SET 
                    no_induk = '$no_induk',
                    nama_lengkap = '$nama',
                    nisn = '$nisn',
                    dokumen = '$dokumen_baru'
                WHERE id_dataanak = '$id_dataanak'";
    } else {
        // Jika tidak ada file baru, update data tanpa mengganti dokumen
        $sql = "UPDATE data_anak SET 
                    no_induk = '$no_induk',
                    nama_lengkap = '$nama',
                    nisn = '$nisn'
                WHERE id_dataanak = '$id_dataanak'";
    }

    // Menjalankan query
    if ($conn->query($sql) === TRUE) {
        $_SESSION['status'] = 'success';
        // Redirect atau tampilkan pesan berhasil
        header("Location: buku_induk_peserta_didik.php");
        exit;
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    // Menutup koneksi
    $conn->close();
}
?>
