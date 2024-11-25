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

// Ambil id_anak dari URL
$id_anak = isset($_GET['id_anak']) ? $_GET['id_anak'] : 0;
if ($id_anak == 0) {
    echo "ID Anak tidak valid!";
    exit();
}

// Ambil data anak
$queryAnak = "SELECT nama FROM anak WHERE id = $id_anak";
$resultAnak = $conn->query($queryAnak);
if ($resultAnak && $resultAnak->num_rows > 0) {
    $namaAnak = $resultAnak->fetch_assoc()['nama'];
} else {
    echo "Data anak tidak ditemukan!";
    exit();
}

// Ambil data pembayaran
$query = "
    SELECT 
        jenis_pembayaran, 
        jumlah, 
        cicilan_1, 
        cicilan_2, 
        (cicilan_1 + cicilan_2) AS total_cicilan, 
        keterangan 
    FROM (
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
    ) AS pembayaran
";

$result = $conn->query($query);
$data = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
} else {
    echo "Data pembayaran tidak ditemukan!";
    exit();
}

// FPDF Library
require('../fpdf.php');
$pdf = new FPDF('L', 'mm', 'A4'); // Landscape mode
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 14);

// Judul
$pdf->Cell(0, 10, 'Rekapitulasi Pembayaran Anak', 0, 1, 'C');
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(0, 10, 'Nama Anak: ' . $namaAnak, 0, 1, 'C');
$pdf->Ln(10); // Jarak vertikal

// Header Tabel
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(10, 10, 'No', 1, 0, 'C');
$pdf->Cell(50, 10, 'Jenis Pembayaran', 1, 0, 'C');
$pdf->Cell(30, 10, 'Jumlah', 1, 0, 'C');
$pdf->Cell(30, 10, 'Cicilan 1', 1, 0, 'C');
$pdf->Cell(30, 10, 'Cicilan 2', 1, 0, 'C');
$pdf->Cell(30, 10, 'Total Cicilan', 1, 0, 'C');
$pdf->Cell(70, 10, 'Keterangan', 1, 1, 'C');

// Isi Tabel
$pdf->SetFont('Arial', '', 10);
$no = 1;
foreach ($data as $row) {
    $pdf->Cell(10, 10, $no++, 1, 0, 'C');
    $pdf->Cell(50, 10, $row['jenis_pembayaran'], 1, 0, 'C');
    $pdf->Cell(30, 10, number_format($row['jumlah'], 0, ',', '.'), 1, 0, 'C');
    $pdf->Cell(30, 10, number_format($row['cicilan_1'], 0, ',', '.'), 1, 0, 'C');
    $pdf->Cell(30, 10, number_format($row['cicilan_2'], 0, ',', '.'), 1, 0, 'C');
    $pdf->Cell(30, 10, number_format($row['total_cicilan'], 0, ',', '.'), 1, 0, 'C');
    $pdf->Cell(70, 10, $row['keterangan'], 1, 1, 'C');
}

// Output PDF
$pdf->Output();
$conn->close();
?>
