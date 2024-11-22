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
<?php
// Koneksi ke database
$host = 'localhost';
$username = 'root';
$password = '';
$dbname = 'capstone_tpa';
$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Mendapatkan id_anak dari URL atau AJAX
$id_anak = isset($_GET['id_anak']) ? $_GET['id_anak'] : (isset($_POST['id_anak']) ? $_POST['id_anak'] : 0);

// Cek apakah id_anak valid
if ($id_anak == 0) {
    echo "ID Anak tidak valid!";
    exit;
}

// Mendapatkan data anak berdasarkan id_anak
$queryAnak = "SELECT nama FROM anak WHERE id = $id_anak";
$anakResult = $conn->query($queryAnak);

if ($anakResult && $anakResult->num_rows > 0) {
    $anak = $anakResult->fetch_assoc();
    $namaAnak = $anak['nama'];
} else {
    echo "Data anak tidak ditemukan!";
    exit;
}

// Mengambil data dari laporan_dana dan rekapitulasi_pembayaran berdasarkan id_anak
if (isset($_GET['action']) && $_GET['action'] == 'load_data') {
    $query = "
        SELECT 
            'Pendaftaran' AS jenis_pembayaran, 
            ld.pendaftaran AS jumlah, 
            rp.cicilan_1, 
            rp.cicilan_2, 
            rp.keterangan
        FROM laporan_dana ld
        LEFT JOIN rekapitulasi_pembayaran rp ON ld.nama = rp.id_anak AND rp.jenis_pembayaran = 'Pendaftaran'
        WHERE ld.nama = $id_anak
        UNION ALL
        SELECT 
            'SPP Bulan', 
            ld.spp_bulan, 
            rp.cicilan_1, 
            rp.cicilan_2, 
            rp.keterangan
        FROM laporan_dana ld
        LEFT JOIN rekapitulasi_pembayaran rp ON ld.nama = rp.id_anak AND rp.jenis_pembayaran = 'SPP Bulan'
        WHERE ld.nama = $id_anak
        UNION ALL
        SELECT 
            'Seragam', 
            ld.seragam, 
            rp.cicilan_1, 
            rp.cicilan_2, 
            rp.keterangan
        FROM laporan_dana ld
        LEFT JOIN rekapitulasi_pembayaran rp ON ld.nama = rp.id_anak AND rp.jenis_pembayaran = 'Seragam'
        WHERE ld.nama = $id_anak
        UNION ALL
        SELECT 
            'Pengembangan Sekolah', 
            ld.pengembangan_sekolah, 
            rp.cicilan_1, 
            rp.cicilan_2, 
            rp.keterangan
        FROM laporan_dana ld
        LEFT JOIN rekapitulasi_pembayaran rp ON ld.nama = rp.id_anak AND rp.jenis_pembayaran = 'Pengembangan Sekolah'
        WHERE ld.nama = $id_anak
        UNION ALL
        SELECT 
            'Kegiatan Pembelajaran', 
            ld.kegiatan_pembelajaran, 
            rp.cicilan_1, 
            rp.cicilan_2, 
            rp.keterangan
        FROM laporan_dana ld
        LEFT JOIN rekapitulasi_pembayaran rp ON ld.nama = rp.id_anak AND rp.jenis_pembayaran = 'Kegiatan Pembelajaran'
        WHERE ld.nama = $id_anak
        UNION ALL
        SELECT 
            'Keterlambatan', 
            ld.keterlambatan AS jumlah, 
            rp.cicilan_1, 
            rp.cicilan_2, 
            rp.keterangan
        FROM laporan_dana ld
        LEFT JOIN rekapitulasi_pembayaran rp ON ld.nama = rp.id_anak AND rp.jenis_pembayaran = 'Keterlambatan'
        WHERE ld.nama = $id_anak
    ";

    $result = $conn->query($query);

    if ($result && $result->num_rows > 0) {
        $no = 1;
        while ($row = $result->fetch_assoc()) {
            $totalCicilan = $row['cicilan_1'] + $row['cicilan_2'];
            echo "<tr>
                    <td>{$no}</td>
                    <td>{$row['jenis_pembayaran']}</td>
                    <td>{$row['jumlah']}</td>
                    <td><input type='number' name='cicilan_1[{$row['jenis_pembayaran']}]' class='form-control' value='{$row['cicilan_1']}'></td>
                    <td><input type='number' name='cicilan_2[{$row['jenis_pembayaran']}]' class='form-control' value='{$row['cicilan_2']}'></td>
                    <td>{$totalCicilan}</td>
                    <td><input type='text' name='keterangan[{$row['jenis_pembayaran']}]' class='form-control' value='{$row['keterangan']}'></td>
                  </tr>";
            $no++;
        }
    } else {
        echo "<tr><td colspan='7'>Data tidak ditemukan</td></tr>";
    }
    exit;
}

// Update records in rekapitulasi_pembayaran
if (isset($_POST['action']) && $_POST['action'] == 'save_data') {
    $id_anak = $_POST['id_anak'];
    $cicilan_1 = $_POST['cicilan_1'];
    $cicilan_2 = $_POST['cicilan_2'];
    $keterangan = $_POST['keterangan'];

    foreach ($cicilan_1 as $jenis => $cicil1) {
        $cicil2 = $cicilan_2[$jenis] ?? 0;
        $ket = $keterangan[$jenis] ?? '';

        $stmt = $conn->prepare("UPDATE rekapitulasi_pembayaran SET cicilan_1 = ?, cicilan_2 = ?, keterangan = ? WHERE id_anak = ? AND jenis_pembayaran = ?");
        $stmt->bind_param("iisis", $cicil1, $cicil2, $ket, $id_anak, $jenis);
        $stmt->execute();
    }

    echo "Data berhasil disimpan!";
    exit;
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Rekapitulasi Pembayaran Anak</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container-fluid">
    <div class="row">
        <?php include 'sidebar.php'; ?>
        <main class="col-md-9 col-lg-10 ms-auto" style="margin-left: auto;">
            <h2 class="bg-info rounded p-4 text-white">Detail Rekapitulasi Pembayaran</h2>
            <p><strong>Nama Anak:</strong> <?= htmlspecialchars($namaAnak) ?></p>

            <form id="paymentForm">
                <table class="table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Jenis Pembayaran</th>
                            <th>Jumlah</th>
                            <th>Cicilan 1</th>
                            <th>Cicilan 2</th>
                            <th>Total Cicilan</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody id="paymentTable"></tbody>
                </table>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </form>
        </main>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

<script>
$(document).ready(function() {
    function loadPaymentData() {
        $.ajax({
            url: 'rekapitulasi_pembayaran_detail.php',
            type: 'GET',
            data: { id_anak: <?= $id_anak ?>, action: 'load_data' },
            success: function(data) {
                $('#paymentTable').html(data);
            }
        });
    }

    loadPaymentData();

     $('#paymentForm').on('submit', function(e) {
        e.preventDefault();
        $.ajax({
            url: 'rekapitulasi_pembayaran_detail.php',
            type: 'POST',
            data: $(this).serialize() + '&id_anak=<?= $id_anak ?>&action=save_data',
            success: function(response) {
                alert(response);
                loadPaymentData();
            },
            error: function(xhr, status, error) {
                console.error("AJAX Error: " + status + ": " + error);
            }
        });
    });
});
</script>

</body>
</html>
