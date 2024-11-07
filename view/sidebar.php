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

        /* Logout button styling */
        .logout {
            color: white;
            margin-top: 10px;
        }

        .logout:hover {
            color: grey;
        }
    </style>
</head>

<body>
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
        </div>
    </div>

    <!-- Bootstrap JavaScript dan Ikon -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.5.0/font/bootstrap-icons.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
