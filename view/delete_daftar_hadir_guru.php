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
$id_daftarhadirguru = $_GET['id_daftarhadirguru'];

// Query untuk menghapus data
$sql = "DELETE FROM daftar_hadir_guru WHERE id_daftarhadirguru = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_daftarhadirguru);

if ($stmt->execute()) {
    // Jika berhasil, arahkan kembali ke halaman utama dengan parameter status success
    header("Location: daftar_hadir_guru.php?status=deleted");
    exit;
} else {
    // Jika gagal
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
