<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pemasukan dan Pengeluaran</title>
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
            
            <!--main utama-->
            <main class="col-md-9 col-lg-10 ms-auto" style="margin-left: auto;">
                <h2 class="bg-info rounded p-4 text-white transition-bg">Aturan Penjemputan</h2>
                <div class="container mt-5">

                    <!-- Tombol Tambah Data -->
                    <div class="mb-3">
                        <a href="pemasukandanpengeluaran-create.php" class="btn btn-success">Tambah Data</a>
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
                                <th>Pemasukan</th>
                                <th>Total Pemasukan</th>
                                <th>Pengeluaran</th>
                                <th>Total Pengeluaran</th>
                                <th>Tanggal</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                        $conn = new mysqli("localhost", "root", "", "capstone_tpa");
                        if ($conn->connect_error) {
                            die("Koneksi gagal: " . $conn->connect_error);
                        }

                        // Proses UPDATE data
                        if (isset($_POST['update'])) {
                            $id = $_POST['id'];
                            $pemasukan = $_POST['pemasukan'];
                            $total_pemasukan = $_POST['total_pemasukan'];
                            $pengeluaran = $_POST['pengeluaran'];
                            $total_pengeluaran = $_POST['total_pengeluaran'];
                            $date = $_POST['date'];

                            $sql = "UPDATE pemasukan_dan_pengeluaran SET 
                                    pemasukan = '$pemasukan', 
                                    total_pemasukan = '$total_pemasukan',
                                    pengeluaran = '$pengeluaran',
                                    total_pengeluaran = '$total_pengeluaran',
                                    date = '$date'
                                    WHERE id = '$id'";

                            if ($conn->query($sql) === TRUE) {
                                echo "<script>alert('Data berhasil diperbarui'); window.location.href = ''; </script>";
                            } else {
                                echo "Error: " . $conn->error;
                            }
                        }

                        // Fungsi DELETE
                        if (isset($_POST['delete'])) {
                            $id = $_POST['id'];
                            $sql = "DELETE FROM pemasukan_dan_pengeluaran WHERE id = '$id'";
                            $conn->query($sql);
                        }

                        // Filter data berdasarkan bulan
                        $sql = "SELECT * FROM pemasukan_dan_pengeluaran";
                        if (isset($_GET['filter_month'])) {
                            $filter_month = $_GET['filter_month'];
                            $sql .= " WHERE DATE_FORMAT(date, '%Y-%m') = '$filter_month'";
                        }

                        $result = $conn->query($sql);
                        $no = 1;
                        $totalPemasukan = 0;
                        $totalPengeluaran = 0;

                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td>" . $no++ . "</td>";
                                echo "<td>" . $row['pemasukan'] . "</td>";
                                echo "<td>" . $row['total_pemasukan'] . "</td>";
                                echo "<td>" . $row['pengeluaran'] . "</td>";
                                echo "<td>" . $row['total_pengeluaran'] . "</td>";
                                echo "<td>" . $row['date'] . "</td>";
                                echo "<td>";
                                echo "<button class='btn btn-warning' onclick='editData(" . json_encode($row) . ")'>Update</button> ";
                                echo "<form method='POST' style='display:inline;' onsubmit='return confirmDelete()'>";
                                echo "<input type='hidden' name='id' value='" . $row['id'] . "'>";
                                echo "<button type='submit' name='delete' class='btn btn-danger'>Delete</button>";
                                echo "</form>";
                                echo "</td>";
                                echo "</tr>";

                                // Hitung total pemasukan dan pengeluaran
                                $totalPemasukan += $row['total_pemasukan'];
                                $totalPengeluaran += $row['total_pengeluaran'];
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
                                <th colspan="2" class="text-end">TOTAL:</th>
                                <th><?php echo number_format($totalPemasukan, 0, ',', '.'); ?></th>
                                <th></th>
                                <th><?php echo number_format($totalPengeluaran, 0, ',', '.'); ?></th>
                                <th colspan="2"></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </main> <!-- Tag main ditutup di sini -->

        </div>
    </div>

    <script>
        function confirmDelete() {
            return confirm("Apakah Anda yakin ingin menghapus data ini?");
        }
    </script>

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
                            <label for="edit-pemasukan" class="form-label">Pemasukan</label>
                            <input type="text" name="pemasukan" class="form-control" id="edit-pemasukan" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit-total-pemasukan" class="form-label">Total Pemasukan</label>
                            <input type="number" name="total_pemasukan" class="form-control" id="edit-total-pemasukan" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit-pengeluaran" class="form-label">Pengeluaran</label>
                            <input type="text" name="pengeluaran" class="form-control" id="edit-pengeluaran" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit-total-pengeluaran" class="form-label">Total Pengeluaran</label>
                            <input type="number" name="total_pengeluaran" class="form-control" id="edit-total-pengeluaran" required>
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
            document.getElementById("edit-pemasukan").value = rowData.pemasukan;
            document.getElementById("edit-total-pemasukan").value = rowData.total_pemasukan;
            document.getElementById("edit-pengeluaran").value = rowData.pengeluaran;
            document.getElementById("edit-total-pengeluaran").value = rowData.total_pengeluaran;
            document.getElementById("edit-date").value = rowData.date;

            // Tampilkan modal
            var editModal = new bootstrap.Modal(document.getElementById("editModal"));
            editModal.show();
        }
    </script>
</body>
</html>
