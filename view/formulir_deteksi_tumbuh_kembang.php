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
    </style>
</head>

<body>
    <?php
    // // Koneksi ke database
    // $servername = "localhost";
    // $username = "root";
    // $password = "";
    // $dbname = "capstone_tpa"; // Ganti dengan nama database Anda

    // // Membuat koneksi
    // $conn = new mysqli($servername, $username, $password, $dbname);

    // // Memeriksa koneksi
    // if ($conn->connect_error) {
    //     die("Koneksi gagal: " . $conn->connect_error);
    // }
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
    $sql = "SELECT * FROM `formulir_deteksi_tumbuh_kembang`";
    $query_siswa = "SELECT * FROM anak";

    // Jika ada pencarian, tambahkan kondisi WHERE pada query SQL
    if ($search != '') {
        $sql .= " WHERE formulir_deteksi_tumbuh_kembang.nama_siswa LIKE '%" . $conn->real_escape_string($search) . "%'";
    }

    $result = $conn->query($sql);
    $result_siswa = $conn->query($query_siswa);

    ?>
    <?php
    // Nama directory tempat file akan disimpan
    $uploadDir = '../uploads/formulir_deteksi_dan_tumbuh_kembang/';

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
                <h2 class="bg-info rounded p-4 text-white transition-bg">Formulir Deteksi dan Tumbuh Kembang</h2>
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
                            <th scope="col">NAMA SISWA</th>
                            <th scope="col">TAHUN PELAJARAN</th>
                            <th scope="col">PENGUMPULAN DOKUMEN</th>
                            <th scope="col">KETERANGAN</th>
                            <th scope="col">ACTION</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            $no = 1;
                            foreach ($result as $row) :
                                echo "<tr>";
                                echo "<td scope='row'>" . $no . "</td>";
                                echo "<td>" . $row["nama_siswa"] . "</td>";
                                echo "<td>" . $row["tahun_pelajaran"] . "</td>";
                                // Pastikan Anda memiliki directory tempat file disimpan, misalnya 'uploads/bukuinduk/'
                                $filePath = '../uploads/formulir_deteksi_dan_tumbuh_kembang/' . $row["pengumpulan_dokumen"];

                                // Periksa apakah file tersedia, jika ya, tampilkan link, jika tidak tampilkan pesan
                                if (file_exists($filePath) && !empty($row["pengumpulan_dokumen"])) {
                                    echo "<td><a href='$filePath' target='_blank'>Lihat Dokumen</a></td>";
                                } else {
                                    echo "<td>Tidak ada dokumen</td>";
                                }
                                echo "<td>" . $row["keterangan"] . "</td>";
                                
                                echo "<td>
                                    <button class='btn btn-warning edit-btn' 
                                        data-bs-toggle='modal' 
                                        data-bs-target='#editModal' 
                                        data-id='{$row['id']}'
                                        data-nama-siswa='{$row['nama_siswa']}'
                                        data-tahun-pelajaran='{$row['tahun_pelajaran']}'
                                        data-pengumpulan_dokumen='{$row['pengumpulan_dokumen']}'
                                        data-keterangan='{$row['keterangan']}'>Edit</button>&nbsp;&nbsp;
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

    <!-- Modal Edit formulir tumbuh kembang -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Edit Formulir Deteksi dan Tumbuh Kembang</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="edit_formulir_deteksi_tumbuh_kembang.php" method="POST" enctype="multipart/form-data">
                        <input type="hidden" id="edit_id" name="id">
                        
                        <!-- Dropdown Nama Siswa -->
                        <div class="mb-3">
                            <label for="id_siswa" class="form-label">Nama Siswa</label>
                            <?php if ($result_siswa->num_rows > 0): ?>
                                <select id="id_siswa" name="id_siswa" class="form-select" required disabled>
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
                            <label for="edit_tahun_pelajaran" class="form-label">Tahun Pelajaran</label>
                            <input type="text" class="form-control" id="edit_tahun_pelajaran" name="tahun_pelajaran" required>
                        </div>                        

                        <div class="mb-3">
                            <label for="edit_file" class="form-label">Upload File Baru (Opsional)</label>
                            <input type="file" class="form-control" id="edit_file" name="file_upload">
                        </div>
                        
                        <div class="mb-3">
                            <label for="edit_keterangan" class="form-label">keterangan</label>
                            <input type="text" class="form-control" id="edit_keterangan" name="keterangan">
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

    <!-- Modal Create Deteksi dan tumbuh kembang -->
    <div class="modal fade" id="createModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Create Formulir Deteksi dan Tumbuh Kembang</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="create_formulir_deteksi_tumbuh_kembang.php" method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="file_upload" class="form-label">Attachment</label>
                            <input type="file" class="form-control" id="file_upload" name="file_upload">
                        </div>

                        <!-- Dropdown Nama Siswa -->
                        <div class="mb-3">
                            <label for="id_siswa" class="form-label">Nama Siswa</label>
                            <?php
                            // Pastikan ulang query result_siswa masih berisi data dari tabel anak
                            $result_siswa = $conn->query($query_siswa); // Query ulang jika $result_siswa sudah digunakan

                            if ($result_siswa && $result_siswa->num_rows > 0): ?>
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
                            <label for="tahun_pelajaran" class="form-label">Tahun Pelajaran</label>
                            <input type="text" class="form-control" id="tahun_pelajaran" name="tahun_pelajaran" required>
                        </div>

                        <div class="mb-3">
                            <label for="keterangan" class="form-label">Keterangan</label>
                            <input type="text" class="form-control" id="keterangan" name="keterangan">
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
            $uploadDir = '../uploads/formulir_deteksi_dan_tumbuh_kembang/';

            // Memindahkan file ke folder upload
            if (move_uploaded_file($fileTmpName, $uploadDir . $newFileName)) {

                // Menyimpan nama file yang sudah diganti ke database
                $sql = "INSERT INTO formulir_deteksi_tumbuh_kembang (pengumpulan_dokumen) VALUES ('$newFileName')";

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
                const namaSiswa = this.getAttribute('data-nama-siswa');
                const tahunPelajaran = this.getAttribute('data-tahun-pelajaran');
                const keterangan = this.getAttribute('data-keterangan');
                const dokumen = this.getAttribute('data-pengumpulan-dokumen');

                // Isi modal dengan data yang didapat
                document.getElementById('edit_id').value = idToEdit; // Set ID ke form hidden
                document.getElementById('edit_tahun_pelajaran').value = tahunPelajaran;
                document.getElementById('edit_keterangan').value = keterangan;

                // Dropdown: Pilih ID siswa yang sesuai
                const dropdown = document.getElementById('id_siswa');
                for (let option of dropdown.options) {
                    if (option.text === namaSiswa) {
                        option.selected = true; // Pilih siswa yang sesuai
                        break;
                    }
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
                        window.location.href = 'delete_formulir_deteksi_tumbuh_kembang.php?id=' + idToDelete;
                    }
                });
            });
        });
    </script>
    
    // <!-- Link untuk SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</body>
</html>
