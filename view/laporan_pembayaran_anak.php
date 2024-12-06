<?php
session_start();

// Cek apakah pengguna sudah login
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

// Regenerasi ID sesi untuk keamanan ekstra
session_regenerate_id(true);

// Koneksi ke database
$conn = new mysqli("localhost", "root", "", "capstone_tpa");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Default bulan dan tahun saat ini
$bulan = isset($_GET['bulan']) ? (int)$_GET['bulan'] : date('m');
$tahun = isset($_GET['tahun']) ? (int)$_GET['tahun'] : date('Y');

// Query untuk mendapatkan data utama dan riwayat
$query ="SELECT 
    a.id AS id_anak,
    a.nama AS nama_anak,
    ld.jenis_pembayaran,
    ld.jumlah,
    rp.cicilan_1,
    rp.cicilan_2,
    rp.keterangan,
    ld.date AS date_pembayaran,
    'CURRENT' AS sumber
FROM anak a
LEFT JOIN (
    SELECT 'Pendaftaran' AS jenis_pembayaran, pendaftaran AS jumlah, nama, date FROM laporan_dana
    UNION ALL
    SELECT 'SPP Bulan', spp_bulan, nama, date FROM laporan_dana
    UNION ALL
    SELECT 'Seragam', seragam, nama, date FROM laporan_dana
    UNION ALL
    SELECT 'Pengembangan Sekolah', pengembangan_sekolah, nama, date FROM laporan_dana
    UNION ALL
    SELECT 'Kegiatan Pembelajaran', kegiatan_pembelajaran, nama, date FROM laporan_dana
    UNION ALL
    SELECT 'Keterlambatan', keterlambatan, nama, date FROM laporan_dana
) AS ld ON a.id = ld.nama
LEFT JOIN rekapitulasi_pembayaran rp 
    ON ld.jenis_pembayaran = rp.jenis_pembayaran 
    AND a.id = rp.id_anak
WHERE MONTH(ld.date) = $bulan AND YEAR(ld.date) = $tahun

UNION ALL

SELECT 
    a.id AS id_anak,
    a.nama AS nama_anak,
    'Pendaftaran' AS jenis_pembayaran, -- Menggunakan label statis untuk kategori pembayaran
    ldh.pendaftaran AS jumlah,
    NULL AS cicilan_1,
    NULL AS cicilan_2,
    ldh.keterangan,
    ldh.date AS date_pembayaran,
    'HISTORY' AS sumber
FROM anak a
LEFT JOIN laporan_dana_history ldh ON a.id = ldh.nama
WHERE ldh.pendaftaran IS NOT NULL AND MONTH(ldh.date) = $bulan AND YEAR(ldh.date) = $tahun

UNION ALL

SELECT 
    a.id AS id_anak,
    a.nama AS nama_anak,
    'SPP Bulan' AS jenis_pembayaran,
    ldh.spp_bulan AS jumlah,
    NULL AS cicilan_1,
    NULL AS cicilan_2,
    ldh.keterangan,
    ldh.date AS date_pembayaran,
    'HISTORY' AS sumber
FROM anak a
LEFT JOIN laporan_dana_history ldh ON a.id = ldh.nama
WHERE ldh.spp_bulan IS NOT NULL AND MONTH(ldh.date) = $bulan AND YEAR(ldh.date) = $tahun

UNION ALL

SELECT 
    a.id AS id_anak,
    a.nama AS nama_anak,
    'Seragam' AS jenis_pembayaran,
    ldh.seragam AS jumlah,
    NULL AS cicilan_1,
    NULL AS cicilan_2,
    ldh.keterangan,
    ldh.date AS date_pembayaran,
    'HISTORY' AS sumber
FROM anak a
LEFT JOIN laporan_dana_history ldh ON a.id = ldh.nama
WHERE ldh.seragam IS NOT NULL AND MONTH(ldh.date) = $bulan AND YEAR(ldh.date) = $tahun

UNION ALL

SELECT 
    a.id AS id_anak,
    a.nama AS nama_anak,
    'Pengembangan Sekolah' AS jenis_pembayaran,
    ldh.pengembangan_sekolah AS jumlah,
    NULL AS cicilan_1,
    NULL AS cicilan_2,
    ldh.keterangan,
    ldh.date AS date_pembayaran,
    'HISTORY' AS sumber
FROM anak a
LEFT JOIN laporan_dana_history ldh ON a.id = ldh.nama
WHERE ldh.pengembangan_sekolah IS NOT NULL AND MONTH(ldh.date) = $bulan AND YEAR(ldh.date) = $tahun

UNION ALL

SELECT 
    a.id AS id_anak,
    a.nama AS nama_anak,
    'Kegiatan Pembelajaran' AS jenis_pembayaran,
    ldh.kegiatan_pembelajaran AS jumlah,
    NULL AS cicilan_1,
    NULL AS cicilan_2,
    ldh.keterangan,
    ldh.date AS date_pembayaran,
    'HISTORY' AS sumber
FROM anak a
LEFT JOIN laporan_dana_history ldh ON a.id = ldh.nama
WHERE ldh.kegiatan_pembelajaran IS NOT NULL AND MONTH(ldh.date) = $bulan AND YEAR(ldh.date) = $tahun

UNION ALL

SELECT 
    a.id AS id_anak,
    a.nama AS nama_anak,
    'Keterlambatan' AS jenis_pembayaran,
    ldh.keterlambatan AS jumlah,
    NULL AS cicilan_1,
    NULL AS cicilan_2,
    ldh.keterangan,
    ldh.date AS date_pembayaran,
    'HISTORY' AS sumber
FROM anak a
LEFT JOIN laporan_dana_history ldh ON a.id = ldh.nama
WHERE ldh.keterlambatan IS NOT NULL AND MONTH(ldh.date) = $bulan AND YEAR(ldh.date) = $tahun";

// Menambahkan filter berdasarkan tanggal jika ada
if (!empty($start_date) && !empty($end_date)) {
    $query .= " AND ld.date BETWEEN '$start_date' AND '$end_date'";
} elseif (!empty($start_date)) {
    $query .= " AND ld.date >= '$start_date'";
} elseif (!empty($end_date)) {
    $query .= " AND ld.date <= '$end_date'";
}

$query .= " UNION ALL

SELECT 
    a.id AS id_anak,
    a.nama AS nama_anak,
    'Infaq' AS jenis_pembayaran,
    ldh.infaq AS jumlah,
    NULL AS cicilan_1,
    NULL AS cicilan_2,
    ldh.keterangan,
    ldh.date AS date_pembayaran,
    'HISTORY' AS sumber
FROM anak a
LEFT JOIN laporan_dana_history ldh ON a.id = ldh.nama
WHERE ldh.infaq IS NOT NULL AND MONTH(ldh.date) = $bulan AND YEAR(ldh.date) = $tahun";


// Menambahkan filter berdasarkan tanggal untuk history
if (!empty($start_date) && !empty($end_date)) {
    $query .= " AND ldh.date BETWEEN '$start_date' AND '$end_date'";
} elseif (!empty($start_date)) {
    $query .= " AND ldh.date >= '$start_date'";
} elseif (!empty($end_date)) {
    $query .= " AND ldh.date <= '$end_date'";
}

$query .= " ORDER BY nama_anak, date_pembayaran, jenis_pembayaran";


$result = $conn->query($query);


$currentData = [];
$historyData = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        if ($row['sumber'] === 'CURRENT') {
            $currentData[] = $row;
        } else {
            $historyData[] = $row;
        }
    }
}

$totalPerJenis = []; // Array untuk menyimpan total tiap jenis pembayaran
if (!empty($currentData)) {
    foreach ($currentData as $row) {
        $jenis = $row['jenis_pembayaran'];
        $totalPerJenis[$jenis] = ($totalPerJenis[$jenis] ?? 0) + $row['jumlah'];
    }
}

$groupedData = [];
foreach ($currentData as $row) {
    $groupedData[$row['nama_anak']][] = $row;
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Pembayaran Per Bulan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

    <div class="container-fluid">
        <div class="row">
            <?php include 'sidebar.php'; ?> <!-- Include file sidebar -->

                        <main class="col-md-9 col-lg-10 ms-auto" style="margin-left: auto;">

                                    <h2 class="bg-info rounded p-4 text-white">Laporan Pembayaran Per Bulan</h2>


    <!-- Form Filter Bulan dan Tahun -->
    <form action="" method="GET" class="mb-4">
        <div class="row g-3">
            <div class="col-md-3">
                <label for="bulan" class="form-label">Bulan</label>
                <select name="bulan" id="bulan" class="form-select">
                    <?php for ($i = 1; $i <= 12; $i++): ?>
                        <option value="<?= $i ?>" <?= $i == $bulan ? 'selected' : '' ?>>
                            <?= date('F', mktime(0, 0, 0, $i, 1)) ?>
                        </option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label for="tahun" class="form-label">Tahun</label>
                <select name="tahun" id="tahun" class="form-select">
                    <?php for ($i = date('Y') - 5; $i <= date('Y') + 1; $i++): ?>
                        <option value="<?= $i ?>" <?= $i == $tahun ? 'selected' : '' ?>><?= $i ?></option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="col-md-3 d-flex align-items-end">
                <button type="submit" class="btn btn-primary">Filter</button>
            </div>
        </div>
    </form>

    <h4>Cari Lewat Tanggal</h4>
<form method="GET" action="">
    <div class="row g-2">
        <div class="col-md-4">
            <label for="start_date" class="form-label">Tanggal Mulai</label>
            <input type="date" id="start_date" name="start_date" class="form-control" 
                value="<?php echo htmlspecialchars($_GET['start_date'] ?? ''); ?>">
        </div>
        <div class="col-md-4">
            <label for="end_date" class="form-label">Tanggal Akhir</label>
            <input type="date" id="end_date" name="end_date" class="form-control" 
                value="<?php echo htmlspecialchars($_GET['end_date'] ?? ''); ?>">
        </div>
        <div class="col-md-4 d-flex align-items-end">
            <button type="submit" class="btn btn-primary">Filter</button>
            <a href="" class="btn btn-secondary ms-2">Reset</a>
        </div>
    </div>
</form>

<br>
<a href="laporan_pembayaran_anak_pdf.php?bulan=<?= $bulan ?>&tahun=<?= $tahun ?>" 
       class="btn btn-success">
        Download Laporan PDF
    </a>
<hr>


<h4>Data Baru</h4>
<table class="table table-bordered">
    <thead>
        <tr>
            <th>No</th>
            <th>Nama Anak</th>
            <th>Rincian Pembayaran</th>
        </tr>
    </thead>
    <tbody>
        <?php
        if (!empty($groupedData)) {
            $no = 1;
            foreach ($groupedData as $nama_anak => $rows) {
                echo "<tr>";
                echo "<td>" . $no++ . "</td>";
                echo "<td>" . htmlspecialchars($nama_anak) . "</td>";
                echo "<td>";
                echo "<table class='table table-sm table-bordered'>";
                echo "<thead>
                        <tr>
                            <th>Jenis Pembayaran</th>
                            <th>Jumlah</th>
                            <th>Cicilan 1</th>
                            <th>Cicilan 2</th>
                            <th>Keterangan</th>
                            <th>Date</th> <!-- New Date Column -->
                        </tr>
                      </thead>";
                echo "<tbody>";
                foreach ($rows as $row) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['jenis_pembayaran']) . "</td>";
                    echo "<td>" . number_format($row['jumlah'], 0, ',', '.') . "</td>";
                    echo "<td>" . htmlspecialchars($row['cicilan_1']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['cicilan_2']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['keterangan']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['date_pembayaran']) . "</td>";
                    echo "</tr>";
                }
                echo "</tbody>";
                echo "</table>";
                echo "</td>";
                echo "</tr>";
            }
        }
        ?>
    </tbody>
</table>


     <h4>Total Pembayaran</h4>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Jenis Pembayaran</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            <?php
            foreach ($totalPerJenis as $jenis => $total) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($jenis) . "</td>";
                echo "<td>" . number_format($total, 0, ',', '.') . "</td>";
                echo "</tr>";
            }
            ?>
        </tbody>
    </table>

    <h4>Riwayat Data</h4>
<table class="table table-bordered">
    <thead>
        <tr>
            <th>No</th>
            <th>Nama Anak</th>
            <th>Rincian Pembayaran</th>
        </tr>
    </thead>
    <tbody>
        <?php
        // Kelompokkan data berdasarkan nama anak
        $groupedHistoryData = [];
        foreach ($historyData as $row) {
            $groupedHistoryData[$row['nama_anak']][] = $row;
        }

        if (!empty($groupedHistoryData)) {
            $no = 1;
            foreach ($groupedHistoryData as $nama_anak => $rows) {
                echo "<tr>";
                echo "<td>" . $no++ . "</td>";
                echo "<td>" . htmlspecialchars($nama_anak) . "</td>";
                echo "<td>";
                echo "<table class='table table-sm table-bordered'>";
                echo "<thead>
                        <tr>
                            <th>Jenis Pembayaran</th>
                            <th>Jumlah</th>
                            <th>Cicilan 1</th>
                            <th>Cicilan 2</th>
                            <th>Keterangan</th>
                            <th>Tanggal</th>
                        </tr>
                      </thead>";
                echo "<tbody>";
                foreach ($rows as $detail) {
                    echo "<tr>
                        <td>" . htmlspecialchars($detail['jenis_pembayaran']) . "</td>
                        <td>" . number_format($detail['jumlah'], 2) . "</td>
                        <td>" . ($detail['cicilan_1'] ? number_format($detail['cicilan_1'], 2) : '-') . "</td>
                        <td>" . ($detail['cicilan_2'] ? number_format($detail['cicilan_2'], 2) : '-') . "</td>
                        <td>" . htmlspecialchars($detail['keterangan']) . "</td>
                        <td>" . htmlspecialchars(date('d-m-Y', strtotime($detail['date_pembayaran']))) . "</td>
                      </tr>";
                }
                echo "</tbody></table>";
                echo "</td></tr>";
            }
        } else {
            echo "<tr><td colspan='3' class='text-center'>Riwayat data tidak ditemukan.</td></tr>";
        }
        ?>
    </tbody>
</table>


</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php $conn->close(); ?>
