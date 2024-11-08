<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Home</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.5.0/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        /* Styling Sidebar */
        .sidebar {
            background-color: #333;
            padding-top: 20px;
            height: 100vh;
        }
        .btn-outline-primary {
            color: white;
            border-color: white;
        }
        .btn-outline-primary:hover {
            color: grey;
            border-color: grey;
        }
        /* Styling for dropdown hover */
        .dropdown-toggle {
            color: white;
            cursor: pointer;
        }
        /* Show dropdown on hover, push elements down */
        .dropdown:hover .dropdown-menu {
            display: block;
            position: relative;
            margin-top: 5px;
        }
        .dropdown-menu {
            display: none;
            padding: 0;
            background-color: #444;
        }
        .dropdown-item {
            color: white;
            padding: 8px 16px;
        }
        .dropdown-item:hover {
            background-color: #555;
        }
        /* Logout button styling */
        .logout {
            color: white;
            margin-top: 10px;
        }
        .logout:hover {
            color: grey;
        }
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
                            // Koneksi ke database
                            $servername = "localhost";
                            $username = "root";
                            $password = "";
                            $dbname = "capstone_tpa";

                            $conn = new mysqli($servername, $username, $password, $dbname);

                            // Cek koneksi
                            if ($conn->connect_error) {
                                die("Koneksi gagal: " . $conn->connect_error);
                            }

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

                            $conn->close();
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
</html>
