<?php
require('../fpdf.php');  // Mengimpor library FPDF

// Cek apakah pengguna sudah login
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

session_regenerate_id(true);

function generatePDF() {
    // // Koneksi ke database
    // $servername = "localhost";
    // $username = "root";
    // $password = "";
    // $dbname = "capstone_tpa";
    // $conn = new mysqli($servername, $username, $password, $dbname);

    // // Cek koneksi
    // if ($conn->connect_error) {
    //     die("Koneksi gagal: " . $conn->connect_error);
    // }
    include '../includes/koneksi.php';

    // Filter data berdasarkan bulan
    $sql = "SELECT * FROM pemasukan_pengeluaran";
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
    $pdf->SetFont('Arial', 'B', 14);

    // Judul
    $pdf->Cell(0, 10, 'Laporan Pemasukan dan Pengeluaran', 0, 1, 'C');
    $pdf->Ln(5);

    // Tabel Header dengan background warna
    $pdf->SetFillColor(200, 220, 255);  // Warna biru muda untuk header
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(10, 10, 'No', 1, 0, 'C', true);
    $pdf->Cell(40, 10, 'Jenis Transaksi', 1, 0, 'C', true);
    $pdf->Cell(35, 10, 'Deskripsi', 1, 0, 'C', true);  // Lebar dikurangi
    $pdf->Cell(40, 10, 'Jumlah', 1, 0, 'C', true);
    $pdf->Cell(30, 10, 'Tanggal', 1, 1, 'C', true);  // Tanggal

    // Data Tabel
    $no = 1;
    $pdf->SetFont('Arial', '', 11);
    $pdf->SetFillColor(255, 255, 255);  // Warna latar belakang baris data (putih)

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $pdf->Cell(10, 10, $no++, 1, 0, 'C', true);
            $pdf->Cell(40, 10, $row['jenis'], 1, 0, 'C', true);
            $pdf->Cell(35, 10, $row['deskripsi'], 1, 0, 'C', true);  // Lebar dikurangi
            $pdf->Cell(40, 10, number_format($row['jumlah'], 0, ',', '.'), 1, 0, 'C', true);
            $pdf->Cell(30, 10, date("d-m-Y", strtotime($row['tanggal'])), 1, 1, 'C', true);

            // Hitung total pemasukan dan pengeluaran
            if ($row['jenis'] == "pemasukan") {
                $totalPemasukan += $row['jumlah'];
            } else {
                $totalPengeluaran += $row['jumlah'];
            }
        }
    } else {
        $pdf->Cell(0, 10, 'Tidak ada data', 1, 1, 'C');
    }

    // Menampilkan Total Pemasukan dan Pengeluaran
    $pdf->Ln(5);  // Jarak lebih antara data dan total
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(80, 10, 'TOTAL PEMASUKAN:', 1, 0, 'C');
    $pdf->Cell(55, 10, number_format($totalPemasukan, 0, ',', '.'), 1, 0, 'C');

    $pdf->Ln();  // Membuat baris baru

    $pdf->Cell(80, 10, 'TOTAL PENGELUARAN:', 1, 0, 'C');
    $pdf->Cell(55, 10, number_format($totalPengeluaran, 0, ',', '.'), 1, 0, 'C');

    // Output PDF
    $pdf->Output();
    $conn->close();
}

// Menjalankan fungsi untuk menghasilkan PDF
generatePDF();
?>
