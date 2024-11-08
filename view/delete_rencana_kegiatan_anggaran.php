<?php
// Mulai session
session_start();

// Cek apakah ID ada di URL
if (isset($_GET['id'])) {
    $idToDelete = $_GET['id'];

    // Koneksi ke database
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "capstone_tpa"; // Nama database

    // Membuat koneksi
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Memeriksa koneksi
    if ($conn->connect_error) {
        die("Koneksi gagal: " . $conn->connect_error);
    }

    // Query untuk mengambil nama file dari database berdasarkan ID
    $sql = "SELECT pengumpulan_dokumen FROM rencana_kegiatan_anggaran WHERE id = '$idToDelete'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Ambil data file
        $row = $result->fetch_assoc();
        $fileToDelete = $row['pengumpulan_dokumen'];

        // Path lengkap file yang akan dihapus
        $filePath = '../uploads/rencana_kegiatan_anggaran/' . $fileToDelete;

        // Cek apakah file ada dan hapus
        if (file_exists($filePath)) {
            unlink($filePath);  // Menghapus file dari server
        }

        // Query untuk menghapus data dari database
        $deleteSql = "DELETE FROM rencana_kegiatan_anggaran WHERE id = '$idToDelete'";

        if ($conn->query($deleteSql) === TRUE) {
            $_SESSION['status'] = 'success';
            $_SESSION['message'] = 'Data dan file berhasil dihapus!';
        } else {
            $_SESSION['status'] = 'error';
            $_SESSION['message'] = 'Terjadi kesalahan saat menghapus data!';
        }
    } else {
        $_SESSION['status'] = 'error';
        $_SESSION['message'] = 'Data tidak ditemukan!';
    }

    // Menutup koneksi
    $conn->close();

    // Redirect kembali ke halaman utama
    header('Location: rencana_kegiatan_anggaran.php');
    exit();
} else {
    // Jika ID tidak ditemukan di URL
    $_SESSION['status'] = 'error';
    $_SESSION['message'] = 'ID tidak valid!';
    header('Location: rencana_kegiatan_anggaran.php');
    exit();
}
?>
