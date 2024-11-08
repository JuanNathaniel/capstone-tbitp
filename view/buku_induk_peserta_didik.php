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
    $sql = "SELECT id, id_anak, no_induk, nisn, nama_lengkap, dokumen FROM `data_anak`";

    $result = $conn->query($sql);

    ?>
    <?php
    // Nama directory tempat file akan disimpan
    $uploadDir = '../uploads/';

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
                <h2 class="bg-info rounded p-4 text-white transition-bg">Buku Induk Peserta Didik</h2>
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
                            <th scope="col">NO INDUK</th>
                            <th scope="col">NISN</th>
                            <th scope="col">NAMA</th>
                            <th scope="col">DOKUMEN</th>
                            <th scope="col">ACTION</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            $no = 1;
                            foreach ($result as $row) :
                                echo "<tr>";
                                echo "<td scope='row'>" . $no . "</td>";
                                echo "<td>" . $row["no_induk"] . "</td>";
                                echo "<td>" . $row["nisn"] . "</td>";
                                echo "<td>" . $row["nama_lengkap"] . "</td>";
                                // Pastikan Anda memiliki directory tempat file disimpan, misalnya 'uploads/'
                                $filePath = '../uploads/' . $row["dokumen"];

                                // Periksa apakah file tersedia, jika ya, tampilkan link, jika tidak tampilkan pesan
                                if (file_exists($filePath) && !empty($row["dokumen"])) {
                                    echo "<td><a href='$filePath' target='_blank'>Lihat Dokumen</a></td>";
                                } else {
                                    echo "<td>Tidak ada dokumen</td>";
                                }
                                echo "<td>
                                    <button class='btn btn-warning edit-btn' 
                                        data-bs-toggle='modal' 
                                        data-bs-target='#editModal' 
                                        data-id='{$row['id']}'
                                        data-no-induk='{$row['no_induk']}'
                                        data-nisn='{$row['nisn']}'
                                        data-nama='{$row['nama_lengkap']}'
                                        data-dokumen='{$row['dokumen']}'>Edit</button>&nbsp;&nbsp;
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

    <!-- Modal Edit Buku Induk -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Edit buku induk peserta didik</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="edit_buku_induk_peserta_didik.php" method="POST" enctype="multipart/form-data">
                        <input type="hidden" id="edit_id" name="id">
                        
                        <div class="mb-3">
                            <label for="edit_no_induk" class="form-label">Nomor Induk</label>
                            <input type="text" class="form-control" id="edit_no_induk" name="no_induk" required>
                        </div>

                        <div class="mb-3">
                            <label for="edit_nisn" class="form-label">NISN</label>
                            <input type="text" class="form-control" id="edit_nisn" name="nisn" required>
                        </div>

                        <div class="mb-3">
                            <label for="edit_nama" class="form-label">Nama</label>
                            <input type="text" class="form-control" id="edit_nama" name="nama" required>
                        </div>

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

    <!-- Modal Create Buku Induk -->
    <div class="modal fade" id="createModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Create Buku Induk Peserta Didik</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="create_buku_induk_peserta_didik.php" method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="no_induk" class="form-label">Nomor Induk</label>
                            <input type="text" class="form-control" id="no_induk" name="no_induk" required>
                        </div>

                        <div class="mb-3">
                            <label for="nisn" class="form-label">NISN</label>
                            <input type="text" class="form-control" id="nisn" name="nisn" required>
                        </div>

                        <div class="mb-3">
                            <label for="nama" class="form-label">Nama</label>
                            <input type="text" class="form-control" id="nama" name="nama" required>
                        </div>

                        <div class="mb-3">
                            <label for="file_upload" class="form-label">Upload File (Opsional)</label>
                            <input type="file" class="form-control" id="file_upload" name="file_upload">
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
            $uploadDir = '../uploads/';

            // Memindahkan file ke folder upload
            if (move_uploaded_file($fileTmpName, $uploadDir . $newFileName)) {

                // Menyimpan nama file yang sudah diganti ke database
                $sql = "INSERT INTO data_anak (dokumen) VALUES ('$newFileName')";

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
                const namaAnak = this.getAttribute('data-nama');
                const noInduk = this.getAttribute('data-no-induk');
                const nisn = this.getAttribute('data-nisn');
                const tanggal = this.getAttribute('data-tanggal');
                const dokumen = this.getAttribute('data-dokumen');

                // Isi modal dengan data yang didapat
                document.getElementById('edit_id').value = idToEdit;
                document.getElementById('edit_no_induk').value = noInduk;
                document.getElementById('edit_nama').value = namaAnak;
                document.getElementById('edit_nisn').value = nisn;
                document.getElementById('edit_dokumen').value = dokumen;  // Jika ada dokumen

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
                        window.location.href = 'delete_buku_induk_peserta_didik.php?id=' + idToDelete;
                    }
                });
            });
        });
    </script>
    
    // <!-- Link untuk SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</body>

</html>
