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
        }

        tbody tr:hover {
            background-color: #f1f1f1; /* Efek hover pada baris tabel */
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

    // Query untuk mengambil data
    $sql = "SELECT guru.nama, daftar_hadir_guru.jam_datang, daftar_hadir_guru.jam_pulang, 
            daftar_hadir_guru.tanda_tangan1, daftar_hadir_guru.date, daftar_hadir_guru.ket
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

            <div class="hover-trigger"></div>
            <!-- Sidebar -->
            <nav class="col-md-3 col-lg-2 sidebar" id="sidebar">
                <button type="button" class="btn btn-outline-primary w-100 user-info d-flex align-items-center text-white mb-3">
                    <i class="bi bi-person-circle me-2"></i>Aming
                </button>

                <div class="dropdown">
                    <a class="dropdown-toggle text-decoration-none" href="#" role="button">
                        Guru dan Anak
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item text-decoration-none" href="#">Absensi Datang dan Jemput</a></li>
                        <li><a class="dropdown-item text-decoration-none" href="#">Daftar Hadir Guru</a></li>
                        <li><a class="dropdown-item text-decoration-none" href="#">Aturan Penjemputan</a></li>
                        <li><a class="dropdown-item text-decoration-none" href="#">Buku Induk Peserta Didik</a></li>
                    </ul>
                </div>

                <div class="dropdown mt-3">
                    <a class="dropdown-toggle text-decoration-none" href="#" role="button">
                        Keuangan
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item text-decoration-none" href="#">Pemasukan dan Pengeluaran</a></li>
                        <li><a class="dropdown-item text-decoration-none" href="#">Rencana Kegiatan Anggaran</a></li>
                        <li><a class="dropdown-item text-decoration-none" href="#">Rincian Biaya Pendidikan</a></li>
                        <li><a class="dropdown-item text-decoration-none" href="#">Laporan Dana</a></li>
                        <li><a class="dropdown-item text-decoration-none" href="#">Rekapitulasi Pembayaran</a></li>
                    </ul>
                </div>
                
                <div class="dropdown mt-3">
                    <a class="dropdown-toggle text-decoration-none" href="#" role="button">
                        Pembelajaran
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item text-decoration-none" href="#">Jadwal Tematik dan Kegiatan</a></li>
                        <li><a class="dropdown-item text-decoration-none" href="#">Laporan Perkembangan</a></li>
                        <li><a class="dropdown-item text-decoration-none" href="#">Formulir Deteksi dan Tumbuh Kembang</a></li>
                        <li><a class="dropdown-item text-decoration-none" href="#">Data Kurikulum Merdeka</a></li>
                    </ul>
                </div>

                <button type="button" class="btn btn-outline-primary w-100 logout d-flex align-items-center">
                    <i class="bi bi-box-arrow-left me-2"></i>Logout
                </button>
            </nav>

            <!-- Konten Utama -->
            <main class="col-md-9 col-lg-10 ms-auto" style="margin-left: auto;">
                <h2 class="bg-info rounded p-4 text-white transition-bg">Daftar hadir guru</h2>
                <div class="content">
                    <div class="d-flex justify-content-between mb-3">
                        <div>
                            <button class="btn btn-primary">CREATE</button>
                            <label>Bulan: <input type="text" class="input-date" placeholder="Bulan"></label>
                            <label>Hari/Tgl: <input type="text" class="input-date" placeholder="Tanggal"></label>
                        </div>
                    </div>
                </div>

                <?php foreach ($dataByDate as $date => $data) : ?>
                    <br>
                    <h4>Date : <?php echo $date; ?></h4>
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
                                echo "<td>" . $row["ket"] . "</td>";
                                echo "<td>" . $row["tanda_tangan1"] . "</td>";
                                echo "<td><button class='btn btn-primary'>Edit</button>&nbsp;&nbsp;<button class='btn btn-primary'>Delete</button></td>";
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

    <!-- Bootstrap JavaScript dan Ikon -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.5.0/font/bootstrap-icons.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
