<?php
require('../fpdf.php');
include('../includes/koneksi.php');

// Buat kelas PDF
class PDF extends FPDF
{
    // Header dokumen
    function Header()
    {
        global $bulan, $tahun;
        $this->SetFont('Arial', 'B', 16);

        if ($bulan !== '*' && $tahun !== '') {
            $this->Cell(0, 10, 'Rekap Daftar Hadir Guru - Bulan: ' . date('F', mktime(0, 0, 0, $bulan, 1)) . ' Tahun: ' . $tahun, 0, 1, 'C');
        } elseif ($bulan === '*' && $tahun !== '') {
            $this->Cell(0, 10, 'Rekap Daftar Hadir Guru - Tahun: ' . $tahun, 0, 1, 'C');
        }
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
        if ($bulan !== '*' && $tahun !== '') {
            $this->Cell(0, 10, 'Bulan: ' . date('F', strtotime("$tahun-$bulan-01")) . " $tahun", 0, 1, 'L');
        } elseif ($bulan === '*' && $tahun !== '') {
            $this->Cell(0, 10, 'Tahun: ' . $tahun, 0, 1, 'L');
        }
        $this->Ln(2);

        // Header tabel dengan warna
        $this->SetFillColor(200, 220, 255); // Warna header
        $this->SetFont('Arial', 'B', 10);
        foreach ($header as $col) {
            $this->SetDrawColor(0, 0, 0); // Warna garis hitam
            $this->Cell($col['width'], 10, $col['label'], 1, 0, $col['align'], true); // Header tetap berwarna
        }
        $this->Ln();

        // Isi tabel tanpa warna
        $this->SetFont('Arial', '', 9);
        foreach ($data as $row) {
            foreach ($header as $col) {
                $value = $row[$col['field']];
                $this->SetDrawColor(0, 0, 0); // Warna garis hitam
                if ($col['field'] === 'tanda_tangan') {
                    // Gambar checkbox dengan centang atau silang
                    $x = $this->GetX();
                    $y = $this->GetY();
                    $isPresent = ($value === 'H'); // H untuk hadir
                    $this->SetFillColor($isPresent ? 144 : 255, $isPresent ? 238 : 80, $isPresent ? 144 : 80); // Hijau jika hadir, merah jika absen
                    $this->Rect($x + 3, $y + 3, 4, 4, 'F'); // Kotak checkbox
                    if ($isPresent) {
                        // Centang
                        $this->SetDrawColor(0, 128, 0); // Warna garis centang hijau
                        $this->Line($x + 3.5, $y + 4.5, $x + 4.5, $y + 5.5); // Garis ke bawah
                        $this->Line($x + 4.5, $y + 5.5, $x + 6, $y + 3.5);   // Garis ke atas
                    } else {
                        // Silang
                        $this->SetDrawColor(255, 0, 0); // Warna garis silang merah
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

// Ambil parameter bulan dan tahun dari request
$bulan = isset($_GET['bulan']) ? $_GET['bulan'] : '*';
$tahun = isset($_GET['tahun']) ? $_GET['tahun'] : '';

// Bangun query SQL berdasarkan bulan dan tahun yang dipilih
if ($bulan === '*' && $tahun !== '') {
    $sql = "SELECT MONTH(daftar_hadir_guru.date) AS bulan, YEAR(daftar_hadir_guru.date) AS tahun, 
                   daftar_hadir_guru.date AS tanggal, guru.nama, 
                   daftar_hadir_guru.jam_datang, daftar_hadir_guru.jam_pulang, 
                   daftar_hadir_guru.keterangan, daftar_hadir_guru.tanda_tangan1
            FROM daftar_hadir_guru
            JOIN guru ON guru.id_guru = daftar_hadir_guru.id_guru
            WHERE YEAR(daftar_hadir_guru.date) = '$tahun'
            ORDER BY tahun, bulan, daftar_hadir_guru.date";
} elseif ($bulan !== '*' && $tahun !== '') {
    $sql = "SELECT MONTH(daftar_hadir_guru.date) AS bulan, YEAR(daftar_hadir_guru.date) AS tahun, 
                   daftar_hadir_guru.date AS tanggal, guru.nama, 
                   daftar_hadir_guru.jam_datang, daftar_hadir_guru.jam_pulang, 
                   daftar_hadir_guru.keterangan, daftar_hadir_guru.tanda_tangan1
            FROM daftar_hadir_guru
            JOIN guru ON guru.id_guru = daftar_hadir_guru.id_guru
            WHERE MONTH(daftar_hadir_guru.date) = '$bulan' AND YEAR(daftar_hadir_guru.date) = '$tahun'
            ORDER BY tahun, bulan, daftar_hadir_guru.date";
} else {
    echo "Bulan dan tahun harus dipilih.";
    exit;
}

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
    echo '<script>
        alert("Tidak ada data untuk bulan dan tahun yang dipilih.");
        window.location.href = "daftar_hadir_guru.php"; // Ganti dengan URL halaman sebelumnya
    </script>';
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

$filename = 'rekap_daftar_hadir_guru_bulan_' . $bulan . '_tahun_' . $tahun . '.pdf';
$pdf->Output('D', $filename);
?>
