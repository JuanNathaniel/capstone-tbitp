<?php
require('../fpdf.php');  // Mengimpor library FPDF

// Cek apakah pengguna sudah login
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

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
    // Sertakan file koneksi
    include '../includes/koneksi.php';

    // Query untuk mengambil data dari tabel aturan_penjemputan
    $sql = "SELECT id, waktu_keterlambatan_penjemputan, charge FROM aturan_penjemputan";
    $result = $conn->query($sql);

    // Membuat objek FPDF
    $pdf = new FPDF();
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 12);

    // Judul
    $pdf->Cell(0, 10, 'Aturan Penjemputan', 0, 1, 'C');
    $pdf->Ln(5);

    // Tabel Header
    $pdf->Cell(20, 10, 'No', 1, 0, 'C');
    $pdf->Cell(80, 10, 'Waktu Keterlambatan Penjemputan', 1, 0, 'C');
    $pdf->Cell(40, 10, 'Charge', 1, 1, 'C');  // Tidak ada kolom Action

    // Data Tabel
    $no = 1;
    $pdf->SetFont('Arial', '', 10);
    
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $pdf->Cell(20, 10, $no++, 1, 0, 'C');
            $pdf->Cell(80, 10, $row['waktu_keterlambatan_penjemputan'], 1, 0, 'C');
            $pdf->Cell(40, 10, $row['charge'], 1, 1, 'C');
        }
    } else {
        $pdf->Cell(0, 10, 'Tidak ada data', 1, 1, 'C');
    }

    // Output PDF
    $pdf->Output();
    $conn->close();
}

// Menjalankan fungsi untuk menghasilkan PDF
generatePDF();
?>
