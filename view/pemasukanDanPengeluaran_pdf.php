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

// Query data pemasukan dan pengeluaran berdasarkan bulan
$sql = "SELECT * FROM pemasukan_dan_pengeluaran";
if (!empty($filter_month)) {
    $sql .= " WHERE DATE_FORMAT(date, '%Y-%m') = '$filter_month'";
}
$result = $conn->query($sql);

// Persiapkan data untuk PDF
$data = [];
$totalPemasukan = 0;
$totalPengeluaran = 0;

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
        $totalPemasukan += $row['total_pemasukan'];
        $totalPengeluaran += $row['total_pengeluaran'];
    }
}

// FPDF Library
require('../fpdf.php');  // Mengimpor library FPDF

// Membuat objek FPDF
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 12);

// Judul
$pdf->Cell(0, 10, 'Laporan Pemasukan dan Pengeluaran', 0, 1, 'C');
if (!empty($filter_month)) {
    $bulanTahun = date("F Y", strtotime($filter_month . "-01"));
    $pdf->Cell(0, 10, 'Periode: ' . $bulanTahun, 0, 1, 'C');
}
$pdf->Ln(10);

// Header Tabel
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(10, 10, 'No', 1, 0, 'C');
$pdf->Cell(50, 10, 'Pemasukan', 1, 0, 'C');
$pdf->Cell(30, 10, 'Total Pemasukan', 1, 0, 'C');
$pdf->Cell(50, 10, 'Pengeluaran', 1, 0, 'C');
$pdf->Cell(30, 10, 'Total Pengeluaran', 1, 0, 'C');
$pdf->Cell(30, 10, 'Tanggal', 1, 1, 'C');

// Isi Tabel
$pdf->SetFont('Arial', '', 10);
$no = 1;
foreach ($data as $row) {
    $pdf->Cell(10, 10, $no++, 1, 0, 'C');
    $pdf->Cell(50, 10, $row['pemasukan'], 1, 0, 'C');
    $pdf->Cell(30, 10, number_format($row['total_pemasukan'], 0, ',', '.'), 1, 0, 'C');
    $pdf->Cell(50, 10, $row['pengeluaran'], 1, 0, 'C');
    $pdf->Cell(30, 10, number_format($row['total_pengeluaran'], 0, ',', '.'), 1, 0, 'C');
    $pdf->Cell(30, 10, date("d-m-Y", strtotime($row['date'])), 1, 1, 'C');
}

// Menampilkan Total Pemasukan dan Pengeluaran
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(60, 10, 'TOTAL', 1, 0, 'C');
$pdf->Cell(30, 10, number_format($totalPemasukan, 0, ',', '.'), 1, 0, 'C');
$pdf->Cell(50, 10, '', 1, 0, 'C');
$pdf->Cell(30, 10, number_format($totalPengeluaran, 0, ',', '.'), 1, 0, 'C');
$pdf->Cell(30, 10, '', 1, 1, 'C');

// Output PDF
$pdf->Output();
$conn->close();
?>
