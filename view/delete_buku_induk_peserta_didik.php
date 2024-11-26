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

// // Membuat koneksi
// $conn = new mysqli($servername, $username, $password, $dbname);

// // Memeriksa koneksi
// if ($conn->connect_error) {
//     die("Koneksi gagal: " . $conn->connect_error);
// }
// Sertakan file koneksi
include '../includes/koneksi.php';

// Ambil ID yang dikirimkan melalui GET
$id = $_GET['id'];

// Query untuk mendapatkan nama file dan id_anak berdasarkan ID
$fileQuery = "SELECT dokumen, id_anak FROM data_anak WHERE id = ?";
$fileStmt = $conn->prepare($fileQuery);
$fileStmt->bind_param("i", $id);
$fileStmt->execute();
$fileResult = $fileStmt->get_result();

if ($fileResult->num_rows > 0) {
    $fileRow = $fileResult->fetch_assoc();
    $filePath = '../uploads/bukuinduk/' . $fileRow['dokumen'];
    $id_anak = $fileRow['id_anak'];  // Mendapatkan id_anak dari data_anak

    // Hapus file jika ada di server
    if (file_exists($filePath)) {
        unlink($filePath);
    }

    // Setelah file dihapus, hapus data di tabel data_anak
    $deleteQuery1 = "DELETE FROM data_anak WHERE id = ?";
    $deleteStmt1 = $conn->prepare($deleteQuery1);
    $deleteStmt1->bind_param("i", $id);
    $deleteResult1 = $deleteStmt1->execute();

    // Hapus data di tabel anak berdasarkan id_anak
    $deleteQuery2 = "DELETE FROM anak WHERE id = ?";
    $deleteStmt2 = $conn->prepare($deleteQuery2);
    $deleteStmt2->bind_param("i", $id_anak);
    $deleteResult2 = $deleteStmt2->execute();

    if ($deleteResult1 && $deleteResult2) {
        header("Location: buku_induk_peserta_didik.php?status=deleted");
        exit;
    } else {
        echo "Error: " . $conn->error;
    }

    $deleteStmt1->close();
    $deleteStmt2->close();
} else {
    header("Location: buku_induk_peserta_didik.php?status=notfound");
    exit;
}

$fileStmt->close();
$conn->close();
?>
