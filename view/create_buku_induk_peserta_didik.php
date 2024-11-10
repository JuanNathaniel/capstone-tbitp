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
    $nama_anak = $_POST['nama_anak'];
    $noInduk = $_POST['no_induk'];
    $nisn = $_POST['nisn'];
    $usia = $_POST['usia'];
    $semester = $_POST['semester'];
    $kelompok = $_POST['kelompok'];
    $tahun = $_POST['tahun'];

    // // Query untuk mengambil data nama lengkap siswa berdasarkan ID yang dipilih
    // $siswaDetailQuery = "SELECT nama FROM anak WHERE id = ?";
    // $stmt = $conn->prepare($siswaDetailQuery);
    // $stmt->bind_param("i", $idSiswa);
    // $stmt->execute();
    // $siswaResult = $stmt->get_result();
    // $siswaData = $siswaResult->fetch_assoc(); // Mengambil data sebagai array asosiatif
    // $nama = $siswaData['nama']; // Ambil nilai nama dari array
    
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
            // Jika tidak ada file di-upload, simpan data tanpa file
            $sqlAnak = "INSERT INTO anak (nama, usia, semester, kelompok, tahun) 
            VALUES ('$nama_anak', '$usia', '$semester','$kelompok', '$tahun')";
    
            if ($conn->query($sqlAnak) === TRUE) {
                // Dapatkan ID terakhir yang dihasilkan untuk kolom `id_anak`
                $idAnak = $conn->insert_id;
            
                // Query untuk memasukkan data ke tabel `data_anak` menggunakan id dari tabel `anak`
                $sqlDataAnak = "INSERT INTO data_anak (id_anak, no_induk, nisn, nama_lengkap, dokumen) 
                                VALUES ('$idAnak', '$noInduk', '$nisn', '$nama_anak', '$newFileName')";
            
                if ($conn->query($sqlDataAnak) === TRUE) {
                    $_SESSION['status'] = 'success2'; // Untuk menampilkan SweetAlert
                    header("Location: buku_induk_peserta_didik.php"); // Redirect kembali ke halaman utama
                    exit;
                } else {
                    echo "Error: " . $sqlDataAnak . "<br>" . $conn->error;
                }
            } else {
                echo "Error: " . $sqlAnak . "<br>" . $conn->error;
            }
        } else {
            echo "Gagal meng-upload file.";
        }
    } else {
        // Jika tidak ada file di-upload, simpan data tanpa file
        $sqlAnak = "INSERT INTO anak (nama, usia, semester, kelompok, tahun) 
                VALUES ('$nama_anak', '$usia', '$semester','$kelompok', '$tahun')";
        
        if ($conn->query($sqlAnak) === TRUE) {
            // Dapatkan ID terakhir yang dihasilkan untuk kolom `id_anak`
            $idAnak = $conn->insert_id;
        
            // Query untuk memasukkan data ke tabel `data_anak` menggunakan id dari tabel `anak`
            $sqlDataAnak = "INSERT INTO data_anak (id_anak, no_induk, nisn, nama_lengkap) 
                            VALUES ('$idAnak', '$noInduk', '$nisn', '$nama_anak')";
        
            if ($conn->query($sqlDataAnak) === TRUE) {
                $_SESSION['status'] = 'success2'; // Untuk menampilkan SweetAlert
                header("Location: buku_induk_peserta_didik.php"); // Redirect kembali ke halaman utama
                exit;
            } else {
                echo "Error: " . $sqlDataAnak . "<br>" . $conn->error;
            }
        } else {
            echo "Error: " . $sqlAnak . "<br>" . $conn->error;
        }
    }
}

$conn->close();
?>
