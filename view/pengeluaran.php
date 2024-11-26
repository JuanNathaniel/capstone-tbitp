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
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pengeluaran</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .transition-bg {
            background: linear-gradient(to right, #344EAD, #1767A6); /* Gradasi horizontal */
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
<div class="container-fluid">
        <div class="row">
            <?php include 'sidebar.php'; ?> <!-- Include file sidebar -->
            
            <!--main utama-->
            <main class="col-md-9 col-lg-10 ms-auto" style="margin-left: auto;">
                <h2 class="bg-info rounded p-4 text-white transition-bg">Pengeluaran Only </h2>
                <div class="container mt-5">

                    <!-- Tombol Tambah Data -->
                    <div class="mb-3">
                        <a href="pengeluaran-create.php" class="btn btn-success">Tambah Data</a>
                        <!-- <a href="pengeluaran_pdf.php" class="btn btn-success">Download PDF</a> -->
                    </div>

                    <!-- Form untuk filter data berdasarkan bulan -->
                    <form method="GET" class="row mb-4">
                        <div class="col-md-6">
                            <input type="month" name="filter_month" class="form-control" placeholder="Bulan" required>
                        </div>
                        <div class="col-md-6">
                            <button type="submit" class="btn btn-primary">Filter</button>
                        </div>
                    </form>

                    <!-- Tabel data pemasukan dan pengeluaran -->
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Jenis Transaksi</th>
                                <th>Deskripsi</th>
                                <th>Jumlah</th>
                                <th>Tanggal</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                            // $conn = new mysqli("localhost", "root", "", "capstone_tpa");
                            // if ($conn->connect_error) {
                            //     die("Koneksi gagal: " . $conn->connect_error);
                            // }
                            include '../includes/koneksi.php';

                            // Proses UPDATE data
                            if (isset($_POST['update'])) {
                                $id = $_POST['id'];
                                $jenis = $_POST['jenis'];
                                $deskripsi = $_POST['deskripsi'];
                                $jumlah = $_POST['jumlah'];
                                $date = $_POST['date'];

                                $sql = "UPDATE pemasukan_pengeluaran SET 
                                        jenis = '$jenis', 
                                        deskripsi = '$deskripsi',
                                        jumlah = '$jumlah',
                                        tanggal = '$date'
                                        WHERE id = '$id'";

                                if ($conn->query($sql) === TRUE) {
                                    echo "<script>
                                        Swal.fire({
                                            icon: 'success',
                                            title: 'Berhasil',
                                            text: 'Data berhasil diperbarui'
                                        }).then((result) => {
                                            if (result.isConfirmed) {
                                                window.location.href = '';
                                            }
                                        });
                                    </script>";
                                } else {
                                    echo "Error: " . $conn->error;
                                }
                            }

                            // Fungsi DELETE
                            if (isset($_POST['delete'])) {
                                $id = $_POST['id'];
                                $stmt = $conn->prepare("DELETE FROM pemasukan_pengeluaran WHERE id = ?");
                                $stmt->bind_param("i", $id);
                                if ($stmt->execute()) {
                                    echo "<script>Swal.fire({icon: 'success', title: 'Berhasil', text: 'Data berhasil dihapus'}).then((result) => {if (result.isConfirmed) {window.location.href = '';}});</script>";
                                } else {
                                    echo "Error: " . $conn->error;
                                }
                                $stmt->close();
                            }
                            

                            // Filter data berdasarkan bulan
                            $sql = "SELECT * FROM pemasukan_pengeluaran where jenis = 'pengeluaran'";
                            if (isset($_GET['filter_month'])) {
                                $filter_month = $_GET['filter_month'];
                                $sql .= " WHERE DATE_FORMAT(tanggal, '%Y-%m') = '$filter_month'";
                            }

                            $result = $conn->query($sql);
                            $no = 1;
                            $totalJumlah = 0;

                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    echo "<tr>";
                                    echo "<td>" . $no++ . "</td>";
                                    echo "<td>" . $row['jenis'] . "</td>";
                                    echo "<td>" . $row['deskripsi'] . "</td>";
                                    echo "<td>" . $row['jumlah'] . "</td>";
                                    echo "<td>" . $row['tanggal'] . "</td>";
                                    echo "<td>";
                                    echo "<button class='btn btn-warning' onclick='editData(" . json_encode($row) . ")'>Update</button> ";
                                    echo "<form method='POST' style='display:inline;' onSubmit='return confirmDelete(this)'>";
                                    echo "<input type='hidden' name='id' value='" . $row['id'] . "'>";
                                    echo "<button type='submit' name='delete' class='btn btn-danger'>Delete</button>";
                                    echo "</form>";
                                    echo "</td>";
                                    echo "</tr>";

                                    // Hitung total pemasukan dan pengeluaran
                                    $totalJumlah += $row['jumlah'];
                                }
                            } else {
                                echo "<tr><td colspan='7' class='text-center'>Tidak ada data</td></tr>";
                            }
                            $conn->close();
                        ?>
                        </tbody>
                        <!-- Menampilkan Total Pemasukan dan Pengeluaran -->
                        <tfoot>
                            <tr>
                                <th colspan="3" class="text-end">TOTAL:</th>
                                <th><?php echo number_format($totalJumlah, 0, ',', '.'); ?></th>
                                <th></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </main> <!-- Tag main ditutup di sini -->

        </div>
    </div>

    <!-- Modal Update Data -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editModalLabel">Edit Data</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="id" id="edit-id">
                        <div class="mb-3">
                            <label for="edit-jenis" class="form-label">Jenis</label>
                            <select name="jenis" class="form-control" id="edit-jenis" required disabled>
                                <option value="pengeluaran">pengeluaran</option>
                            </select>
                            <input type="hidden" name="jenis" value="pengeluaran">
                        </div>

                        <div class="mb-3">
                            <label for="edit-deskripsi" class="form-label">Deskripsi</label>
                            <input type="text" name="deskripsi" class="form-control" id="edit-deskripsi" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit-jumlah" class="form-label">Jumlah</label>
                            <input type="number" name="jumlah" class="form-control" id="edit-jumlah" required>
                        </div>       
                        <div class="mb-3">
                            <label for="edit-date" class="form-label">Tanggal</label>
                            <input type="date" name="date" class="form-control" id="edit-date" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" name="update" class="btn btn-primary">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function editData(rowData) {
            // Isi form di modal dengan data yang akan diedit
            document.getElementById("edit-id").value = rowData.id;
            document.getElementById("edit-jenis").value = rowData.jenis;
            document.getElementById("edit-deskripsi").value = rowData.deskripsi;
            document.getElementById("edit-jumlah").value = rowData.jumlah;
            document.getElementById("edit-date").value = rowData.tanggal;

            // Tampilkan modal
            var editModal = new bootstrap.Modal(document.getElementById("editModal"));
            editModal.show();
        }
    </script>
    <!-- <script>
        function confirmDelete(form) {
            Swal.fire({
                title: 'Konfirmasi Hapus',
                text: "Apakah Anda yakin ingin menghapus data ini?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit(); // Kirim formulir jika pengguna mengonfirmasi
                }
            });
            return false; // Cegah pengiriman formulir secara langsung
        }
    </script> -->

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</body>
</html>
