<?php
session_start();

// Cek apakah pengguna sudah login
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

// Regenerasi ID sesi untuk keamanan ekstra
session_regenerate_id(true);

// Sertakan file koneksi
include '../includes/koneksi.php';

// Cek apakah id_daftarhadirguru ada di parameter GET
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id_daftarhadirguru = $_GET['id'];

    // Query untuk menghapus data
    $sql = "DELETE FROM daftar_hadir_guru WHERE id_daftarhadirguru = ?";

    if ($stmt = $conn->prepare($sql)) {
        // Bind parameter dan eksekusi query
        $stmt->bind_param("i", $id_daftarhadirguru);

        if ($stmt->execute()) {
            // Jika berhasil, arahkan kembali ke halaman utama dengan parameter status success
            $_SESSION['status'] = 'deleted'; // Menambahkan status untuk menunjukkan penghapusan berhasil
            header("Location: daftar_hadir_guru.php?status=deleted");
            exit;
        } else {
            echo "Error: " . $stmt->error;
        }
        $stmt->close();
    } else {
        echo "Error: Gagal menyiapkan query.";
    }
} else {
    echo "Error: ID tidak valid.";
}

$conn->close();
?>
