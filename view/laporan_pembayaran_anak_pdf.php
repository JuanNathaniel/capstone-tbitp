<?php
session_start();

require('../fpdf.php'); // Mengimpor library FPDF

// Cek apakah pengguna sudah login
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

// Koneksi ke database
include '../includes/koneksi.php';

// Default bulan dan tahun saat ini
$bulan = isset($_GET['bulan']) ? (int)$_GET['bulan'] : date('m');
$tahun = isset($_GET['tahun']) ? (int)$_GET['tahun'] : date('Y');

// Query untuk mendapatkan data utama dan riwayat
$query = <<<SQL
SELECT 
    a.nama AS nama_anak,
    ld.jenis_pembayaran,
    ld.jumlah,
    rp.cicilan_1,
    rp.cicilan_2,
    rp.keterangan,
    ld.date AS date_pembayaran
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
ORDER BY a.nama, ld.date;
SQL;

$result = $conn->query($query);

$data = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $data[$row['nama_anak']][] = $row;
    }
}

// Inisialisasi FPDF dengan landscape
$pdf = new FPDF('L', 'mm', 'A4');
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(0, 10, "Laporan Pembayaran Anak Didik", 0, 1, 'C');
$pdf->Ln(5);

// Info Bulan dan Tahun
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(0, 10, "Bulan: " . date('F', mktime(0, 0, 0, $bulan, 1)) . " $tahun", 0, 1);
$pdf->Ln(5);

// Tabel data
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(10, 7, 'No', 1);
$pdf->Cell(50, 7, 'Nama Anak', 1);
$pdf->Cell(60, 7, 'Jenis Pembayaran', 1);
$pdf->Cell(30, 7, 'Jumlah', 1);
$pdf->Cell(30, 7, 'Cicilan 1', 1);
$pdf->Cell(30, 7, 'Cicilan 2', 1);
$pdf->Cell(60, 7, 'Keterangan', 1);
$pdf->Ln();

$pdf->SetFont('Arial', '', 10);
$no = 1;
foreach ($data as $nama_anak => $records) {
    $isFirstRow = true;
    foreach ($records as $record) {
        $pdf->Cell(10, 6, $isFirstRow ? $no++ : '', 1);
        $pdf->Cell(50, 6, $isFirstRow ? $nama_anak : '', 1); // Hanya tampilkan nama pada baris pertama
        $pdf->Cell(60, 6, $record['jenis_pembayaran'], 1);
        $pdf->Cell(30, 6, number_format($record['jumlah'], 0, ',', '.'), 1);
        $pdf->Cell(30, 6, $record['cicilan_1'] ? number_format($record['cicilan_1'], 0, ',', '.') : '-', 1);
        $pdf->Cell(30, 6, $record['cicilan_2'] ? number_format($record['cicilan_2'], 0, ',', '.') : '-', 1);
        $pdf->Cell(60, 6, $record['keterangan'], 1);
        $pdf->Ln();
        $isFirstRow = false; // Nama anak tidak muncul lagi pada baris berikutnya
    }
}

// Output PDF
$pdf->Output('I', 'Laporan_Pembayaran_Anak.pdf');

// Close database connection
$conn->close();
?>
