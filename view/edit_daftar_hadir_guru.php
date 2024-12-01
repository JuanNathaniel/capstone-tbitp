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
// Sertakan file koneksi
include '../includes/koneksi.php';

// Ambil data yang dikirimkan melalui POST
$id_daftar_hadir_guru = $_POST['id_daftar_hadir_guru'];
$id_guru = $_POST['id_guru'];
$jam_datang = $_POST['jam_datang'];
$jam_pulang = $_POST['jam_pulang'];
$keterangan = $_POST['keterangan'];
$tanda_tangan = isset($_POST['tanda_tangan1']) ? 1 : 0; // Checkbox untuk tanda tangan
$tanggal = $_POST['tanggal'];

// Query untuk update data
$sql = "UPDATE daftar_hadir_guru 
        SET id_guru = ?, jam_datang = ?, jam_pulang = ?, keterangan = ?, tanda_tangan1 = ?, date = ? 
        WHERE id_daftarhadirguru = ?";

// Menyiapkan prepared statement
$stmt = $conn->prepare($sql);

// Mengikat parameter dengan tipe data yang sesuai
$stmt->bind_param("ssssisi", $id_guru, $jam_datang, $jam_pulang, $keterangan, $tanda_tangan, $tanggal, $id_daftar_hadir_guru);

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
