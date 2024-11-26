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
// $host = 'localhost';
// $username = 'root';
// $password = '';
// $dbname = 'capstone_tpa';
// $conn = new mysqli($host, $username, $password, $dbname);

// if ($conn->connect_error) {
//     die("Connection failed: " . $conn->connect_error);
// }
include '../includes/koneksi.php';

// Pencarian data anak
$searchKeyword = isset($_GET['search']) ? $_GET['search'] : '';
$queryAnak = "SELECT id, nama FROM anak";
if ($searchKeyword != "") {
    $queryAnak .= " WHERE nama LIKE '%$searchKeyword%'";
}
$anakData = $conn->query($queryAnak);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekapitulasi Pembayaran Anak</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .transition-bg {
            background: linear-gradient(to right, #344EAD, #1767A6); /* Gradasi horizontal */
        }
        
        .main-content {
            margin-left: 540px;
            padding-left: 120px;
        }
    </style>
</head>
<body>

<div class="container-fluid">
    <div class="row">
        <?php include 'sidebar.php'; ?> <!-- Include file sidebar -->

        <!-- Konten Utama -->
        <main class="col-md-9 col-lg-10 ms-auto" style="margin-left: auto;">
            <h2 class="bg-info rounded p-4 text-white transition-bg">Rekapitulasi Pembayaran Anak</h2>

            <!-- Formulir Pencarian -->
            <form method="GET" action="" class="mb-4">
                <div class="input-group">
                    <input type="text" name="search" class="form-control" placeholder="Cari Nama Anak..." value="<?= htmlspecialchars($searchKeyword) ?>">
                    <button type="submit" class="btn btn-primary">Cari</button>
                </div>
            </form>

            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nama Anak</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $anakData->fetch_assoc()): ?>
                        <tr>
                            <td><?= $row['id'] ?></td>
                            <td><?= $row['nama'] ?></td>
                            <td>
                                <a href="rekapitulasi_pembayaran_detail.php?id_anak=<?= $row['id'] ?>" class="btn btn-primary">Lihat Rekapitulasi</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </main>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
