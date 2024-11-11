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
$host = 'localhost';
$username = 'root';
$password = '';
$dbname = 'capstone_tpa';
$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Menangani operasi CRUD laporan pertumbuhan anak
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Menambah laporan pertumbuhan anak
    if (isset($_POST['add_laporan'])) {
        $id_anak = $_POST['id_anak'];
        $date = $_POST['date'];
        $nilai_moral_dan_agama = $_POST['nilai_moral_dan_agama'];
        $fisik_motorik = $_POST['fisik_motorik'];
        $kognitif = $_POST['kognitif'];
        $bahasa = $_POST['bahasa'];
        $social_emosional = $_POST['social_emosional'];

        $sql = "INSERT INTO laporan_pertumbuhan_anak_didik (id_anak, date, nilai_moral_dan_agama, fisik_motorik, kognitif, bahasa, social_emosional)
                VALUES ('$id_anak', '$date', '$nilai_moral_dan_agama', '$fisik_motorik', '$kognitif', '$bahasa', '$social_emosional')";
        if ($conn->query($sql) === TRUE) {
            echo "Laporan Pertumbuhan berhasil ditambahkan!";
        } else {
            echo "Error: " . $conn->error;
        }
    }

    // Mengupdate laporan pertumbuhan anak
    if (isset($_POST['update_laporan'])) {
        $id = $_POST['id'];
        $id_anak = $_POST['id_anak'];
        $date = $_POST['date'];
        $nilai_moral_dan_agama = $_POST['nilai_moral_dan_agama'];
        $fisik_motorik = $_POST['fisik_motorik'];
        $kognitif = $_POST['kognitif'];
        $bahasa = $_POST['bahasa'];
        $social_emosional = $_POST['social_emosional'];

        $sql = "UPDATE laporan_pertumbuhan_anak_didik SET 
                    id_anak='$id_anak', date='$date', 
                    nilai_moral_dan_agama='$nilai_moral_dan_agama', 
                    fisik_motorik='$fisik_motorik', kognitif='$kognitif', 
                    bahasa='$bahasa', social_emosional='$social_emosional' 
                WHERE id='$id'";

        if ($conn->query($sql) === TRUE) {
            echo "Laporan Pertumbuhan berhasil diupdate!";
        } else {
            echo "Error: " . $conn->error;
        }
    }

    // Menghapus laporan pertumbuhan anak
    if (isset($_POST['delete_laporan'])) {
        $id = $_POST['id'];

        $sql = "DELETE FROM laporan_pertumbuhan_anak_didik WHERE id='$id'";
        if ($conn->query($sql) === TRUE) {
            echo "Laporan Pertumbuhan berhasil dihapus!";
        } else {
            echo "Error: " . $conn->error;
        }
    }
}

// Pencarian data anak
$searchKeyword = isset($_GET['search']) ? $_GET['search'] : '';
$queryAnak = "SELECT * FROM anak";
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
    <title>Data Anak dan Laporan Pertumbuhan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Style untuk sidebar */
        .sidebar {
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            width: 240px;
            overflow-y: auto;
            background-color: #333;
            color: white;
            padding-top: 20px;
        }
        
        /* Konten utama */
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
                <h2 class="bg-info rounded p-4 text-white transition-bg">Data Anak</h2>

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
                        <th>Semester</th>
                        <th>Kelompok</th>
                        <th>Tahun</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $anakData->fetch_assoc()): ?>
                        <tr>
                            <td><?= $row['id'] ?></td>
                            <td><?= $row['nama'] ?></td>
                            <td><?= $row['semester'] ?></td>
                            <td><?= $row['kelompok'] ?></td>
                            <td><?= $row['tahun'] ?></td>
                            <td>
                                <a href="laporan_pertumbuhan_detail.php?id_anak=<?= $row['id'] ?>" class="btn btn-primary">Lihat Laporan</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
