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

        .dropdown-toggle {
            color: white;
            cursor: pointer;
        }

        /* Styling dropdown agar tidak hilang */
        .dropdown-menu {
            padding: 0;
            background-color: #333;
            font-size: 14px;
        }

        .dropdown-item {
            color: white;
            padding: 8px 16px;
            
        }

        /* Warna berubah saat dropdown item ditekan */
        .dropdown-item:active, .dropdown-item:focus {
            background-color: #133E87; /* Sesuaikan warna yang diinginkan */
            color: #fff; /* Sesuaikan warna teks jika perlu */
            outline: none; /* Menghilangkan border fokus default */
        }

        .dropdown-item:hover {
            background-color: #133E87;
        }

        /* Logout button styling */
        .logout {
            color: white;
            margin-top: 10px;
        }

        .logout:hover {
            color: white;
        }

        .list-unstyled {
            font-size: 13px;
        }
    </style>
</head>

<body>
    <div class="container-fluid">
        <div class="row">

            <div class="hover-trigger"></div>
            <!-- Sidebar -->
            <nav class="col-md-3 col-lg-2 sidebar" id="sidebar">
            <a href="home.php" class="w-100">
                <button type="button" class="btn btn-outline-primary w-100 user-info d-flex align-items-center text-white mb-3">
                    <i class="bi bi-person-circle me-2"></i>Aming
                </button>
            </a>


                <!-- Guru dan Anak Dropdown -->
                <div class="dropdown">
                    <a class="dropdown-toggle text-decoration-none" href="#" role="button" data-bs-toggle="collapse" data-bs-target="#guruAnakDropdown" aria-expanded="false">
                        <i class="bi bi-people-fill me-2"></i> Guru dan Anak
                    </a>
                    <div class="collapse" id="guruAnakDropdown">
                        <ul class="list-unstyled ms-3">
                            <li><a id="absensi" class="dropdown-item text-decoration-none" href="absendanpenjemputan.php"><i class="bi bi-calendar-check me-2"></i> Absensi Datang dan Jemput</a></li>
                            <li><a id="daftarHadirGuru" class="dropdown-item text-decoration-none" href="daftar_hadir_guru.php"><i class="bi bi-card-list me-2"></i> Daftar Hadir Guru</a></li>
                            <li><a id="aturanPenjemputan" class="dropdown-item text-decoration-none" href="aturanPenjemputan.php"><i class="bi bi-shield-lock me-2"></i> Aturan Penjemputan</a></li>
                            <li><a id="bukuIndukPesertaDidik" class="dropdown-item text-decoration-none" href="buku_induk_peserta_didik.php"><i class="bi bi-book me-2"></i> Buku Induk Peserta Didik</a></li>
                        </ul>
                    </div>
                </div>

                <!-- Keuangan Dropdown -->
                <div class="dropdown mt-3">
                    <a class="dropdown-toggle text-decoration-none" href="#" role="button" data-bs-toggle="collapse" data-bs-target="#keuanganDropdown" aria-expanded="false">
                        <i class="bi bi-cash-stack me-2"></i> Keuangan
                    </a>
                    <div class="collapse" id="keuanganDropdown">
                        <ul class="list-unstyled ms-3">
                            <li><a id="pemasukanPengeluaran" class="dropdown-item text-decoration-none" href="pemasukandanpengeluaran.php"><i class="bi bi-arrow-down-circle me-2"></i> Pemasukan dan Pengeluaran</a></li>
                            <li><a id="rencanaKegiatanAnggaran" class="dropdown-item text-decoration-none" href="rencana_kegiatan_anggaran.php"><i class="bi bi-journal me-2"></i> Rencana Kegiatan Anggaran</a></li>
                            <li><a id="rincianBiayaPendidikan" class="dropdown-item text-decoration-none" href="#"><i class="bi bi-calculator me-2"></i> Rincian Biaya Pendidikan</a></li>
                            <li><a id="laporanDana" class="dropdown-item text-decoration-none" href="#"><i class="bi bi-file-earmark-bar-graph me-2"></i> Laporan Dana</a></li>
                            <li><a id="rekapitulasiPembayaran" class="dropdown-item text-decoration-none" href="#"><i class="bi bi-receipt me-2"></i> Rekapitulasi Pembayaran</a></li>
                        </ul>
                    </div>
                </div>

                <!-- Pembelajaran Dropdown -->
                <div class="dropdown mt-3">
                    <a class="dropdown-toggle text-decoration-none" href="#" role="button" data-bs-toggle="collapse" data-bs-target="#pembelajaranDropdown" aria-expanded="false">
                        <i class="bi bi-book-half me-2"></i> Pembelajaran
                    </a>
                    <div class="collapse" id="pembelajaranDropdown">
                        <ul class="list-unstyled ms-3">
                            <li><a id="jadwalTematikDanKegiatan" class="dropdown-item text-decoration-none" href="#"><i class="bi bi-calendar3 me-2"></i> Jadwal Tematik dan Kegiatan</a></li>
                            <li><a id="laporanPerkembangan" class="dropdown-item text-decoration-none" href="#"><i class="bi bi-bar-chart-line me-2"></i> Laporan Perkembangan</a></li>
                            <li><a id="formulirTumbuhKembang" class="dropdown-item text-decoration-none" href="formulir_deteksi_tumbuh_kembang.php"><i class="bi bi-file-earmark-medical me-2"></i> Formulir Tumbuh Kembang</a></li>
                            <li><a id="dataKurikulumMerdeka" class="dropdown-item text-decoration-none" href="data_kurikulum_merdeka.php"><i class="bi bi-journal-bookmark-fill me-2"></i> Data Kurikulum Merdeka</a></li>
                        </ul>
                    </div>
                </div>

                <!-- Logout Button -->
                <button type="button" class="btn btn-outline-primary w-100 logout d-flex align-items-center mt-auto">
                    <i class="bi bi-box-arrow-left me-2"></i> Logout
                </button>
            </nav>
        </div>
    </div>

    <!-- Bootstrap JavaScript dan Ikon -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.5.0/font/bootstrap-icons.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</html>
