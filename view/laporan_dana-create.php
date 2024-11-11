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
$conn = new mysqli("localhost", "root", "", "capstone_tpa");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Menangani proses input data
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit'])) {
    $nama = $_POST['nama'];
    $pendaftaran = $_POST['pendaftaran'] ?? 0;
    $spp_bulan = $_POST['spp_bulan'] ?? 0;
    $seragam = $_POST['seragam'] ?? 0;
    $pengembangan_sekolah = $_POST['pengembangan_sekolah'] ?? 0;
    $kegiatan_pembelajaran = $_POST['kegiatan_pembelajaran'] ?? 0;
    $infaq = $_POST['infaq'] ?? 0;
    $keterangan = $_POST['keterangan'] ?? '';

    // Menyimpan data ke database
    $sql = "INSERT INTO laporan_dana (nama, pendaftaran, spp_bulan, seragam, pengembangan_sekolah, kegiatan_pembelajaran, infaq, keterangan)
            VALUES ('$nama', '$pendaftaran', '$spp_bulan', '$seragam', '$pengembangan_sekolah', '$kegiatan_pembelajaran', '$infaq', '$keterangan')";

    if ($conn->query($sql) === TRUE) {
        echo "Data berhasil disimpan";
    } else {
        echo "Error: " . $conn->error;
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
                <label for="infaq" class="form-label">Infaq</label>
                <input type="number" name="infaq" class="form-control" step="0.01" id="infaq">
            </div>

            <div class="mb-3">
                <label for="keterangan" class="form-label">Keterangan</label>
                <textarea name="keterangan" class="form-control" id="keterangan"></textarea>
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
