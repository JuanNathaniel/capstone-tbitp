<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Data</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php
    // Koneksi database
    $pdo = new PDO("mysql:host=localhost;dbname=capstone_tpa", "root", "");

    // Ambil data berdasarkan ID anak
    $id_anak = $_GET['id'];
$stmt = $pdo->prepare("
    SELECT anak.nama AS nama_siswa, pengantar.nama_pengantar, pengantar.jam_datang, pengantar.paraf AS paraf_pengantar, 
           penjemput.nama_penjemput, penjemput.jam_jemput, penjemput.paraf AS paraf_penjemput, 
           absen.id_pengantar, absen.id_penjemput
    FROM absensi_dan_jemput AS absen
    INNER JOIN anak ON absen.id_anak = anak.id
    INNER JOIN pengantar ON absen.id_pengantar = pengantar.id
    INNER JOIN penjemput ON absen.id_penjemput = penjemput.id
    WHERE absen.id = :id
");
$stmt->execute([':id' => $id_anak]);
$data = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$data) {
    // Jika data tidak ditemukan, tampilkan pesan atau redirect
    echo "Data tidak ditemukan.";
    exit;
}
    $stmt->execute([':id' => $id_anak]);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $nama_pengantar = $_POST['nama_pengantar'];
        $jam_datang = $_POST['jam_datang'];
        $paraf_pengantar = $_POST['paraf_pengantar'];
        $nama_penjemput = $_POST['nama_penjemput'];
        $jam_jemput = $_POST['jam_jemput'];
        $paraf_penjemput = $_POST['paraf_penjemput'];

        // Perbarui data pengantar
        $sql_update_pengantar = "
            UPDATE pengantar 
            SET nama_pengantar = :nama_pengantar, jam_datang = :jam_datang, paraf = :paraf_pengantar 
            WHERE id = :id_pengantar
        ";
        $stmt_update_pengantar = $pdo->prepare($sql_update_pengantar);
        $stmt_update_pengantar->execute([
            ':id_pengantar' => $data['id_pengantar'],
            ':nama_pengantar' => $nama_pengantar,
            ':jam_datang' => $jam_datang,
            ':paraf_pengantar' => $paraf_pengantar,
        ]);

        // Perbarui data penjemput
        $sql_update_penjemput = "
            UPDATE penjemput 
            SET nama_penjemput = :nama_penjemput, jam_jemput = :jam_jemput, paraf = :paraf_penjemput 
            WHERE id = :id_penjemput
        ";
        $stmt_update_penjemput = $pdo->prepare($sql_update_penjemput);
        $stmt_update_penjemput->execute([
            ':id_penjemput' => $data['id_penjemput'],
            ':nama_penjemput' => $nama_penjemput,
            ':jam_jemput' => $jam_jemput,
            ':paraf_penjemput' => $paraf_penjemput,
        ]);

        header('Location: absendanPenjemputan.php');
        exit;
    }
    ?>

    <div class="container mt-5">
        <h2>Update Data for <?= htmlspecialchars($data['nama_siswa']) ?></h2>
        <form method="POST">
            <div class="mb-3">
                <label for="nama_pengantar" class="form-label">Nama Pengantar</label>
                <input type="text" class="form-control" id="nama_pengantar" name="nama_pengantar" value="<?= htmlspecialchars($data['nama_pengantar']) ?>">
            </div>
            <div class="mb-3">
                <label for="jam_datang" class="form-label">Jam Datang</label>
                <input type="time" class="form-control" id="jam_datang" name="jam_datang" value="<?= htmlspecialchars($data['jam_datang']) ?>">
            </div>
            <div class="mb-3">
                <label for="paraf_pengantar" class="form-label">Paraf Pengantar</label>
                <input type="text" class="form-control" id="paraf_pengantar" name="paraf_pengantar" value="<?= htmlspecialchars($data['paraf_pengantar']) ?>">
            </div>
            <div class="mb-3">
                <label for="nama_penjemput" class="form-label">Nama Penjemput</label>
                <input type="text" class="form-control" id="nama_penjemput" name="nama_penjemput" value="<?= htmlspecialchars($data['nama_penjemput']) ?>">
            </div>
            <div class="mb-3">
                <label for="jam_jemput" class="form-label">Jam Jemput</label>
                <input type="time" class="form-control" id="jam_jemput" name="jam_jemput" value="<?= htmlspecialchars($data['jam_jemput']) ?>">
            </div>
            <div class="mb-3">
                <label for="paraf_penjemput" class="form-label">Paraf Penjemput</label>
                <input type="text" class="form-control" id="paraf_penjemput" name="paraf_penjemput" value="<?= htmlspecialchars($data['paraf_penjemput']) ?>">
            </div>
            <button type="submit" class="btn btn-primary">Save changes</button>
            <a href="absendanPenjemputan.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
