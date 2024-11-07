<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Tambah Data</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <h2 class="text-center mb-4">Tambah Data Pemasukan dan Pengeluaran</h2>

        <?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $conn = new mysqli("localhost", "root", "", "capstone_tpa");

    if ($conn->connect_error) {
        die("Koneksi gagal: " . $conn->connect_error);
    }

    $pemasukan = $_POST['pemasukan'];
    $total_pemasukan = $_POST['total_pemasukan'];
    $pengeluaran = $_POST['pengeluaran'];
    $total_pengeluaran = $_POST['total_pengeluaran'];
    $date = $_POST['date'] . "-01"; // Menambahkan tanggal 01

    $sql = "INSERT INTO pemasukan_dan_pengeluaran (pemasukan, total_pemasukan, pengeluaran, total_pengeluaran, date) 
            VALUES ('$pemasukan', '$total_pemasukan', '$pengeluaran', '$total_pengeluaran', '$date')";

    if ($conn->query($sql) === TRUE) {
        echo "<div class='alert alert-success'>Data berhasil ditambahkan.</div>";
    } else {
        echo "<div class='alert alert-danger'>Gagal menambahkan data: " . $conn->error . "</div>";
    }

    $conn->close();
}
?>


        <form method="POST" class="mb-4">
            <div class="mb-3">
                <label for="pemasukan" class="form-label">Pemasukan</label>
                <input type="text" name="pemasukan" class="form-control" id="pemasukan" placeholder="Pemasukan" required>
            </div>
            <div class="mb-3">
                <label for="total_pemasukan" class="form-label">Total Pemasukan</label>
                <input type="number" name="total_pemasukan" class="form-control" id="total_pemasukan" placeholder="Total Pemasukan" required>
            </div>
            <div class="mb-3">
                <label for="pengeluaran" class="form-label">Pengeluaran</label>
                <input type="text" name="pengeluaran" class="form-control" id="pengeluaran" placeholder="Pengeluaran" required>
            </div>
            <div class="mb-3">
                <label for="total_pengeluaran" class="form-label">Total Pengeluaran</label>
                <input type="number" name="total_pengeluaran" class="form-control" id="total_pengeluaran" placeholder="Total Pengeluaran" required>
            </div>
            <div class="mb-3">
                <label for="date" class="form-label">Bulan</label>
                <input type="month" name="date" class="form-control" id="date" required>
            </div>
            <button type="submit" class="btn btn-success">Simpan Data</button>
            <a href="pemasukandanpengeluaran.php" class="btn btn-secondary">Kembali</a>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
