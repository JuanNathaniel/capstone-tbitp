<?php
// Memulai sesi untuk status
session_start();

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

// Ambil data yang dikirimkan melalui POST
$id_daftar_hadir_guru = $_POST['id_daftar_hadir_guru'];
$id_guru = $_POST['id_guru'];
$jam_datang = $_POST['jam_datang'];
$jam_pulang = $_POST['jam_pulang'];
$keterangan = $_POST['keterangan'];
$tanggal = $_POST['tanggal'];

// Query untuk update data
$sql = "UPDATE daftar_hadir_guru 
        SET id_guru = ?, jam_datang = ?, jam_pulang = ?, keterangan = ?, date = ? 
        WHERE id_daftarhadirguru = ?";

// Menyiapkan prepared statement
$stmt = $conn->prepare($sql);

// Mengikat parameter dengan tipe data yang sesuai
$stmt->bind_param("sssssi", $id_guru, $jam_datang, $jam_pulang, $keterangan, $tanggal, $id_daftar_hadir_guru);

// Mengeksekusi query
if ($stmt->execute()) {
    // Set session status success
    $_SESSION['status'] = 'success';
    
    // Arahkan kembali ke halaman utama dengan parameter status success
    header("Location: daftar_hadir_guru.php");
    exit;
} else {
    // Jika query gagal
    echo "Error: " . $stmt->error;
}

// Menutup prepared statement dan koneksi
$stmt->close();
$conn->close();
?>
