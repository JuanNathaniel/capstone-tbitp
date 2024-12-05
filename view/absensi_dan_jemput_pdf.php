<?php
require('../fpdf.php'); // Mengimpor library FPDF
include '../includes/koneksi.php'; // Koneksi database

// Ambil filter dari query string
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

// Tambahkan kondisi filter
if ($filterDate) {
    $sql .= " AND DATE(absen.date) = :filterDate";
}
if ($filterMonth) {
    $currentYear = date('Y'); // Tahun sekarang
    $sql .= " AND MONTH(absen.date) = :filterMonth AND YEAR(absen.date) = :filterYear";
}

$stmt = $pdo->prepare($sql);

// Bind parameter
if ($filterDate) {
    $stmt->bindParam(':filterDate', $filterDate);
}
if ($filterMonth) {
    $currentYear = date('Y'); // Tahun sekarang
    $stmt->bindParam(':filterMonth', $filterMonth, PDO::PARAM_INT);
    $stmt->bindParam(':filterYear', $currentYear, PDO::PARAM_INT);
}

$stmt->execute();
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Buat dokumen PDF
$pdf = new FPDF('L', 'mm', 'A4');
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(277, 10, 'Laporan Absensi Datang dan Jemput', 0, 1, 'C');
$pdf->Ln(5);

// Informasi filter
$pdf->SetFont('Arial', '', 10);
if ($filterDate) {
    $pdf->Cell(277, 6, 'Tanggal: ' . $filterDate, 0, 1, 'L');
}
if ($filterMonth) {
    $monthName = date('F', mktime(0, 0, 0, $filterMonth, 1));
    $pdf->Cell(277, 6, 'Bulan: ' . $monthName . ' ' . $currentYear, 0, 1, 'L');
}
$pdf->Ln(5);

// Header tabel
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(10, 8, 'No', 1, 0, 'C');
$pdf->Cell(30, 8, 'Nama Siswa', 1, 0, 'C');
$pdf->Cell(40, 8, 'Nama Pengantar', 1, 0, 'C');
$pdf->Cell(25, 8, 'Jam Datang', 1, 0, 'C');
$pdf->Cell(30, 8, 'Paraf Pengantar', 1, 0, 'C');
$pdf->Cell(40, 8, 'Nama Penjemput', 1, 0, 'C');
$pdf->Cell(25, 8, 'Jam Jemput', 1, 0, 'C');
$pdf->Cell(30, 8, 'Paraf Penjemput', 1, 0, 'C');
$pdf->Cell(47, 8, 'Tanggal', 1, 1, 'C');

// Data tabel
$pdf->SetFont('Arial', '', 9);
$no = 1;
foreach ($results as $row) {
    $pdf->Cell(10, 8, $no++, 1, 0, 'C'); // No
    $pdf->Cell(30, 8, $row['nama_siswa'], 1, 0, 'L'); // Nama Siswa
    $pdf->Cell(40, 8, $row['nama_pengantar'], 1, 0, 'L'); // Nama Pengantar
    $pdf->Cell(25, 8, $row['jam_datang'], 1, 0, 'C'); // Jam Datang
    $pdf->Cell(30, 8, $row['paraf_pengantar'] ? 'v' : '-', 1, 0, 'C'); // Paraf Pengantar
    $pdf->Cell(40, 8, $row['nama_penjemput'], 1, 0, 'L'); // Nama Penjemput
    $pdf->Cell(25, 8, $row['jam_jemput'], 1, 0, 'C'); // Jam Jemput
    $pdf->Cell(30, 8, $row['paraf_penjemput'] ? 'v' : '-', 1, 0, 'C'); // Paraf Penjemput
    $pdf->Cell(47, 8, $row['date'], 1, 1, 'C'); // Tanggal
}

// Output file PDF
$pdf->Output('I', 'Absensi_Dan_Jemput.pdf');
?>
