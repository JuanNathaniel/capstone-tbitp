<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Home</title>
    <link href="../scss/custom.scss" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
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
            position: relative; /* Position relative to push content below */
            margin-top: 5px;
        }

        .dropdown-menu {
            display: none; /* Hide by default, shown on hover */
            padding: 0;
            background-color: #444; /* Background for dropdown */
        }

        .dropdown-item {
            color: white;
            padding: 8px 16px;
        }

        .dropdown-item:hover {
            background-color: #555;
        }

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

        /* Styling untuk tabel */
        table {
            width: 100%;
            border-collapse: collapse; /* Menggabungkan border tabel */
        }

        th, td {
            text-align: center;
            padding: 12px 15px;
            border: 1px solid #ddd;
        }

        table th {
            background-color: #10375C !important;
            color: white !important;
            text-align: center !important;
        }

        .table td, .table th {
            vertical-align: middle; /* Menjaga isi tabel sejajar secara vertikal */
        }

        .btn-primary {
            padding: 5px 10px;
            font-size: 14px;
        }
    </style>
</head>

<body>
    <?php
    session_start();
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
    if (isset($_SESSION['status']) && $_SESSION['status'] == 'success') {
        echo '<script type="text/javascript">
            window.onload = function() {
                Swal.fire({
                    icon: "success",
                    title: "Data berhasil diubah!",
                    showConfirmButton: false,
                    timer: 2500 // Pop-up akan hilang setelah 2.5 detik
                });
            };
        </script>';
        
        // Hapus status session setelah menampilkan SweetAlert
        unset($_SESSION['status']);
    }

    // Query untuk mengambil data
    $sql = "SELECT guru.id_guru, guru.nama, daftar_hadir_guru.id_daftarhadirguru, daftar_hadir_guru.jam_datang, daftar_hadir_guru.jam_pulang, 
            daftar_hadir_guru.tanda_tangan1, daftar_hadir_guru.date, daftar_hadir_guru.keterangan
            FROM daftar_hadir_guru
            JOIN guru ON guru.id_guru = daftar_hadir_guru.id_guru
            ORDER BY daftar_hadir_guru.date"; // Urutkan berdasarkan tanggal

    $result = $conn->query($sql);
    
    // Array untuk menyimpan data berdasarkan tanggal
    $dataByDate = [];

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $date = $row['date'];
            $dataByDate[$date][] = $row;
        }
    }
    ?>

    <div class="container-fluid">
        <div class="row">
            <?php include 'sidebar.php'; ?> <!-- Include file sidebar -->

            <!-- Konten Utama -->
            <main class="col-md-9 col-lg-10 ms-auto" style="margin-left: auto;">
                <h2 class="bg-info rounded p-4 text-white transition-bg">Daftar hadir guru</h2>
                <div class="content">
                    <div class="d-flex justify-content-between mb-3">
                        <div class="d-flex align-items-center">
                            <button class="btn btn-primary me-2" onclick="window.location.href='create_daftar_hadir_guru.php'">Create</button>
                            <form action="daftar_hadir_guru.php" method="GET" class="d-flex align-items-center">
                                <label for="filter_date" class="form-label me-2 mb-0">&nbsp;&nbsp;&nbsp;&nbsp;</label>
                                <input type="date" name="filter_date" id="filter_date" class="form-control me-2" value="<?php echo isset($_GET['filter_date']) ? $_GET['filter_date'] : ''; ?>">
                                <button type="submit" class="btn btn-primary" id="searchBtn">Search</button>
                            </form>
                        </div>
                    </div>
                </div>

                <?php foreach ($dataByDate as $date => $data) : ?>
                    <br>
                    <h5>Date : <?php echo $date; ?></h5>
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th scope="col">NO</th>
                                <th scope="col">NAMA</th>
                                <th scope="col">JAM DATANG</th>
                                <th scope="col">JAM PULANG</th>
                                <th scope="col">KET</th>
                                <th scope="col">TANDA TANGAN</th>
                                <th scope="col">ACTION</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = 1;
                            foreach ($data as $row) :
                                echo "<tr>";
                                echo "<td scope='row'>" . $no . "</td>";
                                echo "<td>" . $row["nama"] . "</td>";
                                echo "<td>" . $row["jam_datang"] . "</td>";
                                echo "<td>" . $row["jam_pulang"] . "</td>";
                                echo "<td>" . $row["keterangan"] . "</td>";
                                echo "<td>" . $row["tanda_tangan1"] . "</td>";
                                echo "<td>
                                    <button class='btn btn-warning' 
                                        data-bs-toggle='modal' 
                                        data-bs-target='#editModal' 
                                        data-id='{$row['id_daftarhadirguru']}'
                                        data-id-guru='{$row['id_guru']}'
                                        data-nama='{$row['nama']}'
                                        data-jam-datang='{$row['jam_datang']}'
                                        data-jam-pulang='{$row['jam_pulang']}'
                                        data-keterangan='{$row['keterangan']}'
                                        data-tanggal='{$row['date']}'>Edit</button>&nbsp;&nbsp;
                                    <button class='btn btn-danger delete-btn' data-id='{$row['id_daftarhadirguru']}'>Delete</button>
                                </td>";
                                echo "</tr>";
                                $no++;
                            endforeach;
                            ?>
                        </tbody>
                    </table>
                <?php endforeach; ?>
            </main>
            
        </div>
    </div>

    <!-- Modal Edit Daftar Hadir Guru -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Edit Daftar Hadir Guru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="edit_daftar_hadir_guru.php" method="POST">
                        <input type="hidden" id="edit_id" name="id_daftar_hadir_guru">
                        
                        <div class="mb-3">
                            <label for="edit_id_guru" class="form-label">Nama Guru</label>
                            <select id="edit_id_guru" name="id_guru" class="form-select" required>
                                <?php
                                    // Koneksi ke database untuk menampilkan daftar nama guru
                                    $conn = new mysqli($servername, $username, $password, $dbname);
                                    $sql = "SELECT id_guru, nama FROM guru";
                                    $result = $conn->query($sql);

                                    if ($result->num_rows > 0) {
                                        while ($row = $result->fetch_assoc()) {
                                            echo "<option value='{$row['id_guru']}'>{$row['nama']}</option>";
                                        }
                                    }
                                    $conn->close();
                                ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="edit_jam_datang" class="form-label">Jam Datang</label>
                            <input type="time" class="form-control" id="edit_jam_datang" name="jam_datang" required>
                        </div>

                        <div class="mb-3">
                            <label for="edit_jam_pulang" class="form-label">Jam Pulang</label>
                            <input type="time" class="form-control" id="edit_jam_pulang" name="jam_pulang" required>
                        </div>

                        <div class="mb-3">
                            <label for="edit_keterangan" class="form-label">Keterangan</label>
                            <input type="text" class="form-control" id="edit_keterangan" name="keterangan" required>
                        </div>

                        <div class="mb-3">
                            <label for="edit_tanggal" class="form-label">Tanggal</label>
                            <input type="date" class="form-control" id="edit_tanggal" name="tanggal" required>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- JS Script -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Menangani ketika tombol edit diklik
        const editButtons = document.querySelectorAll('[data-bs-toggle="modal"][data-bs-target="#editModal"]');
        
        editButtons.forEach(button => {
            button.addEventListener('click', function () {
                // Ambil data dari atribut data-*
                const idDaftarHadirGuru = this.getAttribute('data-id');
                const namaGuru = this.getAttribute('data-nama');
                const jamDatang = this.getAttribute('data-jam-datang');
                const jamPulang = this.getAttribute('data-jam-pulang');
                const keterangan = this.getAttribute('data-keterangan');
                const tanggal = this.getAttribute('data-tanggal');
                
                // Isi form modal dengan data
                document.getElementById('edit_id').value = idDaftarHadirGuru;
                document.getElementById('edit_id_guru').value = this.getAttribute('data-id-guru');
                document.getElementById('edit_jam_datang').value = jamDatang;
                document.getElementById('edit_jam_pulang').value = jamPulang;
                document.getElementById('edit_keterangan').value = keterangan;
                document.getElementById('edit_tanggal').value = tanggal;
            });
        });


        // Menangani ketika tombol delete ditekan
        const deleteButtons = document.querySelectorAll('.delete-btn');
    
        deleteButtons.forEach(button => {
            button.addEventListener('click', function () {
                const idToDelete = this.getAttribute('data-id'); // Ambil ID yang akan dihapus
            
                // Tampilkan konfirmasi SweetAlert2
                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: "Data ini akan dihapus secara permanen!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, hapus!',
                    cancelButtonText: 'No, batalkan!',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Jika user memilih Yes, lakukan penghapusan
                        window.location.href = 'delete_daftar_hadir_guru.php?id_daftarhadirguru=' + idToDelete;
                    }
                });
            });
        });
    </script>
    
    <!-- Link untuk SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</body>

</html>
