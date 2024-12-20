<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Absensi Datang dan Jemput</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.3/dist/sweetalert2.min.css">
    <style>
        .transition-bg {
            background: linear-gradient(to right, #344EAD, #1767A6);
        }

        .form-check {
            margin: 0;
        }
    </style>
</head>

<body>
    <?php
    include '../includes/koneksi.php';

    // Hapus data jika tombol delete diklik
    if (isset($_GET['delete_id'])) {
        $deleteId = intval($_GET['delete_id']);
        $deleteStmt = $pdo->prepare("DELETE FROM absensi_dan_jemput WHERE id = :id");
        $deleteStmt->bindParam(':id', $deleteId, PDO::PARAM_INT);

        if ($deleteStmt->execute()) {
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        } else {
            echo "<script>alert('Penghapusan gagal. Silakan coba lagi.');</script>";
        }
    }

    // Inisialisasi filter
    $filterDate = isset($_GET['filter_date']) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $_GET['filter_date']) ? $_GET['filter_date'] : null;
    $filterMonth = isset($_GET['filter_month']) && ctype_digit($_GET['filter_month']) ? intval($_GET['filter_month']) : null;

    // Query untuk mengambil data absensi
    $sql = "
        SELECT 
            absen.id AS id,
            date AS date,
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
        WHERE 1=1
    ";

    // Tambahkan kondisi filter tanggal
    if ($filterDate) {
        $sql .= " AND DATE(absen.date) = :filterDate";
    }

    // Tambahkan kondisi filter bulan
    if ($filterMonth) {
        $currentYear = date('Y'); // Sesuaikan dengan tahun sekarang
        $sql .= " AND MONTH(absen.date) = :filterMonth AND YEAR(absen.date) = :filterYear";
    }

    $stmt = $pdo->prepare($sql);

    // Bind parameter untuk filter tanggal
    if ($filterDate) {
        $stmt->bindParam(':filterDate', $filterDate);
    }

    // Bind parameter untuk filter bulan
    if ($filterMonth) {
        $currentYear = date('Y'); // Tahun sekarang
        $stmt->bindParam(':filterMonth', $filterMonth, PDO::PARAM_INT);
        $stmt->bindParam(':filterYear', $currentYear, PDO::PARAM_INT);
    }

    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    ?>

    <div class="container-fluid">
        <div class="row">
            <?php include 'sidebar.php'; ?>
            <main class="col-md-9 col-lg-10 ms-auto">
                <h2 class="bg-info rounded p-4 text-white transition-bg">Absensi Datang dan Jemput</h2>

                <div class="container-fluid">
                    <div class="header d-flex justify-content-between align-items-center">
                        <a href="absendanPenjemputan-create.php" class="btn btn-primary">Create</a>
                        <a href="absensi_dan_jemput_pdf.php?filter_date=<?= htmlspecialchars($filterDate) ?>&filter_month=<?= htmlspecialchars($filterMonth) ?>" class="btn btn-success">Download PDF</a>
                    </div>

                    <!-- Form Filter -->
                    <form method="GET" class="mt-3">
                        <div class="row">
                            <!-- Filter Tanggal -->
                            <div class="col-md-5">
                                <label for="filter_date" class="form-label">Filter Tanggal:</label>
                                <div class="input-group">
                                    <input type="date" name="filter_date" id="filter_date" class="form-control">
                                    <button type="submit" class="btn btn-secondary">Filter Tanggal</button>
                                </div>
                            </div>

                            <!-- Filter Bulan -->
                            <div class="col-md-5">
                                <label for="filter_month" class="form-label">Filter Bulan:</label>
                                <div class="input-group">
                                    <select name="filter_month" id="filter_month" class="form-select">
                                        <option value="">Pilih Bulan</option>
                                        <?php
                                        for ($i = 1; $i <= 12; $i++) {
                                            $selected = isset($_GET['filter_month']) && intval($_GET['filter_month']) === $i ? 'selected' : '';
                                            echo "<option value=\"$i\" $selected>" . date('F', mktime(0, 0, 0, $i, 1)) . "</option>";
                                        }
                                        ?>
                                    </select>
                                    <button type="submit" class="btn btn-secondary">Filter Bulan</button>
                                </div>
                            </div>

                            <div class="col-md-2 d-flex align-items-end">
                                <a href="<?= $_SERVER['PHP_SELF'] ?>" class="btn btn-outline-secondary w-100">Reset Filter</a>
                            </div>
                        </div>
                    </form>

                    <!-- Tabel Data -->
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
                                    <th>Tanggal</th>
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
                                    <td>
                                        <div class="form-check d-flex justify-content-center">
                                            <input class="form-check-input" type="checkbox" id="parafPengantar<?= $row['id'] ?>" <?= $row['paraf_pengantar'] ? 'checked' : '' ?> disabled>
                                        </div>
                                    </td>
                                    <td><?= htmlspecialchars($row['nama_penjemput']) ?></td>
                                    <td><?= htmlspecialchars($row['jam_jemput']) ?></td>
                                    <td>
                                        <div class="form-check d-flex justify-content-center">
                                            <input class="form-check-input" type="checkbox" id="parafPenjemput<?= $row['id'] ?>" <?= $row['paraf_penjemput'] ? 'checked' : '' ?> disabled>
                                        </div>
                                    </td>
                                    <td><?= htmlspecialchars($row['date']) ?></td>
                                    <td class="d-flex justify-content-around">
                                        <a href="absendanPenjemputan-update.php?id=<?= $row['id'] ?>" class="btn btn-warning btn-sm">Edit</a>
                                        <button class="btn btn-danger btn-sm" onclick="confirmDelete(<?= $row['id'] ?>)">Hapus</button>
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

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.3/dist/sweetalert2.all.min.js"></script>
    <script>
        function confirmDelete(id) {
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Data ini akan dihapus secara permanen!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = "?delete_id=" + id;
                }
            });
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
