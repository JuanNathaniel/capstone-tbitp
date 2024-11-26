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

// session_start();

// // Membuat koneksi
// $conn = new mysqli($servername, $username, $password, $dbname);

// // Memeriksa koneksi
// if ($conn->connect_error) {
//     die("Koneksi gagal: " . $conn->connect_error);
// }
// Sertakan file koneksi
include '../includes/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Mengambil data dari form edit
    $id = $_POST['edit_id'];
    $nama_dokumen = $_POST['edit_nama_dokumen'];
    $tahun_anggaran = $_POST['edit_tahun_anggaran'];
    $keterangan = $_POST['edit_keterangan'];
    $status = $_POST['edit_status'];
    $dokumen = $_POST['edit_dokumen']; // Jika ada file yang diedit

    // Query untuk mendapatkan file lama dari database
    $sql_get_file = "SELECT pengumpulan_dokumen FROM rencana_kegiatan_anggaran WHERE id = $id";
    $result = $conn->query($sql_get_file);
    $row = $result->fetch_assoc();
    $oldFileName = $row['pengumpulan_dokumen'];

    // Jika ada file yang di-upload
    if ($_FILES['file_upload']['error'] === 0) {
        // Cek jika file lama ada, hapus file lama
        if (!empty($oldFileName)) {
            $oldFilePath = '../uploads/rencana_kegiatan_anggaran/' . $oldFileName;
            if (file_exists($oldFilePath)) {
                unlink($oldFilePath); // Menghapus file lama
            }
        }

        // Menangani file baru
        $fileName = $_FILES['file_upload']['name'];
        $fileTmpName = $_FILES['file_upload']['tmp_name'];
        $fileExt = pathinfo($fileName, PATHINFO_EXTENSION);
        $newFileName = uniqid('', true) . '.' . $fileExt;
        $uploadDir = '../uploads/rencana_kegiatan_anggaran/';

        if (move_uploaded_file($fileTmpName, $uploadDir . $newFileName)) {
            // Update data termasuk file yang baru
            $sql = "UPDATE rencana_kegiatan_anggaran 
                    SET nama_dokumen='$nama_dokumen', tahun_anggaran='$tahun_anggaran', keterangan='$keterangan', status='$status', pengumpulan_dokumen='$newFileName' 
                    WHERE id=$id";
        }
    } else {
        // Jika tidak ada file baru, cukup update data lain
        $sql = "UPDATE rencana_kegiatan_anggaran 
                SET nama_dokumen='$nama_dokumen', tahun_anggaran='$tahun_anggaran', keterangan='$keterangan', status='$status' 
                WHERE id=$id";
    }

    if ($conn->query($sql) === TRUE) {
        $_SESSION['status'] = 'success'; // Untuk menampilkan SweetAlert
        header("Location: rencana_kegiatan_anggaran.php"); // Redirect kembali ke halaman utama
        exit;
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

$conn->close();
?>
