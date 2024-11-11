
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
    $noInduk = $_POST['no_induk'];
    $nisn = $_POST['nisn'];
    $nama = $_POST['nama'];
    
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
        $uploadDir = '../uploads/bukuinduk/';

        // Memindahkan file ke folder upload
        if (move_uploaded_file($fileTmpName, $uploadDir . $newFileName)) {
            // Simpan data ke database, termasuk nama file yang telah diubah
            $sql = "INSERT INTO data_anak (no_induk, nisn, nama_lengkap, dokumen) 
                    VALUES ('$noInduk', '$nisn', '$nama', '$newFileName')";
            
            if ($conn->query($sql) === TRUE) {
                // Jika berhasil disimpan, beri feedback sukses
                $_SESSION['status'] = 'success'; // Untuk menampilkan SweetAlert
                header("Location: buku_induk_peserta_didik.php"); // Redirect kembali ke halaman utama
                exit;
            } else {
                echo "Error: " . $sql . "<br>" . $conn->error;
            }
        } else {
            echo "Gagal meng-upload file.";
        }
    } else {
        // Jika tidak ada file di-upload, simpan data tanpa file
        $sql = "INSERT INTO data_anak (no_induk, nisn, nama_lengkap) 
                VALUES ('$noInduk', '$nisn', '$nama')";
        
        if ($conn->query($sql) === TRUE) {
            $_SESSION['status'] = 'success2'; // Untuk menampilkan SweetAlert
            header("Location: buku_induk_peserta_didik.php"); // Redirect kembali ke halaman utama
            exit;
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    }
}

$conn->close();
?>
