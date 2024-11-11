<!DOCTYPE html>
<html lang="en">

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

    // Mengambil nilai pencarian dari URL (query string)
    $search = isset($_GET['search']) ? $_GET['search'] : '';

    // Query untuk mengambil data
    $sql = "SELECT data_anak.id, data_anak.id_anak, data_anak.no_induk, data_anak.nisn, anak.nama, anak.usia, anak.semester, anak.kelompok, anak.tahun, dokumen FROM `data_anak` JOIN anak ON data_anak.id_anak = anak.id";
    $query_siswa = "SELECT id, nama FROM anak";

    // Jika ada pencarian, tambahkan kondisi WHERE pada query SQL
    if ($search != '') {
        $sql .= " WHERE anak.nama LIKE '%" . $conn->real_escape_string($search) . "%'";
    }

    $result = $conn->query($sql);
    $result_siswa = $conn->query($query_siswa);
    ?>
    <?php
    // Nama directory tempat file akan disimpan
    $uploadDir = '../uploads/bukuinduk/';

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
                        <!-- Form untuk Pencarian Nama Anak -->
                        <form class="d-flex" action="" method="GET">
                            <input type="text" name="search" class="form-control me-2" placeholder="Cari Nama Anak" 
                                value="<?php echo isset($_GET['search']) ? $_GET['search'] : ''; ?>">
                            <button type="submit" class="btn btn-outline-secondary">Search</button>
                        </form>
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
                            <th scope="col">USIA</th>
                            <th scope="col">SEMESTER</th>
                            <th scope="col">KELOMPOK</th>
                            <th scope="col">TAHUN</th>
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
                                echo "<td>" . $row["nama"] . "</td>";
                                echo "<td>" . $row["usia"] . "</td>";
                                echo "<td>" . $row["semester"] . "</td>";
                                echo "<td>" . $row["kelompok"] . "</td>";
                                echo "<td>" . $row["tahun"] . "</td>";
                                // Pastikan Anda memiliki directory tempat file disimpan, misalnya 'uploads/bukuinduk/'
                                $filePath = '../uploads/bukuinduk/' . $row["dokumen"];

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
                                        data-nama='{$row['nama']}'
                                        data-usia='{$row['usia']}'
                                        data-semester='{$row['semester']}'
                                        data-kelompok='{$row['kelompok']}'
                                        data-tahun='{$row['tahun']}'
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
                        
                        <!-- Dropdown Nama Siswa -->
                        <div class="mb-3">
                            <label for="id_siswa" class="form-label">Nama Siswa</label>
                            <?php if ($result_siswa->num_rows > 0): ?>
                                <select id="id_siswa" name="id_siswa" class="form-select" required>
                                    <option value="" disabled selected hidden>Pilih Nama Siswa</option>
                                    <?php
                                    // Menampilkan nama siswa sebagai opsi dalam dropdown
                                    while ($row = $result_siswa->fetch_assoc()) {
                                        echo "<option value='" . $row['id'] . "'>" . $row['nama'] . "</option>";
                                    }
                                    ?>
                                </select>
                            <?php else: ?>
                                <p class="text-danger">Tidak ada data siswa tersedia.</p>
                                <select id="id_siswa" name="id_siswa" class="form-select" disabled>
                                    <option value="">Tidak ada siswa</option>
                                </select>
                            <?php endif; ?>
                        </div>

                        <div class="mb-3">
                            <label for="edit_no_induk" class="form-label">Nomor Induk</label>
                            <input type="text" class="form-control" id="edit_no_induk" name="no_induk" required>
                        </div>

                        <div class="mb-3">
                            <label for="edit_nisn" class="form-label">NISN</label>
                            <input type="text" class="form-control" id="edit_nisn" name="nisn" required>
                        </div>

                        <div class="mb-3">
                            <label for="edit_usia" class="form-label">Usia</label>
                            <input type="text" class="form-control" id="edit_usia" name="usia" required>
                        </div>

                        <div class="mb-3">
                            <label for="edit_semester" class="form-label">Semester</label>
                            <input type="text" class="form-control" id="edit_semester" name="semester" required>
                        </div>

                        <div class="mb-3">
                            <label for="edit_kelompok" class="form-label">Kelompok</label>
                            <input type="text" class="form-control" id="edit_kelompok" name="kelompok" required>
                        </div>

                        <div class="mb-3">
                            <label for="edit_tahun" class="form-label">Tahun</label>
                            <input type="text" class="form-control" id="edit_tahun" name="tahun" required>
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
                            <label for="nama_anak" class="form-label">Nama Anak</label>
                            <input type="text" class="form-control" id="nama_anak" name="nama_anak" required>
                        </div>

                        <div class="mb-3">
                            <label for="no_induk" class="form-label">Nomor Induk</label>
                            <input type="text" class="form-control" id="no_induk" name="no_induk" required>
                        </div>

                        <div class="mb-3">
                            <label for="nisn" class="form-label">NISN</label>
                            <input type="text" class="form-control" id="nisn" name="nisn" required>
                        </div>

                        <div class="mb-3">
                            <label for="usia" class="form-label">Usia</label>
                            <input type="text" class="form-control" id="usia" name="usia" required>
                        </div>

                        <div class="mb-3">
                            <label for="semester" class="form-label">Semester</label>
                            <input type="text" class="form-control" id="semester" name="semester" required>
                        </div>

                        <div class="mb-3">
                            <label for="kelompok" class="form-label">Kelompok</label>
                            <input type="text" class="form-control" id="kelompok" name="kelompok" required>
                        </div>

                        <div class="mb-3">
                            <label for="tahun" class="form-label">Tahun</label>
                            <input type="text" class="form-control" id="tahun" name="tahun" required>
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
            $uploadDir = '../uploads/bukuinduk/';

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

    <!-- JS Script -->
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
                const usia = this.getAttribute('data-usia');
                const semester = this.getAttribute('data-semester');
                const kelompok = this.getAttribute('data-kelompok');
                const tahun = this.getAttribute('data-tahun');
                const dokumen = this.getAttribute('data-dokumen');

                // Isi modal dengan data yang didapat
                document.getElementById('edit_id').value = idToEdit;
                document.getElementById('edit_no_induk').value = noInduk;
                document.getElementById('edit_nisn').value = nisn;
                document.getElementById('edit_usia').value = usia;
                document.getElementById('edit_semester').value = semester;
                document.getElementById('edit_kelompok').value = kelompok;
                document.getElementById('edit_tahun').value = tahun;
                
                // Update dropdown Nama Siswa agar terpilih sesuai data yang ada
                const idSiswaDropdown = document.getElementById('id_siswa');
                const options = idSiswaDropdown.getElementsByTagName('option');
                for (let option of options) {
                    if (option.value == idToEdit) {
                        option.selected = true;
                        break;
                    }
                }

                // Update nilai dokumen jika ada (optional, tergantung apakah Anda ingin menampilkannya)
                if (dokumen) {
                    document.getElementById('edit_dokumen').value = dokumen;
                }

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
