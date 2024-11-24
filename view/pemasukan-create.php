<?php
ob_start(); // Mulai output buffering
session_start();

// Cek apakah pengguna sudah login
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

// Regenerasi ID sesi untuk keamanan ekstra
session_regenerate_id(true);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Tambah Data Pemasukan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <?php include 'sidebar.php';?>

            <!-- Konten Utama -->
            <main class="col-md-9 col-lg-10 ms-auto">
                <h2 class="bg-info rounded p-4 text-white transition-bg">Create Pemasukan</h2>
                
                <?php
                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                    $conn = new mysqli("localhost", "root", "", "capstone_tpa");

                    if ($conn->connect_error) {
                        die("Koneksi gagal: " . $conn->connect_error);
                    }

                    // Ambil data dari form
                    $jenis = $_POST['jenis'];
                    $deskripsi = $_POST['deskripsi'];
                    $jumlah = $_POST['jumlah'];
                    $date = $_POST['date'] . "-01";

                    // Gunakan prepared statement untuk keamanan
                    $stmt = $conn->prepare("INSERT INTO pemasukan_pengeluaran (jenis, deskripsi, jumlah, tanggal) VALUES (?, ?, ?, ?)");
                    $stmt->bind_param("ssis", $jenis, $deskripsi, $jumlah, $date);

                    if ($stmt->execute()) {
                        // Redirect ke halaman 'pemasukandanpengeluaran.php'
                        echo "<script>
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: 'Data berhasil ditambahkan!'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    window.location.href = 'pemasukan.php';
                                }
                            });
                        </script>";
                    } else {
                        echo "<script>
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal menambahkan data',
                                    text: '" . $stmt->error . "'
                                });
                              </script>";
                    }

                    $stmt->close();
                    $conn->close();
                }
                ?>

                <form method="POST" class="mb-4">
                    <div class="mb-3">
                        <label for="edit-jenis" class="form-label">Jenis</label>
                        <select name="jenis_display" class="form-control" id="edit-jenis" disabled>
                            <option value="pemasukan" selected>Pemasukan</option>
                        </select>
                        <input type="hidden" name="jenis" value="pemasukan">
                    </div>

                    <div class="mb-3">
                        <label for="deskripsi" class="form-label">Deskripsi</label>
                        <input type="text" name="deskripsi" class="form-control" id="deskripsi" placeholder="Sapu ijuk" required>
                    </div>
                    <div class="mb-3">
                        <label for="jumlah" class="form-label">Jumlah</label>
                        <input type="number" name="jumlah" class="form-control" id="jumlah" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="date" class="form-label">Bulan</label>
                        <input type="month" name="date" class="form-control" id="date" required>
                    </div>
                    <button type="submit" class="btn btn-success">Simpan Data</button>
                    <!-- <a href="pemasukandanpengeluaran.php" class="btn btn-secondary">Kembali</a> -->
                </form>
            </main>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
<?php ob_end_flush(); ?>
