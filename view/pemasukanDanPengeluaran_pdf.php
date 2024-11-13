<?php
require('../fpdf.php');  // Mengimpor library FPDF

// Cek apakah pengguna sudah login
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

function generatePDF() {
    // Koneksi ke database
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "capstone_tpa";
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Cek koneksi
    if ($conn->connect_error) {
        die("Koneksi gagal: " . $conn->connect_error);
    }

    // Filter data berdasarkan bulan
    $sql = "SELECT * FROM pemasukan_dan_pengeluaran";
    if (isset($_GET['filter_month'])) {
        $filter_month = $_GET['filter_month'];
        $sql .= " WHERE DATE_FORMAT(date, '%Y-%m') = '$filter_month'";
    }

    $result = $conn->query($sql);
    $totalPemasukan = 0;
    $totalPengeluaran = 0;

    // Membuat objek FPDF
    $pdf = new FPDF();
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 12);

    // Judul
    $pdf->Cell(0, 10, 'Pemasukan dan Pengeluaran', 0, 1, 'C');
    $pdf->Ln(5);

    // Tabel Header
    $pdf->Cell(10, 10, 'No', 1, 0, 'C');
    $pdf->Cell(40, 10, 'Pemasukan', 1, 0, 'C');
    $pdf->Cell(35, 10, 'Total Pemasukan', 1, 0, 'C');  // Lebar dikurangi
    $pdf->Cell(40, 10, 'Pengeluaran', 1, 0, 'C');
    $pdf->Cell(35, 10, 'Total Pengeluaran', 1, 0, 'C');  // Lebar dikurangi
    $pdf->Cell(30, 10, 'Tanggal', 1, 1, 'C');  // Tanggal

    // Data Tabel
    $no = 1;
    $pdf->SetFont('Arial', '', 10);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $pdf->Cell(10, 10, $no++, 1, 0, 'C');
            $pdf->Cell(40, 10, $row['pemasukan'], 1, 0, 'C');
            $pdf->Cell(35, 10, $row['total_pemasukan'], 1, 0, 'C');  // Lebar dikurangi
            $pdf->Cell(40, 10, $row['pengeluaran'], 1, 0, 'C');
            $pdf->Cell(35, 10, $row['total_pengeluaran'], 1, 0, 'C');  // Lebar dikurangi
            $pdf->Cell(30, 10, $row['date'], 1, 1, 'C');

            // Hitung total pemasukan dan pengeluaran
            $totalPemasukan += $row['total_pemasukan'];
            $totalPengeluaran += $row['total_pengeluaran'];
        }
    } else {
        $pdf->Cell(0, 10, 'Tidak ada data', 1, 1, 'C');
    }

    // Menampilkan Total Pemasukan dan Pengeluaran
    $pdf->Cell(50, 10, 'TOTAL:', 1, 0, 'C');
    $pdf->Cell(35, 10, number_format($totalPemasukan, 0, ',', '.'), 1, 0, 'C');  // Lebar dikurangi
    $pdf->Cell(40, 10, '', 1, 0, 'C');
    $pdf->Cell(35, 10, number_format($totalPengeluaran, 0, ',', '.'), 1, 0, 'C');  // Lebar dikurangi
    $pdf->Cell(30, 10, '', 1, 0, 'C');

    // Output PDF
    $pdf->Output();
    $conn->close();
}

// Menjalankan fungsi untuk menghasilkan PDF
generatePDF();
?>
