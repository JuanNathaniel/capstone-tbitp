<!DOCTYPE html>
<html lang="en">

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
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Data</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php
    // Database connection
    $pdo = new PDO("mysql:host=localhost;dbname=capstone_tpa", "root", "");

    // Fetch list of anak
    $stmt_anak = $pdo->query("SELECT id, nama FROM anak");
    $list_anak = $stmt_anak->fetchAll(PDO::FETCH_ASSOC);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Collect form data
        $id_anak = $_POST['id_anak'];
        $tanggal = $_POST['tanggal'];
        $nama_pengantar = $_POST['nama_pengantar'];
        $jam_datang = $_POST['jam_datang'];
        $paraf_pengantar = $_POST['paraf_pengantar'];
        $nama_penjemput = $_POST['nama_penjemput'];
        $jam_jemput = $_POST['jam_jemput'];
        $paraf_penjemput = $_POST['paraf_penjemput'];

        // Insert data into 'pengantar' table
        $stmt_pengantar = $pdo->prepare("
            INSERT INTO pengantar (nama_pengantar, jam_datang, paraf) 
            VALUES (:nama_pengantar, :jam_datang, :paraf_pengantar)
        ");
        $stmt_pengantar->execute([
            ':nama_pengantar' => $nama_pengantar,
            ':jam_datang' => $jam_datang,
            ':paraf_pengantar' => $paraf_pengantar
        ]);
        $id_pengantar = $pdo->lastInsertId();

        // Insert data into 'penjemput' table
        $stmt_penjemput = $pdo->prepare("
            INSERT INTO penjemput (nama_penjemput, jam_jemput, paraf) 
            VALUES (:nama_penjemput, :jam_jemput, :paraf_penjemput)
        ");
        $stmt_penjemput->execute([
            ':nama_penjemput' => $nama_penjemput,
            ':jam_jemput' => $jam_jemput,
            ':paraf_penjemput' => $paraf_penjemput
        ]);
        $id_penjemput = $pdo->lastInsertId();

        // Insert data into 'absensi_dan_jemput' table with selected date
        $stmt_absensi = $pdo->prepare("
            INSERT INTO absensi_dan_jemput (id_anak, id_pengantar, id_penjemput, date)
            VALUES (:id_anak, :id_pengantar, :id_penjemput, :tanggal)
        ");
        $stmt_absensi->execute([
            ':id_anak' => $id_anak,
            ':id_pengantar' => $id_pengantar,
            ':id_penjemput' => $id_penjemput,
            ':tanggal' => $tanggal
        ]);

        header('Location: absendanPenjemputan.php');
        exit;
    }
    ?>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <?php include 'sidebar.php'; ?>

            <!-- Konten Utama -->
            <main class="col-md-9 col-lg-10 ms-auto" style="margin-left: auto;">
                <h2 class="bg-info rounded p-4 text-white transition-bg">Absensi Datang dan Jemput - Create</h2>
                
        <form method="POST">
            <div class="mb-3">
                <label for="id_anak" class="form-label">Nama Anak</label>
                <select class="form-control" id="id_anak" name="id_anak" required>
                    <option value="">Pilih Anak</option>
                    <?php foreach ($list_anak as $anak): ?>
                        <option value="<?= htmlspecialchars($anak['id']) ?>"><?= htmlspecialchars($anak['nama']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="tanggal" class="form-label">Tanggal</label>
                <input type="date" class="form-control" id="tanggal" name="tanggal" required>
            </div>
            <div class="mb-3">
                <label for="nama_pengantar" class="form-label">Nama Pengantar</label>
                <input type="text" class="form-control" id="nama_pengantar" name="nama_pengantar" required>
            </div>
            <div class="mb-3">
                <label for="jam_datang" class="form-label">Jam Datang</label>
                <input type="time" class="form-control" id="jam_datang" name="jam_datang" required>
            </div>
            <div class="mb-3">
                <label for="paraf_pengantar" class="form-label">Paraf Pengantar</label>
                <input type="text" class="form-control" id="paraf_pengantar" name="paraf_pengantar" required>
            </div>
            <div class="mb-3">
                <label for="nama_penjemput" class="form-label">Nama Penjemput</label>
                <input type="text" class="form-control" id="nama_penjemput" name="nama_penjemput" required>
            </div>
            <div class="mb-3">
                <label for="jam_jemput" class="form-label">Jam Jemput</label>
                <input type="time" class="form-control" id="jam_jemput" name="jam_jemput" required>
            </div>
            <div class="mb-3">
                <label for="paraf_penjemput" class="form-label">Paraf Penjemput</label>
                <input type="text" class="form-control" id="paraf_penjemput" name="paraf_penjemput" required>
            </div>
            <button type="submit" class="btn btn-primary">Create</button>
            <a href="absendanPenjemputan.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
