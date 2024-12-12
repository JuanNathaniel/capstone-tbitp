<?php
session_start();

// Cek apakah pengguna sudah login
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

// Regenerasi ID sesi untuk keamanan ekstra
session_regenerate_id(true);

include '../includes/koneksi.php';

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
    
    $query = "SELECT * FROM rekapitulasi_pembayaran WHERE id_anak = $id_anak";
    //     SELECT 
    //         rp.jenis_pembayaran,
    //         COALESCE(ld.jumlah, 0) AS jumlah, 
    //         rp.cicilan_1, 
    //         rp.cicilan_2, 
    //         rp.keterangan
    //     FROM (
    //         SELECT 'Pendaftaran' AS jenis_pembayaran, pendaftaran AS jumlah FROM laporan_dana WHERE nama = $id_anak
    //         UNION ALL
    //         SELECT 'SPP Bulan', spp_bulan FROM laporan_dana WHERE nama = $id_anak
    //         UNION ALL
    //         SELECT 'Seragam', seragam FROM laporan_dana WHERE nama = $id_anak
    //         UNION ALL
    //         SELECT 'Pengembangan Sekolah', pengembangan_sekolah FROM laporan_dana WHERE nama = $id_anak
    //         UNION ALL
    //         SELECT 'Kegiatan Pembelajaran', kegiatan_pembelajaran FROM laporan_dana WHERE nama = $id_anak
    //         UNION ALL
    //         SELECT 'Keterlambatan', keterlambatan FROM laporan_dana WHERE nama = $id_anak
    //     ) AS ld
    //     LEFT JOIN rekapitulasi_pembayaran rp 
    //         ON ld.jenis_pembayaran = rp.jenis_pembayaran
    //         AND rp.id_anak = $id_anak
    // ";


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

// if (isset($_POST['action']) && $_POST['action'] == 'save_data_lainnya') {
//     $cicilan_1 = $_POST['cicilan_1'];
//     $cicilan_2 = $_POST['cicilan_2'];
//     $keterangan = $_POST['keterangan'];
//     $jenis_pembayaran = $_POST['jenis_pembayaran'];

//     $stmt = $conn->prepare("INSERT INTO rekapitulasi_pembayaran (id_anak, jenis_pembayaran, cicilan_1, cicilan_2, keterangan) VALUES (?, ?, ?, ?, ?)");
//     $stmt->bind_param("isiis", $id_anak, $jenis, $cicilan_1, $cicilan_2, $keterangan);
//     if (!$stmt->execute()) {
//         echo "Error: " . $stmt->error;
//         exit();
//     }

//     echo "Data berhasil disimpan!". $jenis_pembayaran;
//     exit;
// }

// // Simpan data rekapitulasi pembayaran
// if (isset($_POST['action']) && $_POST['action'] == 'save_data') {
//     $cicilan_1 = $_POST['cicilan_1'];
//     $cicilan_2 = $_POST['cicilan_2'];
//     $keterangan = $_POST['keterangan'];

//     foreach ($cicilan_1 as $jenis => $cicil1) {
//         $cicil2 = isset($cicilan_2[$jenis]) ? $cicilan_2[$jenis] : 0;
//         $ket = isset($keterangan[$jenis]) ? $keterangan[$jenis] : '';

//         // Cek apakah data sudah ada di database
//         $checkQuery = "SELECT * FROM rekapitulasi_pembayaran WHERE id_anak = ? AND jenis_pembayaran = ?";
//         $checkStmt = $conn->prepare($checkQuery);
//         $checkStmt->bind_param("is", $id_anak, $jenis);
//         $checkStmt->execute();
//         $checkResult = $checkStmt->get_result();

//         if ($checkResult->num_rows > 0) {
//             // Data sudah ada, lakukan update
//             $stmt = $conn->prepare("UPDATE rekapitulasi_pembayaran SET cicilan_1 = ?, cicilan_2 = ?, keterangan = ? WHERE id_anak = ? AND jenis_pembayaran = ?");
//             $stmt->bind_param("iisis", $cicil1, $cicil2, $ket, $id_anak, $jenis);
//             if (!$stmt->execute()) {
//                 echo "Error: " . $stmt->error;
//                 exit();
//             }
//         } else {
//             // Data belum ada, lakukan insert
//             $stmt = $conn->prepare("INSERT INTO rekapitulasi_pembayaran (id_anak, jenis_pembayaran, cicilan_1, cicilan_2, keterangan) VALUES (?, ?, ?, ?, ?)");
//             $stmt->bind_param("isiis", $id_anak, $jenis, $cicil1, $cicil2, $ket);
//             if (!$stmt->execute()) {
//                 echo "Error: " . $stmt->error;
//                 exit();
//             }
//         }
//     }

//     echo "Data berhasil disimpan!";
//     exit;
// }
// if (isset($_POST['action']) && $_POST['action'] == 'save_data_lainnya') {
//     $cicilan_1 = $_POST['cicilan_1'];
//     $cicilan_2 = $_POST['cicilan_2'];
//     $keterangan = $_POST['keterangan'];
//     $jenis_pembayaran = $_POST['jenis_pembayaran'];
//     $id_anak = $_POST['id_anak']; // Pastikan id_anak diterima dari form.

//     // Mengecek apakah jenis_pembayaran terisi
//     if (!empty($jenis_pembayaran)) {
//         // Simpan data jenis pembayaran baru
//         foreach ($jenis_pembayaran as $key => $jenis) {
//             $jenis = isset($jenis_pembayaran[$key]) ? $jenis_pembayaran[$key] : 'tes';
//             $cicil1 = isset($cicilan_1[$key]) ? $cicilan_1[$key] : 0;
//             $cicil2 = isset($cicilan_2[$key]) ? $cicilan_2[$key] : 0;
//             $ket = isset($keterangan[$key]) ? $keterangan[$key] : '';

//             // Insert data rekapitulasi pembayaran baru
//             $stmt = $conn->prepare("INSERT INTO rekapitulasi_pembayaran (id_anak, jenis_pembayaran, cicilan_1, cicilan_2, keterangan) VALUES (?, ?, ?, ?, ?)");
//             $stmt->bind_param("isiis", $id_anak, $jenis, $cicil1, $cicil2, $ket); // Perbaikan tipe data
//             if (!$stmt->execute()) {
//                 echo "Error: " . $stmt->error;
//                 exit();
//             }
//         }
//         echo "Data jenis pembayaran baru berhasil disimpan!";
//     } else {
//         echo "Jenis pembayaran tidak terisi.";
//     }
//     exit;
// }


// // Simpan data rekapitulasi pembayaran untuk baris yang sudah ada
// if (isset($_POST['action']) && $_POST['action'] == 'save_data') {
//     $cicilan_1 = $_POST['cicilan_1'];
//     $cicilan_2 = $_POST['cicilan_2'];
//     $keterangan = $_POST['keterangan'];

//     // Loop untuk update data yang sudah ada
//     foreach ($cicilan_1 as $jenis => $cicil1) {
//         $cicil2 = isset($cicilan_2[$jenis]) ? $cicilan_2[$jenis] : 0;
//         $ket = isset($keterangan[$jenis]) ? $keterangan[$jenis] : '';

//         // Cek apakah data sudah ada di database
//         $checkQuery = "SELECT * FROM rekapitulasi_pembayaran WHERE id_anak = ? AND jenis_pembayaran = ?";
//         $checkStmt = $conn->prepare($checkQuery);
//         $checkStmt->bind_param("is", $id_anak, $jenis);
//         $checkStmt->execute();
//         $checkResult = $checkStmt->get_result();

//         if ($checkResult->num_rows > 0) {
//             // Data sudah ada, lakukan update
//             $stmt = $conn->prepare("UPDATE rekapitulasi_pembayaran SET cicilan_1 = ?, cicilan_2 = ?, keterangan = ? WHERE id_anak = ? AND jenis_pembayaran = ?");
//             $stmt->bind_param("iisis", $cicil1, $cicil2, $ket, $id_anak, $jenis);
//             if (!$stmt->execute()) {
//                 echo "Error: " . $stmt->error;
//                 exit();
//             }
//         } else {
//             // Data belum ada, lakukan insert
//             $stmt = $conn->prepare("INSERT INTO rekapitulasi_pembayaran (id_anak, jenis_pembayaran, cicilan_1, cicilan_2, keterangan) VALUES (?, ?, ?, ?, ?)");
//             $stmt->bind_param("isiis", $id_anak, $jenis, $cicil1, $cicil2, $ket);
//             if (!$stmt->execute()) {
//                 echo "Error: " . $stmt->error;
//                 exit();
//             }
//         }
//     }

//     echo "Data berhasil disimpan!";
//     exit;
// }

// Simpan data rekapitulasi pembayaran untuk baris yang sudah ada
if (isset($_POST['action']) && $_POST['action'] == 'save_data') {
    $cicilan_1 = $_POST['cicilan_1'];
    $cicilan_2 = $_POST['cicilan_2'];
    $keterangan = $_POST['keterangan'];
    
    $id_anak = $_POST['id_anak']; // Pastikan id_anak diterima dari form.
    

    if (!empty($_POST['jenis_pembayaran'])) {
        $jenis_pembayaran = $_POST['jenis_pembayaran'];
        $jumlah = $_POST = $_POST['jumlah'];
        foreach ($jenis_pembayaran as $key => $jenis) {
            $jenis = isset($jenis_pembayaran[$key]) ? $jenis_pembayaran[$key] : 'null';
            $jumlah = isset($jumlah[$key]) ? $jumlah[$key] : 0;
            $cicil1 = isset($cicilan_1[$key]) ? $cicilan_1[$key] : 0;
            $cicil2 = isset($cicilan_2[$key]) ? $cicilan_2[$key] : 0;
            $ket = isset($keterangan[$key]) ? $keterangan[$key] : '';

            // Insert data rekapitulasi pembayaran baru
            $stmt = $conn->prepare("INSERT INTO rekapitulasi_pembayaran (id_anak, jenis_pembayaran, jumlah, cicilan_1, cicilan_2, keterangan) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("isdiis", $id_anak, $jenis,  $jumlah, $cicil1, $cicil2, $ket); // Perbaikan tipe data
            if (!$stmt->execute()) {
                echo "Error: " . $stmt->error;
                exit();
            }
        }
        echo "Data jenis pembayaran baru berhasil disimpan!";
        exit();
    } else {
        // Loop untuk update data yang sudah ada
        foreach ($cicilan_1 as $jenis => $cicil1) {
            $cicil2 = isset($cicilan_2[$jenis]) ? $cicilan_2[$jenis] : 0;
            $ket = isset($keterangan[$jenis]) ? $keterangan[$jenis] : '';

            // Cek apakah data sudah ada di database
            $checkQuery = "SELECT * FROM rekapitulasi_pembayaran WHERE id_anak = ? AND jenis_pembayaran = ?";
            $checkStmt = $conn->prepare($checkQuery);
            $checkStmt->bind_param("is", $id_anak, $jenis);
            $checkStmt->execute();
            $checkResult = $checkStmt->get_result();

            if ($checkResult->num_rows > 0) {
                // Data sudah ada, lakukan update
                $stmt = $conn->prepare("UPDATE rekapitulasi_pembayaran SET cicilan_1 = ?, cicilan_2 = ?, keterangan = ? WHERE id_anak = ? AND jenis_pembayaran = ?");
                $stmt->bind_param("iisis", $cicil1, $cicil2, $ket, $id_anak, $jenis);
                if (!$stmt->execute()) {
                    echo "Error: " . $stmt->error;
                    exit();
                }
            } else {
                // Data belum ada, lakukan insert
                $stmt = $conn->prepare("INSERT INTO rekapitulasi_pembayaran (id_anak, jenis_pembayaran, cicilan_1, cicilan_2, keterangan) VALUES (?, ?, ?, ?, ?)");
                $stmt->bind_param("isiis", $id_anak, $jenis, $cicil1, $cicil2, $ket);
                if (!$stmt->execute()) {
                    echo "Error: " . $stmt->error;
                    exit();
                }
            }
        }
        echo "Data berhasil disimpan!";
        exit;
    }
    
}



?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Rekapitulasi Pembayaran Anak</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .transition-bg {
            background: linear-gradient(to right, #344EAD, #1767A6); /* Gradasi horizontal */
        }
    </style>
</head>
<body>

<div class="container-fluid">
    <div class="row">
        <?php include 'sidebar.php'; ?>
        <main class="col-md-9 col-lg-10 ms-auto" style="margin-left: auto;">
            <h2 class="bg-info rounded p-4 text-white transition-bg">Detail Rekapitulasi Pembayaran</h2>
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
                <a href="rekapitulasi_anak_pdf.php?id_anak=<?= $id_anak ?>" class="btn btn-success">Download PDF</a>
                <button type="button" id="addRowButton" class="btn btn-secondary">Tambah Pembayaran</button>
            </form>
        </main>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

<script>
$(document).ready(function() {
    let rowCount = 1; // Untuk menambahkan nomor baris yang unik

    // Memuat data pembayaran yang sudah ada
    function loadPaymentData() {
        $.ajax({
            url: 'rekapitulasi_pembayaran_detail.php',
            type: 'GET',
            data: { id_anak: <?= $id_anak ?>, action: 'load_data' },
            success: function(data) {
                $('#paymentTable').html(data);
                rowCount = $('#paymentTable tr').length + 1; // Update row count setelah data dimuat
            }
        });
    }

    // Fungsi untuk menambah baris pembayaran baru
    function addRow() {
        const newRow = `
            <tr>
                <td>${rowCount}</td>
                <td><input type="text" name="jenis_pembayaran[]" class="form-control" required></td>
                <td><input type="number" name="jumlah[]" class="form-control" required></td>
                <td><input type="number" name="cicilan_1[]" class="form-control"></td>
                <td><input type="number" name="cicilan_2[]" class="form-control"></td>
                <td><span class="total_cicilan">0</span></td>
                <td><input type="text" name="keterangan[]" class="form-control"></td>
            </tr>
        `;
        $('#paymentTable').append(newRow);
        rowCount++;
    }

    // Fungsi untuk menghitung total cicilan setiap baris
    function calculateTotalCicilan() {
        $('#paymentTable tr').each(function() {
            const cicilan1 = parseFloat($(this).find('input[name^="cicilan_1"]').val()) || 0;
            const cicilan2 = parseFloat($(this).find('input[name^="cicilan_2"]').val()) || 0;
            const totalCicilan = cicilan1 + cicilan2;
            $(this).find('.total_cicilan').text(totalCicilan);
        });
    }

    // Event listener untuk tombol tambah baris
    $('#addRowButton').click(function() {
        addRow();
    });

    // Memuat data pembayaran ketika halaman pertama kali dimuat
    loadPaymentData();

    // Event listener untuk submit form
    $('#paymentForm').on('submit', function(e) {
        e.preventDefault();
        calculateTotalCicilan(); // Hitung total cicilan sebelum submit
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

    // Update total cicilan setiap kali ada perubahan pada cicilan
    $('#paymentTable').on('input', 'input[name^="cicilan_1"], input[name^="cicilan_2"]', function() {
        calculateTotalCicilan();
    });
});

</script>

</body>
</html>
