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
    <title>Home</title>
    <link href="../scss/custom.scss" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
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

        .btn-primary, .btn-secondary {
            padding: 5px 10px;
            font-size: 14px;
        }

        .filter-container {
            display: flex;
            align-items: center;
            gap: 10px;
        }
    </style>
</head>

<body>
    <?php
    // Sertakan file koneksi
    include '../includes/koneksi.php';

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

    // Proses pencarian berdasarkan tanggal
    $selectedDate = isset($_POST['tanggal']) ? $_POST['tanggal'] : '';

    $sql = "SELECT guru.id_guru, guru.nama, daftar_hadir_guru.id_daftarhadirguru, daftar_hadir_guru.jam_datang, daftar_hadir_guru.jam_pulang, 
            daftar_hadir_guru.tanda_tangan1, daftar_hadir_guru.date, daftar_hadir_guru.keterangan
            FROM daftar_hadir_guru
            JOIN guru ON guru.id_guru = daftar_hadir_guru.id_guru";
    
    if ($selectedDate) {
        $sql .= " WHERE daftar_hadir_guru.date = '$selectedDate'";
    }

    $sql .= " ORDER BY daftar_hadir_guru.date"; // Urutkan berdasarkan tanggal

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
                <h2 class="bg-info rounded p-4 text-white transition-bg">Daftar Hadir Guru</h2>
                
                <!-- Form untuk memilih tanggal pencarian -->
                <div class="d-flex justify-content-between mb-3">
                    <div>
                        <button class="btn btn-primary" onclick="window.location.href='create_daftar_hadir_guru.php'">Create</button>
                    </div>

                    <div class="d-flex align-items-center filter-container">
                        <form method="POST" action="" class="d-flex align-items-center">
                            <label for="tanggal" class="form-label mb-0 me-2">Pilih Tanggal:</label>
                            <input type="date" id="tanggal" name="tanggal" value="<?php echo $selectedDate; ?>" class="form-control me-2">
                            <button type="submit" class="btn btn-primary">Cari</button>
                        </form>
                        <a href="<?= $_SERVER['PHP_SELF'] ?>" class="btn btn-outline-secondary">Reset Filter</a>
                    </div>     
                </div>

                <?php if (!empty($dataByDate)) : ?>
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
                                    $checked = $row["tanda_tangan1"] == "1" ? "checked" : "";
                                    echo "<tr>";
                                    echo "<td scope='row'>" . $no . "</td>";
                                    echo "<td>" . $row["nama"] . "</td>";
                                    echo "<td>" . $row["jam_datang"] . "</td>";
                                    echo "<td>" . $row["jam_pulang"] . "</td>";
                                    echo "<td>" . $row["keterangan"] . "</td>";
                                    echo "<td>
                                        <input type='checkbox' class='form-check-input' data-id='{$row['id_daftarhadirguru']}' {$checked} disabled>
                                    </td>";
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
                <?php else : ?>
                    <p class="text-center text-danger">Tidak ada data untuk tanggal yang dipilih.</p>
                <?php endif; ?>
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
                                    $sql = "SELECT id_guru, nama FROM guru";
                                    $result = $conn->query($sql);

                                    if ($result->num_rows > 0) {
                                        while ($row = $result->fetch_assoc()) {
                                            echo "<option value='{$row['id_guru']}'>{$row['nama']}</option>";
                                        }
                                    }
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

                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="edit_tanda_tangan" name="tanda_tangan1">
                            <label class="form-check-label" for="edit_tanda_tangan">Tanda Tangan</label>
                        </div>

                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </form>
                </div>
            </div>
        </div>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            // Mengambil tombol delete dan menambahkan event listener
            const deleteButtons = document.querySelectorAll(".delete-btn");
            deleteButtons.forEach(function (btn) {
                btn.addEventListener("click", function () {
                    const id = this.getAttribute("data-id");

                    // Menggunakan SweetAlert untuk konfirmasi penghapusan
                    Swal.fire({
                        title: 'Apakah Anda yakin?',
                        text: 'Data yang dihapus tidak bisa dikembalikan!',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Ya, hapus!',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Proses penghapusan data
                            window.location.href = "delete_daftar_hadir_guru.php?id=" + id;
                        }
                    });
                });
            });
        });
    </script>
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
                                    $sql = "SELECT id_guru, nama FROM guru";
                                    $result = $conn->query($sql);

                                    if ($result->num_rows > 0) {
                                        while ($row = $result->fetch_assoc()) {
                                            echo "<option value='{$row['id_guru']}'>{$row['nama']}</option>";
                                        }
                                    }
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

                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="edit_tanda_tangan" name="tanda_tangan1">
                            <label class="form-check-label" for="edit_tanda_tangan">Tanda Tangan</label>
                        </div>

                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
                                        
    <script>
        // Menangani klik pada tombol edit
        document.addEventListener("DOMContentLoaded", function () {
            var editButtons = document.querySelectorAll('[data-bs-toggle="modal"]');
            
            editButtons.forEach(function (button) {
                button.addEventListener("click", function () {
                    // Ambil data atribut dari tombol yang dipilih
                    var id_daftarhadirguru = this.getAttribute('data-id');
                    var id_guru = this.getAttribute('data-id-guru');
                    var nama = this.getAttribute('data-nama');
                    var jam_datang = this.getAttribute('data-jam-datang');
                    var jam_pulang = this.getAttribute('data-jam-pulang');
                    var keterangan = this.getAttribute('data-keterangan');
                    var tanggal = this.getAttribute('data-tanggal');
                    var tanda_tangan = this.getAttribute('data-tanda-tangan') === "1"; // Pastikan tanda_tangan berupa boolean

                    // Isi data ke dalam form modal
                    document.getElementById('edit_id').value = id_daftarhadirguru;
                    document.getElementById('edit_id_guru').value = id_guru;
                    document.getElementById('edit_jam_datang').value = jam_datang;
                    document.getElementById('edit_jam_pulang').value = jam_pulang;
                    document.getElementById('edit_keterangan').value = keterangan;
                    document.getElementById('edit_tanggal').value = tanggal;
                    document.getElementById('edit_tanda_tangan').checked = tanda_tangan; // Set checkbox
                });
            });
        });
    </script>


    <?php
        if (isset($_GET['status']) && $_GET['status'] == 'deleted') {
            echo '<script type="text/javascript">
                window.onload = function() {
                    Swal.fire({
                        icon: "success",
                        title: "Data berhasil dihapus!",
                        showConfirmButton: false,
                        timer: 2500 // Pop-up akan hilang setelah 2.5 detik
                    });
                };
            </script>';
        }
        // Setelah pesan ditampilkan, hapus session agar tidak muncul lagi
        unset($_SESSION['deleted']);
    ?>

</body>
</html>
