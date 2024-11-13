<?php
require '../fpdf.php';  // Adjust the path to go up one level from 'view' folder to locate 'fpdf.php'

function generatePDF($title, $header, $data, $filename) {
    $pdf = new FPDF();
    $pdf->AddPage();

    // Title
    $pdf->SetFont('Arial', 'B', 16);
    $pdf->Cell(0, 10, $title, 0, 1, 'C');
    $pdf->Ln(10);

    // Header
    $pdf->SetFont('Arial', 'B', 10);
    foreach ($header as $col) {
        $pdf->Cell(24, 10, $col, 1);
    }
    $pdf->Ln();

    // Data
    $pdf->SetFont('Arial', '', 10);
    foreach ($data as $row) {
        foreach ($row as $col) {
            $pdf->Cell(24, 10, $col, 1);
        }
        $pdf->Ln();
    }

    // Output PDF
    $pdf->Output('D', $filename);
}
