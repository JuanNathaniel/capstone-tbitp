
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

// Membuat koneksi
$conn = new mysqli($servername, $username, $password, $dbname);

// Memeriksa koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Mengambil data dari form
    $nama_anak = $_POST['nama_anak'];
    $usia = $_POST['usia'];
    $noInduk = $_POST['no_induk'];
    $nisn = $_POST['nisn'];
    $semester = $_POST['semester'];
    $kelompok = $_POST['kelompok'];
    $tahun = $_POST['tahun'];

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

            // Insert data anak menggunakan prepared statement
            $sqlAnak = "INSERT INTO anak (nama, usia, semester, kelompok, tahun) VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sqlAnak);
            $stmt->bind_param("sssss", $nama_anak, $usia, $semester, $kelompok, $tahun);
            if ($stmt->execute()) {
                // Dapatkan ID terakhir yang dihasilkan untuk kolom `id_anak`
                $idAnak = $conn->insert_id;

                // Menambah data pembayaran default untuk anak baru di tabel rekapitulasi_pembayaran
                $payments = [
                    'Pendaftaran' => [0, 0, ''],
                    'SPP Bulan' => [0, 0, ''],
                    'Seragam' => [0, 0, ''],
                    'Pengembangan Sekolah' => [0, 0, ''],
                    'Kegiatan Pembelajaran' => [0, 0, '']
                ];
                //inituh buat rekapitulasi
                $sql3 = "INSERT INTO rekapitulasi_pembayaran (id_anak, jenis_pembayaran, cicilan_1, cicilan_2, keterangan) 
                        VALUES (?, ?, ?, ?, ?)";
                $stmt3 = $conn->prepare($sql3);

                foreach ($payments as $jenis => $data) {
                    $stmt3->bind_param("isiis", $idAnak, $jenis, $data[0], $data[1], $data[2]);
                    if (!$stmt3->execute()) {
                        throw new Exception("Gagal memasukkan data pembayaran untuk jenis: " . $jenis);
                }
                }

                // Insert data ke tabel data_anak menggunakan prepared statement
                $sqlDataAnak = "INSERT INTO data_anak (id_anak, no_induk, nisn, nama_lengkap, dokumen) VALUES (?, ?, ?, ?, ?)";
                $stmtDataAnak = $conn->prepare($sqlDataAnak);
                $stmtDataAnak->bind_param("iisss", $idAnak, $noInduk, $nisn, $nama_anak, $newFileName);

                if ($stmtDataAnak->execute()) {
                    $_SESSION['status'] = 'success2'; // Untuk menampilkan SweetAlert
                    header("Location: buku_induk_peserta_didik.php"); // Redirect kembali ke halaman utama
                    exit;
                } else {
                    echo "Error: " . $stmtDataAnak->error;
                }
            } else {
                echo "Error: " . $stmt->error;
            }
        } else {
            echo "Gagal meng-upload file.";
        }
    } else {
        // Jika tidak ada file di-upload, simpan data tanpa file

        $sqlAnak = "INSERT INTO anak (nama, usia, semester, kelompok, tahun) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sqlAnak);
        $stmt->bind_param("sssss", $nama_anak, $usia, $semester, $kelompok, $tahun);

        if ($stmt->execute()) {
            // Dapatkan ID terakhir yang dihasilkan untuk kolom `id_anak`
            $idAnak = $conn->insert_id;

            // Menambah data pembayaran default untuk anak baru di tabel rekapitulasi_pembayaran
            $payments = [
                'Pendaftaran' => [0, 0, ''],
                'SPP Bulan' => [0, 0, ''],
                'Seragam' => [0, 0, ''],
                'Pengembangan Sekolah' => [0, 0, ''],
                'Kegiatan Pembelajaran' => [0, 0, '']
            ];
            //inituh buat rekapitulasi
            $sql3 = "INSERT INTO rekapitulasi_pembayaran (id_anak, jenis_pembayaran, cicilan_1, cicilan_2, keterangan) 
                    VALUES (?, ?, ?, ?, ?)";
            $stmt3 = $conn->prepare($sql3);

            foreach ($payments as $jenis => $data) {
                $stmt3->bind_param("isiis", $idAnak, $jenis, $data[0], $data[1], $data[2]);
                if (!$stmt3->execute()) {
                    throw new Exception("Gagal memasukkan data pembayaran untuk jenis: " . $jenis);
            }
            }

            // Insert data ke tabel data_anak tanpa file menggunakan prepared statement
            $sqlDataAnak = "INSERT INTO data_anak (id_anak, no_induk, nisn, nama_lengkap) VALUES (?, ?, ?, ?)";
            $stmtDataAnak = $conn->prepare($sqlDataAnak);
            $stmtDataAnak->bind_param("iiss", $idAnak, $noInduk, $nisn, $nama_anak);

            if ($stmtDataAnak->execute()) {
                $_SESSION['status'] = 'success2'; // Untuk menampilkan SweetAlert
                header("Location: buku_induk_peserta_didik.php"); // Redirect kembali ke halaman utama
                exit;
            } else {
                echo "Error: " . $stmtDataAnak->error;
            }
        } else {
            echo "Error: " . $stmt->error;
        }
    }
}

// Menutup koneksi
$conn->close();
?>
