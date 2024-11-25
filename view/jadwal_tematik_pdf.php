<?php
session_start();

// Cek apakah pengguna sudah login
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

require('../fpdf.php'); // Mengimpor library FPDF

// Koneksi ke database
$conn = new mysqli("localhost", "root", "", "capstone_tpa");
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Ambil data dari database
$queryTema = "SELECT * FROM jadwal_tematik";
$resultTema = $conn->query($queryTema);

// Buat dokumen PDF
$pdf = new FPDF('L', 'mm', 'A4');
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(190, 10, 'Laporan Jadwal Tematik', 0, 1, 'C');
$pdf->Ln(10); // Spasi

// Iterasi setiap tema
while ($tema = $resultTema->fetch_assoc()) {
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(277, 8, 'Tema: ' . $tema['tema'], 1, 1, 'L');
    
    // Tabel kegiatan
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(10, 8, 'No', 1, 0, 'C');
    $pdf->Cell(20, 8, 'KD', 1, 0, 'C');
    $pdf->Cell(50, 8, 'Sub Tema', 1, 0, 'C');
    $pdf->Cell(20, 8, 'Minggu', 1, 0, 'C');
    $pdf->Cell(30, 8, 'Tanggal', 1, 0, 'C');
    $pdf->Cell(147, 8, 'Kegiatan Bersama', 1, 1, 'C');

    // Ambil data kegiatan berdasarkan tema
    $queryKegiatan = "SELECT * FROM jadwal_kegiatan WHERE id_tematik = " . $tema['id'];
    $resultKegiatan = $conn->query($queryKegiatan);

    $no = 1;
    while ($kegiatan = $resultKegiatan->fetch_assoc()) {
        $pdf->Cell(10, 8, $no++, 1, 0, 'C'); // Kolom No
        $pdf->Cell(20, 8, $kegiatan['kd'], 1, 0, 'C'); // Kolom KD
        $pdf->Cell(50, 8, $kegiatan['sub_tema'], 1, 0, 'L'); // Kolom Sub Tema
        $pdf->Cell(20, 8, $kegiatan['jumlah_minggu'], 1, 0, 'C'); // Kolom Minggu
        $pdf->Cell(30, 8, $kegiatan['date'], 1, 0, 'C'); // Kolom Tanggal

        // Simpan posisi awal kolom "Kegiatan Bersama"
        $x = $pdf->GetX();
        $y = $pdf->GetY();

        // Tampilkan "Kegiatan Bersama" dengan MultiCell
        $pdf->MultiCell(147, 8, $kegiatan['kegiatan_bersama'], 1, 'L');

        // Kembali ke awal baris berikutnya
        $pdf->SetXY($x - 277 + 147, $y + 8);
    }

    // Tambahkan spasi antar tema
    $pdf->Ln(5);
}

// Output file PDF
$pdf->Output('I', 'Jadwal_Tematik.pdf');
?>
