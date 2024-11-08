<?php
// Koneksi ke database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "capstone_tpa"; // Ganti dengan nama database Anda

// Membuat koneksi
$conn = new mysqli($servername, $username, $password, $dbname);

// Memeriksa koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Memproses data jika form disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil data dari form
    $id_guru = $_POST['id_guru'];
    $jam_datang = $_POST['jam_datang'];
    $jam_pulang = $_POST['jam_pulang'];
    $keterangan = $_POST['keterangan'];
    $tanda_tangan = $_POST['tanda_tangan'];
    $tanggal = $_POST['tanggal'];

    // Validasi input, pastikan tidak kosong
    if (empty($id_guru) || empty($jam_datang) || empty($jam_pulang) || empty($keterangan) || empty($tanggal)) {
        echo "Semua field harus diisi.";
    } else {
        // Query untuk menyimpan data ke dalam tabel daftar_hadir_guru
        $sql = "INSERT INTO daftar_hadir_guru (id_guru, jam_datang, jam_pulang, ket, tanda_tangan1, date) 
                VALUES ('$id_guru', '$jam_datang', '$jam_pulang', '$keterangan', '$tanda_tangan', '$tanggal')";

        if ($conn->query($sql) === TRUE) {
            echo "Data berhasil disimpan!";
            // Redirect setelah data berhasil disimpan
            header("Location: daftar_hadir_guru.php"); // Ganti dengan halaman yang sesuai
            exit;
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    }
}

// Query untuk mengambil data guru
$sql = "SELECT id_guru, nama from guru"; 
$result = $conn->query($sql);

// Menutup koneksi
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Daftar Hadir Guru</title>
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

            <!-- Konten Halaman Create -->
            <main class="col-md-9 col-lg-10 ms-auto">
                <h2 class="bg-info rounded p-4 text-white transition-bg">Create daftar hadir guru</h2>
                <form action="" method="POST">
                    <!-- Dropdown Nama Guru -->
                    <div class="mb-3">
                        <label for="id_guru" class="form-label">Nama Guru</label>
                        <?php if ($result->num_rows > 0): ?>
                            <select id="id_guru" name="id_guru" class="form-select" required>
                                <option value="" disabled selected hidden>Pilih Nama Guru</option>
                                <?php
                                // Menampilkan nama guru sebagai opsi dalam dropdown
                                while ($row = $result->fetch_assoc()) {
                                    echo "<option value='" . $row['id_guru'] . "'>" . $row['nama'] . "</option>";
                                }
                                ?>
                            </select>
                        <?php else: ?>
                            <p class="text-danger">Tidak ada data guru tersedia.</p>
                            <select id="id_guru" name="id_guru" class="form-select" disabled>
                                <option value="">Tidak ada guru</option>
                            </select>
                        <?php endif; ?>
                    </div>

                    <!-- Input Jam Datang -->
                    <div class="mb-3">
                        <label for="jam_datang" class="form-label">Jam Datang</label>
                        <input type="time" class="form-control" id="jam_datang" name="jam_datang" required>
                    </div>
                    
                    <!-- Input Jam Pulang -->
                    <div class="mb-3">
                        <label for="jam_pulang" class="form-label">Jam Pulang</label>
                        <input type="time" class="form-control" id="jam_pulang" name="jam_pulang" required>
                    </div>

                    <!-- Input Keterangan -->
                    <div class="mb-3">
                        <label for="keterangan" class="form-label">Keterangan</label>
                        <input type="text" class="form-control" id="keterangan" name="keterangan" required>
                    </div>
                    <!-- Input Keterangan -->
                    <div class="mb-3">
                        <label for="tandatangan" class="form-label">Tanda tangan</label>
                        <input type="text" class="form-control" id="tanda_tangan" name="tanda tangan" required>
                    </div>

                    <!-- Input Tanggal -->
                    <div class="mb-3">
                        <label for="tanggal" class="form-label">Tanggal</label>
                        <input type="date" class="form-control" id="tanggal" name="tanggal" required>
                    </div>

                    <!-- Tombol Submit -->
                    <button type="submit" class="btn btn-primary">Submit</button>
                    <button type="button" class="btn btn-secondary" onclick="window.history.back();">Cancel</button>
                </form>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
