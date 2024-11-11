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
// Koneksi ke database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "capstone_tpa";

session_start();

// Membuat koneksi
$conn = new mysqli($servername, $username, $password, $dbname);

// Memeriksa koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Mengambil data dari form
    $nama_dokumen = $_POST['nama_dokumen'];
    $tahun_anggaran = $_POST['tahun_anggaran'];
    $keterangan = $_POST['keterangan'];
    $status = $_POST['status']; // Mendapatkan nilai status dari form
    
    // Mengambil data file upload
    $fileName = $_FILES['file_upload']['name'];
    $fileTmpName = $_FILES['file_upload']['tmp_name'];
    $fileSize = $_FILES['file_upload']['size'];
    $fileError = $_FILES['file_upload']['error'];

    // Jika ada file yang diupload
    if ($fileError === 0) {
        // Mengambil ekstensi file
        $fileExt = pathinfo($fileName, PATHINFO_EXTENSION);

        // Membuat nama file baru (misalnya dengan menambahkan timestamp untuk mencegah duplikasi)
        $newFileName = uniqid('', true) . '.' . $fileExt;

        // Lokasi folder tempat file akan disimpan
        $uploadDir = '../uploads/rencana_kegiatan_anggaran/';

        // Memindahkan file ke folder upload
        if (move_uploaded_file($fileTmpName, $uploadDir . $newFileName)) {
            // Simpan data ke database, termasuk nama file yang telah diubah dan status
            $sql = "INSERT INTO rencana_kegiatan_anggaran (id_admin, nama_dokumen, tahun_anggaran, keterangan, pengumpulan_dokumen, status) 
                    VALUES (1, '$nama_dokumen', '$tahun_anggaran', '$keterangan', '$newFileName', '$status')";
            
            if ($conn->query($sql) === TRUE) {
                // Jika berhasil disimpan, beri feedback sukses
                $_SESSION['status'] = 'success'; // Untuk menampilkan SweetAlert
                header("Location: rencana_kegiatan_anggaran.php"); // Redirect kembali ke halaman utama
                exit;
            } else {
                echo "Error: " . $sql . "<br>" . $conn->error;
            }
        } else {
            echo "Gagal meng-upload file.";
        }
    } else {
        // Jika tidak ada file di-upload, simpan data tanpa file
        $sql = "INSERT INTO rencana_kegiatan_anggaran (id_admin, nama_dokumen, tahun_anggaran, keterangan, status) 
                VALUES (1, '$nama_dokumen', '$tahun_anggaran', '$keterangan', '$status')";
        
        if ($conn->query($sql) === TRUE) {
            $_SESSION['status'] = 'success2'; // Untuk menampilkan SweetAlert
            header("Location: rencana_kegiatan_anggaran.php"); // Redirect kembali ke halaman utama
            exit;
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    }
}

$conn->close();
?>
