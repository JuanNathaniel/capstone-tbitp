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
    $namaDokumen = $_POST['nama_dokumen'];
    $tahunPelajaran = $_POST['tahun_pelajaran'];
    $keterangan = $_POST['keterangan'];
    
    // Mengambil data file upload
    $fileName = $_FILES['file_upload']['name'];
    $fileTmpName = $_FILES['file_upload']['tmp_name'];
    $fileSize = $_FILES['file_upload']['size'];
    $fileError = $_FILES['file_upload']['error'];

    // Menentukan ekstensi file yang diperbolehkan
    $allowedExts = ['pdf', 'docx', 'xlsx']; // Contoh ekstensi yang diperbolehkan
    $fileExt = pathinfo($fileName, PATHINFO_EXTENSION);

    // Validasi ekstensi file
    if (!in_array($fileExt, $allowedExts)) {
        echo "Ekstensi file tidak diperbolehkan.";
        exit;
    }

    // Validasi ukuran file (misalnya maksimal 50MB)
    if ($fileSize > 50000000) {
        echo "Ukuran file terlalu besar. Maksimal 50MB.";
        exit;
    }

    // Jika ada file yang diupload
    if ($fileError === 0) {
        // Membuat nama file baru (misalnya dengan menambahkan timestamp untuk mencegah duplikasi)
        $newFileName = uniqid('', true) . '.' . $fileExt;

        // Lokasi folder tempat file akan disimpan
        $uploadDir = '../uploads/data_kurikulum_merdeka/';

        // Memindahkan file ke folder upload
        if (move_uploaded_file($fileTmpName, $uploadDir . $newFileName)) {
            // Query untuk menyimpan data ke database dengan prepared statement
            $sql = "INSERT INTO data_kurikulum_merdeka (nama_dokumen, tahun_pelajaran, keterangan, pengumpulan_dokumen) 
                    VALUES (?, ?, ?, ?)";

            // Menyiapkan prepared statement
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssss", $namaDokumen, $tahunPelajaran, $keterangan, $newFileName);

            // Mengeksekusi query
            if ($stmt->execute()) {
                // Jika berhasil disimpan, beri feedback sukses
                $_SESSION['status'] = 'success'; // Untuk menampilkan SweetAlert
                header("Location: data_kurikulum_merdeka.php"); // Redirect kembali ke halaman utama
                exit;
            } else {
                echo "Error: " . $stmt->error;
            }

            // Menutup prepared statement
            $stmt->close();
        } else {
            echo "Gagal meng-upload file.";
        }
    } else {
        // Jika tidak ada file di-upload, simpan data tanpa file
        $sql = "INSERT INTO data_kurikulum_merdeka (nama_dokumen, tahun_pelajaran, keterangan) 
                VALUES (?, ?, ?)";

        // Menyiapkan prepared statement
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $namaDokumen, $tahunPelajaran, $keterangan);

        // Mengeksekusi query
        if ($stmt->execute()) {
            $_SESSION['status'] = 'success2'; // Untuk menampilkan SweetAlert
            header("Location: data_kurikulum_merdeka.php"); // Redirect kembali ke halaman utama
            exit;
        } else {
            echo "Error: " . $stmt->error;
        }

        // Menutup prepared statement
        $stmt->close();
    }
}

$conn->close();
?>
