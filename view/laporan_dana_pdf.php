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

// Ambil bulan yang difilter dari parameter GET
$filter_month = isset($_GET['filter_month']) ? $_GET['filter_month'] : '';

// Query data laporan_dana dengan filter bulan jika ada
$sql = "SELECT laporan_dana.*, anak.nama AS nama_siswa 
        FROM laporan_dana 
        JOIN anak ON laporan_dana.nama = anak.id";
if (!empty($filter_month)) {
    $sql .= " WHERE DATE_FORMAT(laporan_dana.date, '%Y-%m') = '$filter_month'";
}
$result = $conn->query($sql);

// Persiapkan data untuk PDF
$data = [];
$totalKeseluruhan = [
    'pendaftaran' => 0,
    'spp_bulan' => 0,
    'seragam' => 0,
    'pengembangan_sekolah' => 0,
    'kegiatan_pembelajaran' => 0,
    'infaq' => 0,
    'total' => 0,
];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $total = $row['pendaftaran'] + $row['spp_bulan'] + $row['seragam'] + $row['pengembangan_sekolah'] + $row['kegiatan_pembelajaran'] + $row['infaq'];
        $data[] = array_merge($row, ['total' => $total]);
        $totalKeseluruhan['pendaftaran'] += $row['pendaftaran'];
        $totalKeseluruhan['spp_bulan'] += $row['spp_bulan'];
        $totalKeseluruhan['seragam'] += $row['seragam'];
        $totalKeseluruhan['pengembangan_sekolah'] += $row['pengembangan_sekolah'];
        $totalKeseluruhan['kegiatan_pembelajaran'] += $row['kegiatan_pembelajaran'];
        $totalKeseluruhan['infaq'] += $row['infaq'];
        $totalKeseluruhan['total'] += $total;
    }
}

// FPDF Library
require('../fpdf.php'); // Mengimpor library FPDF

// Membuat objek FPDF dengan orientasi landscape
$pdf = new FPDF('L', 'mm', 'A4'); // 'L' untuk landscape
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 14);

// Judul
$pdf->Cell(0, 10, 'Laporan Dana', 0, 1, 'C');
$pdf->SetFont('Arial', '', 12);
if (!empty($filter_month)) {
    $bulanTahun = date("F Y", strtotime($filter_month . "-01"));
    $pdf->Cell(0, 10, 'Periode: ' . $bulanTahun, 0, 1, 'C');
}
$pdf->Ln(10); // Memberikan jarak vertikal setelah judul

// Header Tabel
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(10, 10, 'No', 1, 0, 'C');
$pdf->Cell(40, 10, 'Nama Siswa', 1, 0, 'C');
$pdf->Cell(30, 10, 'Pendaftaran', 1, 0, 'C');
$pdf->Cell(30, 10, 'SPP Bulan', 1, 0, 'C');
$pdf->Cell(30, 10, 'Seragam', 1, 0, 'C');
$pdf->Cell(40, 10, 'Pengembangan', 1, 0, 'C');
$pdf->Cell(40, 10, 'Pembelajaran', 1, 0, 'C');
$pdf->Cell(30, 10, 'Infaq', 1, 0, 'C');
$pdf->Cell(30, 10, 'Total', 1, 1, 'C');

// Isi Tabel
$pdf->SetFont('Arial', '', 10);
$no = 1;
foreach ($data as $row) {
    $pdf->Cell(10, 10, $no++, 1, 0, 'C');
    $pdf->Cell(40, 10, $row['nama_siswa'], 1, 0, 'C');
    $pdf->Cell(30, 10, number_format($row['pendaftaran'], 0, ',', '.'), 1, 0, 'C');
    $pdf->Cell(30, 10, number_format($row['spp_bulan'], 0, ',', '.'), 1, 0, 'C');
    $pdf->Cell(30, 10, number_format($row['seragam'], 0, ',', '.'), 1, 0, 'C');
    $pdf->Cell(40, 10, number_format($row['pengembangan_sekolah'], 0, ',', '.'), 1, 0, 'C');
    $pdf->Cell(40, 10, number_format($row['kegiatan_pembelajaran'], 0, ',', '.'), 1, 0, 'C');
    $pdf->Cell(30, 10, number_format($row['infaq'], 0, ',', '.'), 1, 0, 'C');
    $pdf->Cell(30, 10, number_format($row['total'], 0, ',', '.'), 1, 1, 'C');
}

// Menampilkan Total Keseluruhan
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(50, 10, 'TOTAL', 1, 0, 'C');
$pdf->Cell(30, 10, number_format($totalKeseluruhan['pendaftaran'], 0, ',', '.'), 1, 0, 'C');
$pdf->Cell(30, 10, number_format($totalKeseluruhan['spp_bulan'], 0, ',', '.'), 1, 0, 'C');
$pdf->Cell(30, 10, number_format($totalKeseluruhan['seragam'], 0, ',', '.'), 1, 0, 'C');
$pdf->Cell(40, 10, number_format($totalKeseluruhan['pengembangan_sekolah'], 0, ',', '.'), 1, 0, 'C');
$pdf->Cell(40, 10, number_format($totalKeseluruhan['kegiatan_pembelajaran'], 0, ',', '.'), 1, 0, 'C');
$pdf->Cell(30, 10, number_format($totalKeseluruhan['infaq'], 0, ',', '.'), 1, 0, 'C');
$pdf->Cell(30, 10, number_format($totalKeseluruhan['total'], 0, ',', '.'), 1, 1, 'C');

// Output PDF
$pdf->Output();
$conn->close();
?>
