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

        .btn-primary {
            padding: 5px 10px;
            font-size: 14px;
        }

        .status-pending {
            background-color: yellow !important;
            color: black !important;
            text-align: center !important;
            padding: 5px !important;
            border-radius: 5px !important;
        }
        .status-approved {
            background-color: green !important;
            color: white !important;
            text-align: center !important;
            padding: 5px !important;
            border-radius: 5px !important;
        }
        .status-rejected {
            background-color: red !important;
            color: white !important;
            text-align: center !important;
            padding: 5px !important;
            border-radius: 5px !important;
        }


    </style>
</head>

<body>
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
    if (isset($_SESSION['status']) && $_SESSION['status'] == 'success2') {
        echo '<script type="text/javascript">
            window.onload = function() {
                Swal.fire({
                    icon: "success",
                    title: "Data berhasil dibuat!",
                    showConfirmButton: false,
                    timer: 2500 // Pop-up akan hilang setelah 2.5 detik
                });
            };
        </script>';
        
        // Hapus status session setelah menampilkan SweetAlert
        unset($_SESSION['status']);
    }

    // Query untuk mengambil data
    $sql = "SELECT * FROM `rencana_kegiatan_anggaran`";

    $result = $conn->query($sql);

    ?>
    <?php
    // Nama directory tempat file akan disimpan
    $uploadDir = '../uploads/rencana_kegiatan_anggaran/';

    // Cek apakah directory sudah ada
    if (!is_dir($uploadDir)) {
        // Jika belum ada, buat directory dengan izin akses
        mkdir($uploadDir, 0777, true);
    }

    ?>

    <div class="container-fluid">
        <div class="row">
            <?php include 'sidebar.php'; ?> <!-- Include file sidebar -->

            <!-- Konten Utama -->
            <main class="col-md-9 col-lg-10 ms-auto" style="margin-left: auto;">
                <h2 class="bg-info rounded p-4 text-white transition-bg">Rencana Kegiatan Anggaran</h2>
                <div class="content">
                    <div class="d-flex justify-content-between mb-3">
                        <div class="d-flex align-items-center">
                            <button class="btn btn-primary me-2" data-bs-toggle="modal" data-bs-target="#createModal">Create</button>
                        </div>
                    </div>
                </div>

                <br>
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th scope="col">NO</th>
                            <th scope="col">NAMA DOKUMEN</th>
                            <th scope="col">TAHUN ANGGARAN</th>
                            <th scope="col">PENGUMPULAN DOKUMEN</th>
                            <th scope="col">KETERANGAN</th>
                            <th scope="col">STATUS</th>
                            <th scope="col">ACTION</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            $no = 1;
                            foreach ($result as $row) :
                                echo "<tr>";
                                echo "<td scope='row'>" . $no . "</td>";
                                echo "<td>" . $row["nama_dokumen"] . "</td>";
                                echo "<td>" . $row["tahun_anggaran"] . "</td>";
                                // Pastikan Anda memiliki directory tempat file disimpan, misalnya 'uploads/bukuinduk/'
                                $filePath = '../uploads/rencana_kegiatan_anggaran/' . $row["pengumpulan_dokumen"];

                                // Periksa apakah file tersedia, jika ya, tampilkan link, jika tidak tampilkan pesan
                                if (file_exists($filePath) && !empty($row["pengumpulan_dokumen"])) {
                                    echo "<td><a href='$filePath' target='_blank'>VIEW</a></td>";
                                } else {
                                    echo "<td>Tidak ada dokumen</td>";
                                }
                                echo "<td>" . $row["keterangan"] . "</td>";
                                
                                // Tambahkan di sini untuk menampilkan status dengan enum
                                echo "<td class='";
                                if ($row["status"] == 'pending') {
                                    echo 'status-pending'; 
                                } elseif ($row["status"] == 'approved') {
                                    echo 'status-approved'; 
                                } elseif ($row["status"] == 'rejected') {
                                    echo 'status-rejected'; 
                                }
                                echo "'>" . $row["status"] . "</td>";


                                echo "<td>
                                    <button class='btn btn-warning edit-btn' 
                                        data-bs-toggle='modal' 
                                        data-bs-target='#editModal' 
                                        data-id='{$row['id']}'
                                        data-nama-dokumen='{$row['nama_dokumen']}'
                                        data-tahun-anggaran='{$row['tahun_anggaran']}'
                                        data-pengumpulan-dokumen='{$row['pengumpulan_dokumen']}'
                                        data-keterangan='{$row['keterangan']}'
                                        data-status='{$row['status']}'>Edit</button>&nbsp;&nbsp;
                                    <button class='btn btn-danger delete-btn' data-id='{$row['id']}'>Delete</button>
                                </td>";
                                echo "</tr>";
                                $no++;
                            endforeach;
                        ?>
                    </tbody>
                </table>
            </main>
        </div>
    </div>

    <!-- Modal Edit rencana kegiatan anggaran -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Edit Rencana Kegiatan Anggaran</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="edit_rencana_kegiatan_anggaran.php" method="POST" enctype="multipart/form-data">
                        <!-- Hidden field untuk ID -->
                        <input type="hidden" id="edit_id" name="edit_id">
                        
                        <!-- Nama Dokumen -->
                        <div class="mb-3">
                            <label for="edit_nama_dokumen" class="form-label">Nama Dokumen</label>
                            <input type="text" class="form-control" id="edit_nama_dokumen" name="edit_nama_dokumen" required>
                        </div>

                        <!-- Tahun Anggaran -->
                        <div class="mb-3">
                            <label for="edit_tahun_anggaran" class="form-label">Tahun Anggaran</label>
                            <input type="text" class="form-control" id="edit_tahun_anggaran" name="edit_tahun_anggaran" min="1000" max="9999" required>
                        </div>

                        <!-- Keterangan -->
                        <div class="mb-3">
                            <label for="edit_keterangan" class="form-label">Keterangan</label>
                            <input type="text" class="form-control" id="edit_keterangan" name="edit_keterangan">
                        </div>

                        <!-- Status -->
                        <div class="mb-3">
                            <label for="edit_status" class="form-label">Status</label>
                            <select class="form-select" id="edit_status" name="edit_status" required>
                                <option value="pending">Pending</option>
                                <option value="approved">Approved</option>
                                <option value="rejected">Rejected</option>
                            </select>
                        </div>

                        <!-- Upload File Baru (Opsional) -->
                        <div class="mb-3">
                            <label for="edit_file" class="form-label">Upload File Baru (Opsional)</label>
                            <input type="file" class="form-control" id="edit_file" name="file_upload">
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



    <!-- Modal Create rencana kegiatan anggaran -->
    <div class="modal fade" id="createModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Create Rencana Kegiatan Anggaran</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="create_rencana_kegiatan_anggaran.php" method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="file_upload" class="form-label">Attachment</label>
                            <input type="file" class="form-control" id="file_upload" name="file_upload">
                        </div>

                        <div class="mb-3">
                            <label for="nama_dokumen" class="form-label">Nama Dokumen</label>
                            <input type="text" class="form-control" id="nama_dokumen" name="nama_dokumen" required>
                        </div>

                        <div class="mb-3">
                            <label for="tahun_anggaran" class="form-label">Tahun Anggaran</label>
                            <input type="text" class="form-control" id="tahun_anggaran" name="tahun_anggaran" min="1000" max="9999" required>
                        </div>

                        <div class="mb-3">
                            <label for="keterangan" class="form-label">Keterangan</label>
                            <input type="text" class="form-control" id="keterangan" name="keterangan">
                        </div>

                        <div class="mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-control" id="status" name="status" required>
                                <option value="pending">Pending</option>
                                <option value="approved">Approved</option>
                                <option value="rejected">Rejected</option>
                            </select>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Create</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>



    <?php
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['file_upload'])) {
            // Mengambil informasi file
            $fileName = $_FILES['file_upload']['name'];
            $fileTmpName = $_FILES['file_upload']['tmp_name'];
            $fileSize = $_FILES['file_upload']['size'];
            $fileError = $_FILES['file_upload']['error'];

            // Mengambil ekstensi file
            $fileExt = pathinfo($fileName, PATHINFO_EXTENSION);

            // Membuat nama file baru (misalnya dengan menambahkan timestamp untuk mencegah duplikasi)
            $newFileName = uniqid('', true) . '.' . $fileExt;

            // Lokasi folder tempat file akan disimpan
            $uploadDir = '../uploads/rencana_kegiatan_anggaran/';

            // Memindahkan file ke folder upload
            if (move_uploaded_file($fileTmpName, $uploadDir . $newFileName)) {

                // Menyimpan nama file yang sudah diganti ke database
                $sql = "INSERT INTO rencana_kegiatan_anggaran (pengumpulan_dokumen) VALUES ('$newFileName')";

                if ($conn->query($sql) === TRUE) {
                    echo "File berhasil di-upload dan nama file disimpan ke database!";
                } else {
                    echo "Error: " . $sql . "<br>" . $conn->error;
                }

                $conn->close();
            } else {
                echo "Gagal meng-upload file.";
            }
        }
    ?>


    // <!-- JS Script -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Menangani ketika tombol edit ditekan
        const editButtons = document.querySelectorAll('.edit-btn');

        editButtons.forEach(button => {
            button.addEventListener('click', function () {
                // Ambil ID yang akan diedit
                const idToEdit = this.getAttribute('data-id'); // ID yang akan diedit

                // Ambil data dari atribut data-* yang ada pada tombol
                const nama_dokumen = this.getAttribute('data-nama-dokumen');
                const tahun_anggaran = this.getAttribute('data-tahun-anggaran');
                const keterangan = this.getAttribute('data-keterangan');
                const status = this.getAttribute('data-status');
                const dokumen = this.getAttribute('data-dokumen'); // Nama file dokumen

                // Isi modal dengan data yang didapat
                document.getElementById('edit_id').value = idToEdit;
                document.getElementById('edit_nama_dokumen').value = nama_dokumen;
                document.getElementById('edit_tahun_anggaran').value = tahun_anggaran;
                document.getElementById('edit_keterangan').value = keterangan;
                document.getElementById('edit_status').value = status;
                document.getElementById('edit_dokumen').value = dokumen;  // Nama file dokumen

                // Tampilkan modal
                const myModal = new bootstrap.Modal(document.getElementById('editModal'));
                myModal.show();
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
                        window.location.href = 'delete_rencana_kegiatan_anggaran.php?id=' + idToDelete;
                    }
                });
            });
        });
    </script>
    
    // <!-- Link untuk SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</body>

</html>
