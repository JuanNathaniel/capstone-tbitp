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

// Menangani input tahun ajaran
$tahunAjaran = isset($_GET['tahun_ajaran']) ? $_GET['tahun_ajaran'] : '';

// Ambil semua jenis rincian biaya pendidikan
$sqlJenis = "SELECT * FROM jenis_rincian_biaya_pendidikan";
$resultJenis = $conn->query($sqlJenis);
$jenisPendidikan = [];

while ($row = $resultJenis->fetch_assoc()) {
    $jenisPendidikan[$row['id']] = [
        'jenis_pendidikan' => $row['jenis_pendidikan'],
        'keterangan' => $row['keterangan'],
    ];
}

// Ambil rincian biaya per jenis pendidikan
$rincianBiaya = [];
foreach ($jenisPendidikan as $idJenis => $jenis) {
    $sql = "SELECT * FROM rincian_biaya_pendidikan WHERE id_jenis='$idJenis' AND tahun_ajaran='$tahunAjaran'";
    $result = $conn->query($sql);
    $rincianBiaya[$idJenis] = $result->fetch_all(MYSQLI_ASSOC);
}

// FPDF Library
require('../fpdf.php');  // Mengimpor library FPDF

// Membuat objek FPDF
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 12);

// Judul
$pdf->Cell(0, 10, 'Rincian Biaya Pendidikan - Tahun Ajaran: ' . htmlspecialchars($tahunAjaran), 0, 1, 'C');
$pdf->Ln(10);

// Tampilkan rincian biaya per jenis pendidikan
$pdf->SetFont('Arial', 'B', 10);

foreach ($jenisPendidikan as $idJenis => $jenis) {
    // Judul dan keterangan jenis pendidikan
    $pdf->Cell(0, 10, 'Jenis Pendidikan: ' . htmlspecialchars($jenis['jenis_pendidikan']), 0, 1);
    $pdf->Cell(0, 10, 'Keterangan: ' . htmlspecialchars($jenis['keterangan']), 0, 1);
    $pdf->Ln(5);

    // Tabel Header
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(100, 10, 'Uraian', 1, 0, 'C');
    $pdf->Cell(40, 10, 'Biaya (IDR)', 1, 1, 'C');

    // Tampilkan rincian biaya
    $pdf->SetFont('Arial', '', 10);
    $totalBiaya = 0;
    if (!empty($rincianBiaya[$idJenis])) {
        foreach ($rincianBiaya[$idJenis] as $row) {
            $pdf->Cell(100, 10, htmlspecialchars($row['uraian']), 1, 0, 'C');
            $pdf->Cell(40, 10, number_format($row['biaya'], 2, ',', '.'), 1, 1, 'C');
            $totalBiaya += $row['biaya'];
        }
    }

    // Menampilkan total biaya untuk jenis pendidikan ini
    $pdf->Cell(100, 10, 'Total Biaya', 1, 0, 'C');
    $pdf->Cell(40, 10, number_format($totalBiaya, 2, ',', '.'), 1, 1, 'C');
    $pdf->Ln(10); // Memberikan jarak antar jenis pendidikan
}

// Output PDF
$pdf->Output();
$conn->close();
?>
