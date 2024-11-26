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

// Menangani input tahun ajaran
$tahunAjaran = isset($_POST['tahun_ajaran']) ? $_POST['tahun_ajaran'] : (isset($_GET['tahun_ajaran']) ? $_GET['tahun_ajaran'] : '');

// Ambil semua jenis rincian biaya pendidikan
$sqlJenis = "SELECT * FROM jenis_rincian_biaya_pendidikan";
$resultJenis = $conn->query($sqlJenis);
$jenisPendidikan = [];

while ($row = $resultJenis->fetch_assoc()) {
    $jenisPendidikan[$row['id']] = [
        'jenis_pendidikan' => $row['jenis_pendidikan'],
        'keterangan' => $row['keterangan'],
    ];
}

// Ambil rincian biaya per jenis pendidikan
$rincianBiaya = [];
foreach ($jenisPendidikan as $idJenis => $jenis) {
    $sql = "SELECT * FROM rincian_biaya_pendidikan WHERE id_jenis='$idJenis' AND tahun_ajaran='$tahunAjaran'";
    $result = $conn->query($sql);
    $rincianBiaya[$idJenis] = $result->fetch_all(MYSQLI_ASSOC);
}

// Menangani proses tambah dan edit data
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    $idJenis = $_POST['id_jenis'];
    $uraian = $_POST['uraian'];
    $biaya = $_POST['biaya'];

    if ($_POST['action'] == 'update' && isset($_POST['id'])) {
        $id = $_POST['id'];
        $sql = "UPDATE rincian_biaya_pendidikan SET uraian='$uraian', biaya='$biaya' WHERE id='$id' AND tahun_ajaran='$tahunAjaran'";
    } else {
        $sql = "INSERT INTO rincian_biaya_pendidikan (id_jenis, uraian, biaya, tahun_ajaran) VALUES ('$idJenis', '$uraian', '$biaya', '$tahunAjaran')";
    }

    if ($conn->query($sql)) {
        header("Location: biaya_pendidikan.php?tahun_ajaran=$tahunAjaran");
    } else {
        echo json_encode(['status' => 'error']);
    }
    exit;
}

// Menangani proses hapus data
if (isset($_GET['delete']) && isset($_GET['tahun_ajaran'])) {
    $id = $_GET['delete'];
    $tahunAjaran = $_GET['tahun_ajaran'];
    $sql = "DELETE FROM rincian_biaya_pendidikan WHERE id='$id' AND tahun_ajaran='$tahunAjaran'";
    $conn->query($sql);
    header("Location: biaya_pendidikan.php?tahun_ajaran=$tahunAjaran");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Biaya Pendidikan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
                <h2 class="bg-info rounded p-4 text-white transition-bg">Rincian Biaya Pendidikan</h2>

        <!-- Input Tahun Ajaran -->
        <form method="POST" action="biaya_pendidikan.php" class="mb-4" id="tahunForm">
            <div class="row">
                <div class="col-md-4">
                    <label for="tahun_ajaran" class="form-label">Tahun Ajaran</label>
                    <input type="text" name="tahun_ajaran" id="tahun_ajaran" class="form-control" value="<?= htmlspecialchars($tahunAjaran) ?>" required>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary mt-4">Set Tahun Ajaran</button>
                </div>
            </div>
        </form>

        <?php if ($tahunAjaran): ?>
            <div class="alert alert-info" role="alert">
                Tahun Ajaran yang dipilih: <strong><?= htmlspecialchars($tahunAjaran) ?></strong>
            </div>
            <a href="biaya_pendidikan_pdf.php?tahun_ajaran=<?= htmlspecialchars($tahunAjaran) ?>" class="btn btn-primary">Download PDF</a>

            <?php foreach ($jenisPendidikan as $idJenis => $jenis): ?>
                <h3 class="mt-4"><?= htmlspecialchars($jenis['jenis_pendidikan']) ?></h3>
                <p><strong>Keterangan:</strong> <?= htmlspecialchars($jenis['keterangan']) ?></p>

                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Uraian</th>
                            <th>Biaya</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $totalBiaya = 0;
                        if (!empty($rincianBiaya[$idJenis])) {
                            foreach ($rincianBiaya[$idJenis] as $row): 
                                $totalBiaya += $row['biaya'];
                        ?>
                        <tr>
                            <td><?= htmlspecialchars($row['uraian']) ?></td>
                            <td><?= number_format($row['biaya'], 2) ?></td>
                            <td>
                                <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editModal<?= $row['id'] ?>">Edit</button>
                                <a href="biaya_pendidikan.php?delete=<?= $row['id'] ?>&tahun_ajaran=<?= htmlspecialchars($tahunAjaran) ?>" class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')">Delete</a>
                            </td>
                        </tr>
                        <div class="modal fade" id="editModal<?= $row['id'] ?>" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="editModalLabel">Edit Rincian Biaya</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <form method="POST" action="biaya_pendidikan.php?tahun_ajaran=<?= htmlspecialchars($tahunAjaran) ?>" class="ajax-form">
                                        <div class="modal-body">
                                            <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                            <input type="hidden" name="id_jenis" value="<?= $idJenis ?>">
                                            <div class="mb-3">
                                                <label for="uraian" class="form-label">Uraian</label>
                                                <input type="text" name="uraian" class="form-control" value="<?= htmlspecialchars($row['uraian']) ?>" required>
                                            </div>
                                            <div class="mb-3">
                                                <label for="biaya" class="form-label">Biaya</label>
                                                <input type="number" name="biaya" class="form-control" value="<?= $row['biaya'] ?>" step="0.01" required>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                            <button type="submit" name="action" value="update" class="btn btn-primary">Save changes</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; } ?>
                    </tbody>
                </table>
                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addModal<?= $idJenis ?>">Tambah Data</button>
                <div class="modal fade" id="addModal<?= $idJenis ?>" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="addModalLabel">Tambah Rincian Biaya</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <form action="biaya_pendidikan.php?tahun_ajaran=<?= htmlspecialchars($tahunAjaran) ?>" method="POST" class="ajax-form">
                                <div class="modal-body">
                                    <input type="hidden" name="id_jenis" value="<?= $idJenis ?>">
                                    <div class="mb-3">
                                        <label for="uraian" class="form-label">Uraian</label>
                                        <input type="text" name="uraian" class="form-control" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="biaya" class="form-label">Biaya</label>
                                        <input type="number" name="biaya" class="form-control" step="0.01" required>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    <button type="submit" name="action" value="insert" class="btn btn-primary">Save</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="alert alert-warning" role="alert">
                Silakan pilih tahun ajaran terlebih dahulu.
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
