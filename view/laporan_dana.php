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

// Menangani proses update data
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update'])) {
    $id = $_POST['id'];
    $pendaftaran = $_POST['pendaftaran'] ?? 0;
    $spp_bulan = $_POST['spp_bulan'] ?? 0;
    $seragam = $_POST['seragam'] ?? 0;
    $pengembangan_sekolah = $_POST['pengembangan_sekolah'] ?? 0;
    $kegiatan_pembelajaran = $_POST['kegiatan_pembelajaran'] ?? 0;
    $keterlambatan = $_POST['keterlambatan'] ?? 0;
    $infaq = $_POST['infaq'] ?? 0;
    $keterangan = $_POST['keterangan'] ?? '';

    // Update data ke database
    $sql = "UPDATE laporan_dana SET pendaftaran='$pendaftaran', spp_bulan='$spp_bulan', seragam='$seragam',
            pengembangan_sekolah='$pengembangan_sekolah', kegiatan_pembelajaran='$kegiatan_pembelajaran', keterlambatan='$keterlambatan', infaq='$infaq', 
            keterangan='$keterangan' WHERE id='$id'";

    if ($conn->query($sql) === TRUE) {
        echo "Data berhasil diperbarui";
    } else {
        echo "Error: " . $conn->error;
    }
}

// Menangani proses delete data
if (isset($_POST['delete_id'])) {
    $id = $_POST['delete_id'];

    // Menghapus data dari database
    $sql = "DELETE FROM laporan_dana WHERE id='$id'";

    if ($conn->query($sql) === TRUE) {
        echo "Data berhasil dihapus";
    } else {
        echo "Error: " . $conn->error;
    }
}

// Mengambil data laporan_dana dengan JOIN ke tabel anak
$sql = "SELECT laporan_dana.*, anak.nama AS nama_siswa 
        FROM laporan_dana 
        JOIN anak ON laporan_dana.nama = anak.id";
$result = $conn->query($sql);

// Inisialisasi variabel untuk menghitung total di setiap kolom
$total_pendaftaran = $total_spp = $total_seragam = $total_pengembangan = $total_kegiatan = $total_keterlambatan =  $total_infaq = $grand_total = 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Dana</title>
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
                <h2 class="bg-info rounded p-4 text-white transition-bg">Tabel Laporan Dana</h2>


        
        <a href="laporan_dana-create.php" class="btn btn-success mb-4">Tambah Data</a>
        <a href="laporan_dana_pdf.php" class="btn btn-success">Download PDF</a>

        <!-- Tabel Data Laporan Dana -->
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nama</th>
                    <th>Pendaftaran</th>
                    <th>SPP Bulan</th>
                    <th>Seragam</th>
                    <th>Pengembangan Sekolah</th>
                    <th>Kegiatan Pembelajaran</th>
                    <th>Biaya Keterlambatan</th>
                    <th>Infaq</th>
                    <th>Total</th>
                    <th>Keterangan</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): 
                    $total = $row['pendaftaran'] + $row['spp_bulan'] + $row['seragam'] + $row['pengembangan_sekolah'] + $row['kegiatan_pembelajaran'] + $row['infaq'];
                    // Tambahkan ke total keseluruhan
                    $total_pendaftaran += $row['pendaftaran'];
                    $total_spp += $row['spp_bulan'];
                    $total_seragam += $row['seragam'];
                    $total_pengembangan += $row['pengembangan_sekolah'];
                    $total_kegiatan += $row['kegiatan_pembelajaran'];
                    $total_keterlambatan += $row['keterlambatan'];
                    $total_infaq += $row['infaq'];
                    $grand_total += $total;
                ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td><?= $row['nama_siswa'] ?></td>
                    <td><?= number_format($row['pendaftaran'], 2) ?></td>
                    <td><?= number_format($row['spp_bulan'], 2) ?></td>
                    <td><?= number_format($row['seragam'], 2) ?></td>
                    <td><?= number_format($row['pengembangan_sekolah'], 2) ?></td>
                    <td><?= number_format($row['kegiatan_pembelajaran'], 2) ?></td>
                    <td><?= number_format($row['keterlambatan'], 2) ?></td>
                    <td><?= number_format($row['infaq'], 2) ?></td>
                    <td><?= number_format($total, 2) ?></td>
                    <td><?= $row['keterangan'] ?></td>
                    <td>
                        <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editModal<?= $row['id'] ?>">Update</button>
                        <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteModal<?= $row['id'] ?>">Delete</button>
                    </td>
                </tr>

                <!-- Modal Edit -->
                <div class="modal fade" id="editModal<?= $row['id'] ?>" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="editModalLabel">Edit Data</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <form action="laporan_dana.php" method="POST">
                                <div class="modal-body">
                                    <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                    <!-- Fields for each financial category -->
                                    <div class="mb-3">
                                        <label for="pendaftaran" class="form-label">Pendaftaran</label>
                                        <input type="number" name="pendaftaran" class="form-control" value="<?= $row['pendaftaran'] ?>" step="0.01" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="spp_bulan" class="form-label">SPP Bulan</label>
                                        <input type="number" name="spp_bulan" class="form-control" value="<?= $row['spp_bulan'] ?>" step="0.01" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="seragam" class="form-label">Seragam</label>
                                        <input type="number" name="seragam" class="form-control" value="<?= $row['seragam'] ?>" step="0.01" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="pengembangan_sekolah" class="form-label">Pengembangan Sekolah</label>
                                        <input type="number" name="pengembangan_sekolah" class="form-control" value="<?= $row['pengembangan_sekolah'] ?>" step="0.01" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="kegiatan_pembelajaran" class="form-label">Kegiatan Pembelajaran</label>
                                        <input type="number" name="kegiatan_pembelajaran" class="form-control" value="<?= $row['kegiatan_pembelajaran'] ?>" step="0.01" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="keterlambatan" class="form-label">Keterlambatan</label>
                                        <input type="number" name="keterlambatan" class="form-control" value="<?= $row['keterlambatan'] ?>" step="0.01" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="infaq" class="form-label">Infaq</label>
                                        <input type="number" name="infaq" class="form-control" value="<?= $row['infaq'] ?>" step="0.01" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="keterangan" class="form-label">Keterangan</label>
                                        <textarea name="keterangan" class="form-control"><?= $row['keterangan'] ?></textarea>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    <button type="submit" name="update" class="btn btn-primary">Save changes</button>
                                
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                 <!-- Modal Delete -->
                <div class="modal fade" id="deleteModal<?= $row['id'] ?>" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="deleteModalLabel">Konfirmasi Hapus</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                Apakah Anda yakin ingin menghapus data ini?
                            </div>
                            <div class="modal-footer">
                                <form action="laporan_dana.php" method="POST">
                                    <input type="hidden" name="delete_id" value="<?= $row['id'] ?>">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                    <button type="submit" class="btn btn-danger">Hapus</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            </tbody>
            <!-- Baris total keseluruhan -->
            <tfoot>
                <tr>
                    <th colspan="2">Total Keseluruhan</th>
                    <th><?= number_format($total_pendaftaran, 2) ?></th>
                    <th><?= number_format($total_spp, 2) ?></th>
                    <th><?= number_format($total_seragam, 2) ?></th>
                    <th><?= number_format($total_pengembangan, 2) ?></th>
                    <th><?= number_format($total_kegiatan, 2) ?></th>
                    <th><?= number_format($total_keterlambatan, 2) ?></th>
                    <th><?= number_format($total_infaq, 2) ?></th>
                    <th><?= number_format($grand_total, 2) ?></th>
                    <th colspan="2"></th>
                </tr>
            </tfoot>
        </table>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
$conn->close();
?>
