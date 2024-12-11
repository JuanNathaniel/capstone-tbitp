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
// $conn = new mysqli("localhost", "root", "", "capstone_tpa");

// if ($conn->connect_error) {
//     die("Connection failed: " . $conn->connect_error);
// }
// Sertakan file koneksi
include '../includes/koneksi.php';

// Menangani proses input data
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit'])) {
    $nama = $_POST['nama'];
    $pendaftaran = $_POST['pendaftaran'] ?? 0;
    $spp_bulan = $_POST['spp_bulan'] ?? 0;
    $seragam = $_POST['seragam'] ?? 0;
    $pengembangan_sekolah = $_POST['pengembangan_sekolah'] ?? 0;
    $kegiatan_pembelajaran = $_POST['kegiatan_pembelajaran'] ?? 0;
    $keterlambatan = $_POST['keterlambatan'] ?? 0;
    $infaq = $_POST['infaq'] ?? 0;
    $date = $_POST['date'];
    $keterangan = $_POST['keterangan'] ?? '';

    // Menambah data pembayaran default untuk anak baru di tabel rekapitulasi_pembayaran
    $payments = [
        'Pendaftaran' => [$pendaftaran, 0, 0, ''],
        'SPP Bulan' => [$spp_bulan, 0, 0, ''],
        'Seragam' => [$seragam, 0, 0, ''],
        'Pengembangan Sekolah' => [$pengembangan_sekolah, 0, 0, ''],
        'Kegiatan Pembelajaran' => [$kegiatan_pembelajaran, 0, 0, ''],
        'keterlambatan' => [$keterlambatan, 0, 0, ''],
        'infaq' => [$infaq, 0, 0, '']
    ];

    // // Query untuk mendapatkan id anak berdasarkan nama
    // $query = "SELECT id FROM anak WHERE nama = ?";
    // $stmt = $pdo->prepare($query); // Gunakan PDO
    // $stmt->execute([$nama]);
    // $result = $stmt->fetch(PDO::FETCH_ASSOC);
    // $idAnak = $result['id'];

    //inituh buat rekapitulasi
    $sql3 = "INSERT INTO rekapitulasi_pembayaran (id_anak, jenis_pembayaran, jumlah, cicilan_1, cicilan_2, keterangan) 
            VALUES (?, ?, ?, ?, ?, ?)";
    $stmt3 = $conn->prepare($sql3);

    foreach ($payments as $jenis => $data) {
        $stmt3->bind_param("isiiis", $nama, $jenis, $data[0], $data[1], $data[2], $data[3]);
        if (!$stmt3->execute()) {
            throw new Exception("Gagal memasukkan data pembayaran untuk jenis: " . $jenis);
        }
    }

    // Menyimpan data ke database
    $sql = "INSERT INTO laporan_dana (nama, pendaftaran, spp_bulan, seragam, pengembangan_sekolah, kegiatan_pembelajaran, keterlambatan, infaq, keterangan, date)
        VALUES ('$nama', '$pendaftaran', '$spp_bulan', '$seragam', '$pengembangan_sekolah', '$kegiatan_pembelajaran', '$keterlambatan', '$infaq', '$keterangan', '$date')";

    if ($conn->query($sql) === TRUE) {
    header("Location: laporan_dana.php?message=success");
    } else {
    echo "<div class='alert alert-danger'>Error: " . $conn->error . "</div>";
    }

    if (empty($nama) || empty($date)) {
    echo "Nama dan tanggal tidak boleh kosong.";
    exit();
}

if ($pendaftaran < 0 || $spp_bulan < 0 || $seragam < 0 || $pengembangan_sekolah < 0 || $kegiatan_pembelajaran < 0 || $keterlambatan < 0 || $infaq < 0) {
    echo "Angka harus bernilai positif.";
    exit();
}

}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Input Laporan Dana</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .transition-bg {
            background: linear-gradient(to right, #344EAD, #1767A6); /* Gradasi horizontal */
        }
    </style>
</head>

<body>

<div class="container-fluid">
        <div class="row">
            <?php include 'sidebar.php'; ?> <!-- Include file sidebar -->

            <!-- Konten Utama -->
            <main class="col-md-9 col-lg-10 ms-auto" style="margin-left: auto;">
                <h2 class="bg-info rounded p-4 text-white transition-bg">Input Laporan Dana</h2>


        <!-- Form Input Data -->
        <form action="laporan_dana-create.php" method="POST">
            <div class="mb-3">
                <label for="nama" class="form-label">Nama</label>
                <select name="nama" id="nama" class="form-control" required>
                    <option value="">Pilih Nama</option>
                    <?php
                    $result = $conn->query("SELECT id, nama FROM anak");
                    while ($row = $result->fetch_assoc()) {
                        echo "<option value='" . $row['id'] . "'>" . $row['nama'] . "</option>";
                    }
                    ?>
                </select>
            </div>
            
            <div class="mb-3">
                <label for="pendaftaran" class="form-label">Pendaftaran</label>
                <input type="number" name="pendaftaran" class="form-control" step="0.01" id="pendaftaran">
            </div>
            
            <div class="mb-3">
                <label for="spp_bulan" class="form-label">SPP Bulan</label>
                <input type="number" name="spp_bulan" class="form-control" step="0.01" id="spp_bulan">
            </div>

            <div class="mb-3">
                <label for="seragam" class="form-label">Seragam</label>
                <input type="number" name="seragam" class="form-control" step="0.01" id="seragam">
            </div>

            <div class="mb-3">
                <label for="pengembangan_sekolah" class="form-label">Pengembangan Sekolah</label>
                <input type="number" name="pengembangan_sekolah" class="form-control" step="0.01" id="pengembangan_sekolah">
            </div>

            <div class="mb-3">
                <label for="kegiatan_pembelajaran" class="form-label">Kegiatan Pembelajaran</label>
                <input type="number" name="kegiatan_pembelajaran" class="form-control" step="0.01" id="kegiatan_pembelajaran">
            </div>
            
            <div class="mb-3">
                <label for="keterlambatan" class="form-label">Keterlambatan</label>
                <input type="number" name="keterlambatan" class="form-control" step="0.01" id="keterlambatan">
            </div>

            <div class="mb-3">
                <label for="infaq" class="form-label">Infaq</label>
                <input type="number" name="infaq" class="form-control" step="0.01" id="infaq">
            </div>

            <div class="mb-3">
                <label for="keterangan" class="form-label">Keterangan</label>
                <textarea name="keterangan" class="form-control" id="keterangan"></textarea>
            </div>
            <div class="mb-3">
    <label for="date" class="form-label">Tanggal</label>
    <input type="date" name="date" class="form-control" id="date" required>
</div>


            <button type="submit" name="submit" class="btn btn-primary">Simpan Data</button>
            <a href="laporan_dana.php" class="btn btn-secondary">Kembali</a>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
$conn->close();
?>
