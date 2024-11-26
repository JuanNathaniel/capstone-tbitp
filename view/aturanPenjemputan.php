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

// Koneksi ke database
// $servername = "localhost";
// $username = "root";
// $password = "";
// $dbname = "capstone_tpa";

// $conn = new mysqli($servername, $username, $password, $dbname);

// // Cek koneksi
// if ($conn->connect_error) {
//     die("Koneksi gagal: " . $conn->connect_error);
// }

// Sertakan file koneksi
include '../includes/koneksi.php';


if (isset($_POST['inputTerlambat'])) {
    $siswaId = $_POST['nama'];
    $keterlambatanId = $_POST['keterlambatan'];

    // Ambil detail charge dari tabel aturan_penjemputan
    $sqlDetail = "SELECT charge FROM aturan_penjemputan WHERE id = '$keterlambatanId'";
    $resultDetail = $conn->query($sqlDetail);

    if ($resultDetail->num_rows > 0) {
        $detail = $resultDetail->fetch_assoc();
        $charge = $detail["charge"]; // Nilai charge yang diambil

        // Periksa apakah data anak sudah ada di tabel laporan_dana
        $sqlCheck = "SELECT keterlambatan FROM laporan_dana WHERE nama = '$siswaId'";
        $resultCheck = $conn->query($sqlCheck);

        if ($resultCheck->num_rows > 0) {
            // Data anak ditemukan, update kolom keterlambatan
            $sqlUpdate = "UPDATE laporan_dana 
                          SET keterlambatan = keterlambatan + '$charge' 
                          WHERE nama = '$siswaId'";
            if ($conn->query($sqlUpdate) === TRUE) {
                $_SESSION['message'] = 'Data keterlambatan berhasil diperbarui!';
                header("Location: " . $_SERVER['PHP_SELF']);
                exit(); // Pastikan skrip berhenti setelah redirect
            } else {
                echo "<script>alert('Terjadi kesalahan saat memperbarui data: " . $conn->error . "');</script>";
            }
        } else {
            // Data anak tidak ditemukan
            echo "<script>alert('Data anak tidak ditemukan di laporan_dana');</script>";
        }
    } else {
        echo "<script>alert('Detail keterlambatan tidak ditemukan');</script>";
    }
}

if (isset($_SESSION['message'])) {
    echo "<script>alert('" . $_SESSION['message'] . "');</script>";
    unset($_SESSION['message']); // Hapus pesan setelah ditampilkan
}

?>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Home</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.5.0/font/bootstrap-icons.min.css" rel="stylesheet">
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
                <h2 class="bg-info rounded p-4 text-white transition-bg">Aturan Penjemputan</h2>
                <div class="container-fluid">
                    <a href="aturanPenjemputan_pdf.php" class="btn btn-success">Download PDF</a>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#inputModal">Input Data</button>

                    <table class="table table-bordered table-striped">
                        <thead class="thead-dark">
                            <tr>
                                <th>No</th>
                                <th>Waktu Keterlambatan Penjemputan</th>
                                <th>Charge</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php

                            // Check if delete or edit button is clicked
                            if (isset($_POST['delete'])) {
                                $id = $_POST['id'];
                                $sql = "DELETE FROM aturan_penjemputan WHERE id = '$id'";
                                if ($conn->query($sql) === TRUE) {
                                    echo "Record berhasil dihapus!";
                                } else {
                                    echo "Error: " . $sql . "<br>" . $conn->error;
                                }
                            }

                            if (isset($_POST['edit'])) {
                                $id = $_POST['id'];
                                $waktu = $_POST['waktu'];
                                $charge = $_POST['charge'];
                                $sql = "UPDATE aturan_penjemputan SET waktu_keterlambatan_penjemputan = '$waktu', charge = '$charge' WHERE id = '$id'";
                                if ($conn->query($sql) === TRUE) {
                                    echo "Record berhasil diperbarui!";
                                } else {
                                    echo "Error: " . $sql . "<br>" . $conn->error;
                                }
                            }

                            // Query untuk mengambil data dari tabel aturan_penjemputan
                            $sql = "SELECT id, waktu_keterlambatan_penjemputan, charge FROM aturan_penjemputan";
                            $result = $conn->query($sql);

                            // Variabel counter untuk nomor urut
                            $no = 1;

                            // Cek apakah ada data
                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    echo "<tr>";
                                    echo "<td>" . $no++ . "</td>";
                                    echo "<td>" . $row["waktu_keterlambatan_penjemputan"] . "</td>";
                                    echo "<td>" . $row["charge"] . "</td>";
                                    echo "<td>";
                                    echo "<form method='POST' style='display:inline;'>";

                                    // Menambahkan ID tersembunyi di form
                                    echo "<input type='hidden' name='id' value='" . $row["id"] . "'>";
                                    
                                    // Tombol Edit yang memunculkan modal
                                    echo "<button type='button' class='btn btn-sm btn-primary me-2' onclick='editRow(" . json_encode($row) . ")'>Edit</button>";
                                    // Tombol Delete
                                    echo "<button type='submit' name='delete' class='btn btn-sm btn-danger'>Delete</button>";
                                    echo "</form>";
                                    echo "</td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='4' class='text-center'>Tidak ada data</td></tr>";
                            }

                            ?>
                        </tbody>
                    </table>
                </div>
            </main>
        </div>
    </div>

    <!-- Modal for Edit -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editModalLabel">Edit Aturan Penjemputan</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="id" id="edit-id">
                        <div class="mb-3">
                            <label for="edit-waktu" class="form-label">Waktu Keterlambatan Penjemputan</label>
                            <input type="text" class="form-control" id="edit-waktu" name="waktu" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit-charge" class="form-label">Charge</label>
                            <input type="text" class="form-control" id="edit-charge" name="charge" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="edit" class="btn btn-primary">Save changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal for Input -->
<div class="modal fade" id="inputModal" tabindex="-1" aria-labelledby="inputModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header">
                    <h5 class="modal-title" id="inputModalLabel">Input Data Terlambat</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                        <div class="mb-3">
                            <label for="nama" class="form-label">Nama</label>
                            <select name="nama" id="nama" class="form-control" required>
                                <option value="">Pilih Nama</option>
                                <?php
                                $result = $conn->query("SELECT id, nama FROM anak");
                                while ($row = $result->fetch_assoc()) {
                                    echo "<option value='" . $row['id'] . "'>" . $row['nama'] . "</option>";
                                }
                                ?>
                            </select>
                        </div>
                    <div class="mb-3">
                        <label for="keterlambatan" class="form-label">Pilih Keterlambatan</label>
                        <select class="form-control" id="keterlambatan" name="keterlambatan" required>
                            <?php
                            // Ambil data keterlambatan dari tabel aturan_penjemputan
                            $sqlKeterlambatan = "SELECT id, waktu_keterlambatan_penjemputan, charge FROM aturan_penjemputan";
                            $resultKeterlambatan = $conn->query($sqlKeterlambatan);
                            if ($resultKeterlambatan->num_rows > 0) {
                                while ($row = $resultKeterlambatan->fetch_assoc()) {
                                    echo "<option value='" . $row["id"] . "'>" . $row["waktu_keterlambatan_penjemputan"] . " - Rp " . $row["charge"] . "</option>";
                                }
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="inputTerlambat" class="btn btn-primary">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>

    <!-- JavaScript to trigger modal and populate fields -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function editRow(rowData) {
            // Set modal fields with row data
            document.getElementById('edit-id').value = rowData.id;
            document.getElementById('edit-waktu').value = rowData.waktu_keterlambatan_penjemputan;
            document.getElementById('edit-charge').value = rowData.charge;
            // Show the modal
            var myModal = new bootstrap.Modal(document.getElementById('editModal'));
            myModal.show();
        }
    </script>
</body>
<?php
                            $conn->close();
?>
</html>
