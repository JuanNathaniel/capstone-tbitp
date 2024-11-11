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

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_siswa = $_POST['id_siswa'];
    $tahun_pelajaran = $_POST['tahun_pelajaran'];
    $keterangan = $_POST['keterangan'];
    $fileName = $_FILES['file_upload']['name'];
    $targetDir = "../uploads/formulir_deteksi_dan_tumbuh_kembang/";

    // Cek apakah id_siswa ada di tabel data_anak
    $stmt = $conn->prepare("SELECT nama_lengkap FROM data_anak WHERE id_anak = ?");
    $stmt->bind_param("i", $id_siswa);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows === 0) {
        echo "ID anak tidak ditemukan dalam tabel data_anak.";
        exit();
    }
    $row = $result->fetch_assoc();
    $nama_siswa = $row['nama_lengkap'];

    // Mengambil ekstensi file
    $fileExt = pathinfo($fileName, PATHINFO_EXTENSION);

    // Membuat nama file baru dengan uniqid untuk mencegah duplikasi
    $newFileName = uniqid('', true) . '.' . $fileExt;
    $targetFilePath = $targetDir . $newFileName;

    // Periksa apakah ada file yang diupload
    if (!empty($fileName)) {
        // Simpan file dengan nama baru untuk mencegah duplikasi
        if (move_uploaded_file($_FILES['file_upload']['tmp_name'], $targetFilePath)) {
            // Insert data dengan file
            $sql = "INSERT INTO formulir_deteksi_tumbuh_kembang (id_anak, nama_siswa, tahun_pelajaran, pengumpulan_dokumen, keterangan) VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("issss", $id_siswa, $nama_siswa, $tahun_pelajaran, $newFileName, $keterangan);
        } else {
            echo "Gagal meng-upload file.";
            exit();
        }
    } else {
        // Insert data tanpa file
        $sql = "INSERT INTO formulir_deteksi_tumbuh_kembang (id_anak, nama_siswa, tahun_pelajaran, keterangan) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("isss", $id_siswa, $nama_siswa, $tahun_pelajaran, $keterangan);
    }

    // Eksekusi pernyataan insert
    if ($stmt->execute()) {
        $_SESSION['status'] = 'success2';
        header("Location: formulir_deteksi_tumbuh_kembang.php");
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>
