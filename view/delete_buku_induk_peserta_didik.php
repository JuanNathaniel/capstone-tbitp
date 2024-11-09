<?php
// Koneksi ke database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "capstone_tpa";

// Membuat koneksi
$conn = new mysqli($servername, $username, $password, $dbname);

// Memeriksa koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Ambil ID yang dikirimkan melalui GET
$id = $_GET['id'];

// Query untuk mendapatkan nama file berdasarkan ID
$fileQuery = "SELECT dokumen FROM data_anak WHERE id = ?";
$fileStmt = $conn->prepare($fileQuery);
$fileStmt->bind_param("i", $id);
$fileStmt->execute();
$fileResult = $fileStmt->get_result();

if ($fileResult->num_rows > 0) {
    $fileRow = $fileResult->fetch_assoc();
    $filePath = '../uploads/bukuinduk/' . $fileRow['dokumen'];

    // Hapus file jika ada di server
    if (file_exists($filePath)) {
        unlink($filePath);
    }

    // Setelah file dihapus, hapus data di database
    $deleteQuery = "DELETE FROM data_anak WHERE id = ?";
    $deleteStmt = $conn->prepare($deleteQuery);
    $deleteStmt->bind_param("i", $id);

    if ($deleteStmt->execute()) {
        header("Location: buku_induk_peserta_didik.php?status=deleted");
        exit;
    } else {
        echo "Error: " . $deleteStmt->error;
    }

    $deleteStmt->close();
} else {
    header("Location: buku_induk_peserta_didik.php?status=notfound");
    exit;
}

$fileStmt->close();
$conn->close();
?>
