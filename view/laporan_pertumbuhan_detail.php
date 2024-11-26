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
// $dbname = 'capstone_tpa'; // Ganti dengan nama database Anda
// $conn = new mysqli($host, $username, $password, $dbname);

// if ($conn->connect_error) {
//     die("Connection failed: " . $conn->connect_error);
// }
// Sertakan file koneksi
include '../includes/koneksi.php';

// Mendapatkan id_anak dari URL
$id_anak = $_GET['id_anak'];

// Mendapatkan data anak
$queryAnak = "SELECT * FROM anak WHERE id = '$id_anak'";
$anakData = $conn->query($queryAnak)->fetch_assoc();

// Menampilkan laporan pertumbuhan anak berdasarkan id_anak
$queryLaporan = "SELECT * FROM laporan_pertumbuhan_anak_didik WHERE id_anak = '$id_anak'";
$laporanData = $conn->query($queryLaporan);

// Menangani operasi CRUD laporan pertumbuhan anak (create, update, delete) dengan AJAX
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_laporan'])) {
        // Insert new report logic
        $date = $_POST['date'];
        $nilai_moral_dan_agama = $_POST['nilai_moral_dan_agama'];
        $fisik_motorik_kasar = $_POST['fisik_motorik_kasar'];
        $fisik_motorik_halus = $_POST['fisik_motorik_halus'];
        $kognitif = $_POST['kognitif'];
        $bahasa = $_POST['bahasa'];
        $sosial_emosional = $_POST['sosial_emosional'];
        $semester = $_POST['semester'];

        $sql = "INSERT INTO laporan_pertumbuhan_anak_didik (id_anak, date, nilai_moral_dan_agama, fisik_motorik_kasar, fisik_motorik_halus, kognitif, bahasa, sosial_emosional, semester)
                VALUES ('$id_anak', '$date', '$nilai_moral_dan_agama', '$fisik_motorik_kasar', '$fisik_motorik_halus', '$kognitif', '$bahasa', '$sosial_emosional', '$semester')";
        $conn->query($sql);
    }

    if (isset($_POST['update_laporan'])) {
    $id = $_POST['id'];
    $date = $_POST['date'];
    $nilai_moral_dan_agama = $_POST['nilai_moral_dan_agama'];
    $fisik_motorik_kasar = $_POST['fisik_motorik_kasar'];
    $fisik_motorik_halus = $_POST['fisik_motorik_halus'];
    $kognitif = $_POST['kognitif'];
    $bahasa = $_POST['bahasa'];
    $sosial_emosional = $_POST['sosial_emosional'];
    $semester = $_POST['semester'];

    $sql = "UPDATE laporan_pertumbuhan_anak_didik SET 
                date='$date', nilai_moral_dan_agama='$nilai_moral_dan_agama', 
                fisik_motorik_kasar='$fisik_motorik_kasar', fisik_motorik_halus='$fisik_motorik_halus', 
                kognitif='$kognitif', bahasa='$bahasa', sosial_emosional='$sosial_emosional' , semester='$semester' 
            WHERE id='$id'";
        $conn->query($sql);
    
}


    if (isset($_POST['delete_laporan'])) {
    $id = $_POST['id'];
    $sql = "DELETE FROM laporan_pertumbuhan_anak_didik WHERE id='$id'";
    $conn->query($sql);
}

}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Pertumbuhan Anak</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  
  <style>
    table {
    width: 100%;
    table-layout: fixed;

    .transition-bg {
            background: linear-gradient(to right, #344EAD, #1767A6); /* Gradasi horizontal */
        }
}

th, td {
    padding: 15px;
    text-align: left;
}

th {
    background-color: #4CAF50;
    color: white;
    border: 1px solid #ddd;
}

td {
    border: 1px solid #ddd;
    background-color: #f9f9f9;
}

th:first-child, td:first-child {
    width: 5%;
}

th:nth-child(2), td:nth-child(2) {
    width: 15%;
}

th:nth-child(3), td:nth-child(3) {
    width: 20%;
}

th:nth-child(4), td:nth-child(4) {
    width: 40%;
}

th:last-child, td:last-child {
    width: 20%;
}

tr:hover {
    background-color: #e0e0e0;
}

.modal-content {
    background-color: #f8f9fa;
}

.btn-sm {
    font-size: 0.875rem;
    padding: 5px 10px;
}

.modal-header {
    background-color: #007bff;
    color: white;
}

.btn-secondary {
    padding: 8px 20px;
    font-size: 14px;
}

.modal-footer .btn {
    padding: 8px 15px;
}

.table-responsive {
    overflow-x: auto;
}

/* Responsif untuk perangkat kecil */
@media (max-width: 768px) {
    table {
        font-size: 12px;
    }
    th, td {
        padding: 10px;
    }
    .modal-content {
        width: 100%;
        margin: 0;
    }
}
</style>

</head>
<body>
<div class="container-fluid">
        <div class="row">
            <?php include 'sidebar.php'; ?> <!-- Include file sidebar -->

            <!-- Konten Utama -->
            <main class="col-md-9 col-lg-10 ms-auto" style="margin-left: auto;">
                <h2 class="bg-info rounded p-4 text-white transition-bg">Laporan Pertumbuhan Anak</h2>

    <a href="laporan_pertumbuhan_anak.php" class="btn btn-secondary mb-3">Kembali ke Daftar Anak</a>
    
    <!-- Menampilkan data anak di atas tabel -->
    <h3>Data Anak: <?= $anakData['nama'] ?> (<?= $anakData['usia'] ?> Tahun)</h3>
    
    <!-- Tombol untuk membuka form pop-up -->
    <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addLaporanModal">Tambah Laporan</button>
    
    <!-- Modal untuk form tambah laporan -->
    <div class="modal fade" id="addLaporanModal" tabindex="-1" aria-labelledby="addLaporanModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addLaporanModalLabel">Tambah Laporan Pertumbuhan Anak</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" id="addLaporanForm">
                    <div class="modal-body">
                        <input type="hidden" name="id_anak" value="<?= $id_anak ?>">
                        <div class="mb-3">
                            <label>Date</label>
                            <input type="date" name="date" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label>Nilai Moral dan Agama</label>
                            <input type="text" name="nilai_moral_dan_agama" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label>Fisik Motorik Kasar</label>
                            <input type="text" name="fisik_motorik_kasar" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label>Fisik Motorik Halus</label>
                            <input type="text" name="fisik_motorik_halus" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label>Kognitif</label>
                            <input type="text" name="kognitif" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label>Bahasa</label>
                            <input type="text" name="bahasa" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label>Social Emosional</label>
                            <input type="text" name="sosial_emosional" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label>Semester</label>
                            <input type="int" name="semester" class="form-control">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        <button type="submit" name="add_laporan" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Tabel Laporan Pertumbuhan -->
    <h3 class="mt-5">Daftar Laporan Pertumbuhan</h3>
    <table class="table">
        <thead>
            <tr>
                <th>No</th>
                <th>Date</th>
                <th>Aspek Perkembangan</th>
                <th>Hasil Perkembangan</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $no = 1;
            while ($laporan = $laporanData->fetch_assoc()): ?>
                <!-- Baris utama untuk tanggal dan tombol tindakan -->
                <tr>
                    <td rowspan="6"><?= $no++ ?></td>
                    <td rowspan="6"><?= $laporan['date'] ?> <br> Semester <?= $laporan['semester'] ?></td>
                    <td>Nilai Moral dan Agama</td>
                    <td><?= $laporan['nilai_moral_dan_agama'] ?></td>
                    <td rowspan="6">
                        <!-- Tombol Update Laporan -->
                        <button class="btn btn-warning btn-sm updateLaporanBtn" 
                            data-id="<?= $laporan['id'] ?>" data-date="<?= $laporan['date'] ?>"
                            data-nilai_moral="<?= $laporan['nilai_moral_dan_agama'] ?>" 
                            data-fisik_kasar="<?= $laporan['fisik_motorik_kasar'] ?>"
                            data-fisik_halus="<?= $laporan['fisik_motorik_halus'] ?>" 
                            data-kognitif="<?= $laporan['kognitif'] ?>" 
                            data-bahasa="<?= $laporan['bahasa'] ?>" 
                            data-sosial="<?= $laporan['sosial_emosional'] ?>">Update</button>
                        
                        <!-- Tombol Delete Laporan -->
                        <button class="btn btn-danger btn-sm deleteLaporanBtn" 
                            data-id="<?= $laporan['id'] ?>">Delete</button>
                    </td>
                </tr>
                <tr>
                    <td>Fisik Motorik Kasar</td>
                    <td><?= $laporan['fisik_motorik_kasar'] ?></td>
                </tr>
                <tr>
                    <td>Fisik Motorik Halus</td>
                    <td><?= $laporan['fisik_motorik_halus'] ?></td>
                </tr>
                <tr>
                    <td>Kognitif</td>
                    <td><?= $laporan['kognitif'] ?></td>
                </tr>
                <tr>
                    <td>Bahasa</td>
                    <td><?= $laporan['bahasa'] ?></td>
                </tr>
                <tr>
                    <td>Social Emosional</td>
                    <td><?= $laporan['sosial_emosional'] ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<!-- Modal untuk update laporan -->
<div class="modal fade" id="updateLaporanModal" tabindex="-1" aria-labelledby="updateLaporanModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="updateLaporanModalLabel">Update Laporan Pertumbuhan Anak</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" id="updateLaporanForm">
                <div class="modal-body">
                    <input type="hidden" name="id" id="updateLaporanId">
                    <input type="hidden" name="id_anak" value="<?= $id_anak ?>">

                    <div class="mb-3">
                        <label>Date</label>
                        <input type="date" name="date" id="updateDate" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label>Nilai Moral dan Agama</label>
                        <input type="text" name="nilai_moral_dan_agama" id="updateNilaiMoral" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label>Fisik Motorik Kasar</label>
                        <input type="text" name="fisik_motorik_kasar" id="updateFisikKasar" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label>Fisik Motorik Halus</label>
                        <input type="text" name="fisik_motorik_halus" id="updateFisikHalus" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label>Kognitif</label>
                        <input type="text" name="kognitif" id="updateKognitif" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label>Bahasa</label>
                        <input type="text" name="bahasa" id="updateBahasa" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label>Social Emosional</label>
                        <input type="text" name="sosial_emosional" id="updateSosial" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label>Semester</label>
                        <input type="int" name="semester" id="updateSemester" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" name="update_laporan" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        // Update Laporan Button Click
        $(".updateLaporanBtn").click(function() {
            var id = $(this).data("id");
            var date = $(this).data("date");
            var nilai_moral = $(this).data("nilai_moral");
            var fisik_kasar = $(this).data("fisik_kasar");
            var fisik_halus = $(this).data("fisik_halus");
            var kognitif = $(this).data("kognitif");
            var bahasa = $(this).data("bahasa");
            var sosial = $(this).data("sosial");
            var semester = $(this).data("semester");

            // Set modal input values
            $("#updateLaporanId").val(id);
            $("#updateDate").val(date);
            $("#updateNilaiMoral").val(nilai_moral);
            $("#updateFisikKasar").val(fisik_kasar);
            $("#updateFisikHalus").val(fisik_halus);
            $("#updateKognitif").val(kognitif);
            $("#updateBahasa").val(bahasa);
            $("#updateSosial").val(sosial);
            $("#updateSemester").val(semester);

            // Show modal
            $("#updateLaporanModal").modal('show');
        });

        // Submit form update laporan
$("#updateLaporanForm").submit(function(e) {
    e.preventDefault();
    
    $.ajax({
        type: 'POST',
        url: '', // Kirim ke halaman yang sama
        data: $(this).serialize() + "&update_laporan=true",
        success: function(response) {
            alert("Laporan berhasil diupdate!");
            location.reload();  // Refresh page setelah update
        },
        error: function(xhr, status, error) {
            console.error("Error: " + error);
        }
    });
});


        // Delete Laporan Button Click
        $(".deleteLaporanBtn").click(function() {
            var id = $(this).data("id");
            
            if (confirm("Apakah Anda yakin ingin menghapus laporan ini?")) {
                $.ajax({
                    type: 'POST',
                    url: '',  // Kirim ke halaman yang sama
                    data: {
                        delete_laporan: true,
                        id: id
                    },
                    success: function(response) {
                        alert("Laporan berhasil dihapus!");
                        location.reload();  // Refresh page setelah delete
                    },
                    error: function(xhr, status, error) {
                        console.error("Error: " + error);
                    }
                });
            }
        });

        // Submit form tambah laporan
        $("#addLaporanForm").submit(function(e) {
            e.preventDefault();

            $.ajax({
                type: 'POST',
                url: '',  // Kirim ke halaman yang sama
                data: $(this).serialize() + "&add_laporan=true",
                success: function(response) {
                    alert("Laporan berhasil ditambahkan!");
                    location.reload();  // Refresh page setelah tambah
                },
                error: function(xhr, status, error) {
                    console.error("Error: " + error);
                }
            });
        });
    });
</script>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
