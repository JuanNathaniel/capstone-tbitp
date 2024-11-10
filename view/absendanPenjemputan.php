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
    <?php
    // Koneksi ke database
    $host = 'localhost';
    $dbname = 'capstone_tpa';
    $username = 'root';
    $password = '';

    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        die("Koneksi gagal: " . $e->getMessage());
    }

    // Hapus data jika tombol delete diklik
    if (isset($_GET['delete_id'])) {
        $deleteId = $_GET['delete_id'];
        $deleteStmt = $pdo->prepare("DELETE FROM absensi_dan_jemput WHERE id = :id");
        $deleteStmt->bindParam(':id', $deleteId, PDO::PARAM_INT);
        
        if ($deleteStmt->execute()) {
            // Redirect jika berhasil dihapus
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        } else {
            echo "Penghapusan gagal. Silakan coba lagi.";
        }
    }

    // Filter data berdasarkan tanggal
    $filterDate = isset($_GET['filter_date']) ? $_GET['filter_date'] : date('Y-m-d');

    // Query untuk mengambil data absensi dan jemput
    $sql = "
        SELECT 
            absen.id AS id,  
            anak.nama AS nama_siswa,
            pengantar.nama_pengantar AS nama_pengantar,
            pengantar.jam_datang AS jam_datang,
            pengantar.paraf AS paraf_pengantar,
            penjemput.nama_penjemput AS nama_penjemput,
            penjemput.jam_jemput AS jam_jemput,
            penjemput.paraf AS paraf_penjemput
        FROM 
            absensi_dan_jemput AS absen
        INNER JOIN 
            anak ON absen.id_anak = anak.id
        INNER JOIN 
            pengantar ON absen.id_pengantar = pengantar.id
        INNER JOIN 
            penjemput ON absen.id_penjemput = penjemput.id
    ";

    // Menambahkan filter tanggal jika ada
    if ($filterDate) {
        $sql .= " WHERE DATE(absen.date) = :filterDate";
    }

    // Menyiapkan dan menjalankan query
    $stmt = $pdo->prepare($sql);

    // Bind parameter untuk filter tanggal jika ada
    if ($filterDate) {
        $stmt->bindParam(':filterDate', $filterDate);
    }

    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    ?>

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <?php include 'sidebar.php'; ?>

            <!-- Konten Utama -->
            <main class="col-md-9 col-lg-10 ms-auto" style="margin-left: auto;">
                <h2 class="bg-info rounded p-4 text-white transition-bg">Absensi Datang dan Jemput</h2>
                <div class="container-fluid">
                    <div class="header d-flex justify-content-between align-items-center">
                        <!-- Tombol Create -->
                        <a href="absendanPenjemputan-create.php" class="btn btn-primary">Create</a>
                    </div>

                    <!-- Form Filter Tanggal -->
                    <form method="GET" class="mt-3">
                        <div class="row">
                            <div class="col-md-4">
                                <input type="date" name="filter_date" class="form-control" value="<?= htmlspecialchars($filterDate) ?>">
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-secondary">Filter</button>
                            </div>
                        </div>
                    </form>

                    <div class="content mt-4">
                        <table class="table table-bordered text-center">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Siswa</th>
                                    <th>Nama (Pengantar)</th>
                                    <th>Jam Datang (Pengantar)</th>
                                    <th>Paraf (Pengantar)</th>
                                    <th>Nama (Penjemput)</th>
                                    <th>Jam Jemput (Penjemput)</th>
                                    <th>Paraf (Penjemput)</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $no = 1; foreach ($results as $row): ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><?= htmlspecialchars($row['nama_siswa']) ?></td>
                                    <td><?= htmlspecialchars($row['nama_pengantar']) ?></td>
                                    <td><?= htmlspecialchars($row['jam_datang']) ?></td>
                                    <td><?= htmlspecialchars($row['paraf_pengantar']) ?></td>
                                    <td><?= htmlspecialchars($row['nama_penjemput']) ?></td>
                                    <td><?= htmlspecialchars($row['jam_jemput']) ?></td>
                                    <td><?= htmlspecialchars($row['paraf_penjemput']) ?></td>
                                    <td>
                                        <a href="absendanPenjemputan-update.php?id=<?= $row['id'] ?>" class="btn btn-warning">Edit</a>
                                        <button class="btn btn-danger" onclick="confirmDelete(<?= $row['id'] ?>)">Delete</button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script>
        // Fungsi konfirmasi penghapusan
        function confirmDelete(id) {
            if (confirm("Apakah Anda yakin ingin menghapus data ini?")) {
                alert("ID yang akan dihapus: " + id); // Debugging: cek ID yang dikirim
                window.location.href = "?delete_id=" + id;
            }
        }

    </script>

    <!-- Bootstrap JavaScript dan Ikon -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.5.0/font/bootstrap-icons.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Pilih item dengan id "absensi"
        document.getElementById('absensi').addEventListener('click', function(event) {
            // Mencegah tindakan default jika link tidak memiliki URL di "href"
            event.preventDefault();
    
            // Arahkan ke absendanpenjemputan.php
            window.location.href = 'absendanpenjemputan.php';
        });

        document.addEventListener('DOMContentLoaded', function() {
            const dateInput = document.querySelector('input[name="filter_date"]');
            
            // Set nilai default ke tanggal hari ini jika belum diset
            if (!dateInput.value) {
                const today = new Date().toISOString().split('T')[0];
                dateInput.value = today;
            }
            
            // Periksa apakah URL sudah memiliki parameter "filter_date"
            const urlParams = new URLSearchParams(window.location.search);
            if (!urlParams.has('filter_date')) {
                // Jika belum ada parameter "filter_date", kirim formulir secara otomatis
                document.querySelector('form').submit();
            }
        });
    </script>

</body>

</html>
