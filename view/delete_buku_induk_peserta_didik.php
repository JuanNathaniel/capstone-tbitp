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
$id_dataanak = $_GET['id_dataanak'];

// Query untuk menghapus data
$sql = "DELETE FROM data_anak WHERE id_dataanak = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_dataanak);

if ($stmt->execute()) {
    // Jika berhasil, arahkan kembali ke halaman utama dengan parameter status success
    header("Location: buku_induk_peserta_didik.php?status=deleted");
    exit;
} else {
    // Jika gagal
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
