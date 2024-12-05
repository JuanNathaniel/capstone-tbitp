<?php
require('../fpdf.php');
include '../includes/koneksi.php';

// Buat kelas PDF
class PDF extends FPDF
{
    // Header dokumen
    function Header()
    {
        $this->SetFont('Arial', 'B', 16);
        $this->Cell(0, 10, 'Rekap Daftar Hadir Guru', 0, 1, 'C');
        $this->Ln(5);
    }

    // Footer dokumen
    function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, 'Halaman ' . $this->PageNo(), 0, 0, 'C');
    }

    // Tambahkan tabel
    function AddTable($header, $data, $bulan, $tahun)
    {
        // Judul tabel per bulan
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(0, 10, 'Bulan: ' . date('F', strtotime("$tahun-$bulan-01")) . " $tahun", 0, 1, 'L');
        $this->Ln(2);

        // Header tabel dengan warna
        $this->SetFillColor(200, 220, 255);
        $this->SetFont('Arial', 'B', 10);
        foreach ($header as $col) {
            $this->Cell($col['width'], 10, $col['label'], 1, 0, $col['align'], true);
        }
        $this->Ln();

        // Isi tabel
        $this->SetFont('Arial', '', 9);
        foreach ($data as $row) {
            foreach ($header as $col) {
                $value = $row[$col['field']];
                if ($col['field'] === 'tanda_tangan') {
                    // Gambar checkbox dengan centang atau silang
                    $x = $this->GetX();
                    $y = $this->GetY();
                    $isPresent = ($value === 'H'); // H untuk hadir
                    $this->SetFillColor($isPresent ? 144 : 255, $isPresent ? 238 : 80, $isPresent ? 144 : 80); // Hijau jika hadir, merah jika absen
                    $this->Rect($x + 3, $y + 3, 4, 4, 'F'); // Kotak checkbox
                    if ($isPresent) {
                        // Centang
                        $this->SetDrawColor(0, 128, 0);
                        $this->Line($x + 3.5, $y + 4.5, $x + 4.5, $y + 5.5); // Garis ke bawah
                        $this->Line($x + 4.5, $y + 5.5, $x + 6, $y + 3.5);   // Garis ke atas
                    } else {
                        // Silang
                        $this->SetDrawColor(255, 0, 0);
                        $this->Line($x + 3.5, $y + 3.5, $x + 6, $y + 6);     // Garis silang pertama
                        $this->Line($x + 6, $y + 3.5, $x + 3.5, $y + 6);     // Garis silang kedua
                    }
                    $this->Cell($col['width'], 8, '', 1, 0, 'C');
                } else {
                    $this->Cell($col['width'], 8, $value, 1, 0, $col['align']);
                }
            }
            $this->Ln();
        }
        $this->Ln(5); // Jarak antar tabel
    }
}

// Ambil semua data dari database, dikelompokkan per bulan dan tahun
$sql = "SELECT MONTH(daftar_hadir_guru.date) AS bulan, YEAR(daftar_hadir_guru.date) AS tahun, 
               daftar_hadir_guru.date AS tanggal, guru.nama, 
               daftar_hadir_guru.jam_datang, daftar_hadir_guru.jam_pulang, 
               daftar_hadir_guru.keterangan, daftar_hadir_guru.tanda_tangan1
        FROM daftar_hadir_guru
        JOIN guru ON guru.id_guru = daftar_hadir_guru.id_guru
        ORDER BY tahun, bulan, daftar_hadir_guru.date";

$result = $conn->query($sql);

// Siapkan data untuk PDF
$data_per_bulan = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $bulan = $row['bulan'];
        $tahun = $row['tahun'];

        if (!isset($data_per_bulan["$tahun-$bulan"])) {
            $data_per_bulan["$tahun-$bulan"] = [];
        }

        $data_per_bulan["$tahun-$bulan"][] = [
            'no' => count($data_per_bulan["$tahun-$bulan"]) + 1,
            'tanggal' => date('d-m-Y', strtotime($row['tanggal'])),
            'nama' => $row['nama'],
            'jam_datang' => $row['jam_datang'],
            'jam_pulang' => $row['jam_pulang'],
            'keterangan' => $row['keterangan'],
            'tanda_tangan' => $row['tanda_tangan1'] == '1' ? 'H' : 'A' // H untuk hadir, A untuk absen
        ];
    }
} else {
    echo "Tidak ada data.";
    exit;
}

// Header tabel
$header = [
    ['label' => 'No', 'field' => 'no', 'width' => 10, 'align' => 'C'],
    ['label' => 'Tanggal', 'field' => 'tanggal', 'width' => 30, 'align' => 'C'],
    ['label' => 'Nama', 'field' => 'nama', 'width' => 40, 'align' => 'L'],
    ['label' => 'Jam Datang', 'field' => 'jam_datang', 'width' => 35, 'align' => 'C'],
    ['label' => 'Jam Pulang', 'field' => 'jam_pulang', 'width' => 35, 'align' => 'C'],
    ['label' => 'Keterangan', 'field' => 'keterangan', 'width' => 30, 'align' => 'L'],
    ['label' => 'Status', 'field' => 'tanda_tangan', 'width' => 15, 'align' => 'C'] // Ganti label
];

// Buat file PDF
$pdf = new PDF();
$pdf->AddPage();

foreach ($data_per_bulan as $key => $data) {
    [$tahun, $bulan] = explode('-', $key);
    $pdf->AddTable($header, $data, $bulan, $tahun);
}

$pdf->Output('D', 'rekap_daftar_hadir_guru.pdf');
?>