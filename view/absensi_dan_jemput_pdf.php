<?php
// Include FPDF and PDF generator functions
require 'pdf_generator.php';

// Database connection details
$host = 'localhost';
$dbname = 'capstone_tpa';
$username = 'root';
$password = '';

// Connect to the database
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Set the title and header for the PDF
$title = 'Absensi Datang dan Jemput';
$header = ['No', 'Nama Siswa', 'Nama (Pengantar)', 'Jam Datang (Pengantar)', 'Paraf (Pengantar)', 'Nama (Penjemput)', 'Jam Jemput (Penjemput)', 'Paraf (Penjemput)'];

// Get the filter date from the URL parameters
$filterDate = isset($_GET['filter_date']) ? $_GET['filter_date'] : null;

// Query to retrieve attendance data with date filter if provided
$sql = "
    SELECT 
        absen.id AS id,  
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
";

// Add date filter to the query if `filter_date` is set
if ($filterDate) {
    $sql .= " WHERE DATE(absen.date) = :filterDate";
}

// Prepare and execute the query with the date filter
$stmt = $pdo->prepare($sql);
if ($filterDate) {
    $stmt->bindParam(':filterDate', $filterDate);
}
$stmt->execute();
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Prepare data for PDF generation
$data = [];
$no = 1;
foreach ($results as $row) {
    $data[] = [
        $no++,
        $row['nama_siswa'],
        $row['nama_pengantar'],
        $row['jam_datang'],
        $row['paraf_pengantar'],
        $row['nama_penjemput'],
        $row['jam_jemput'],
        $row['paraf_penjemput']
    ];
}

// Generate the PDF and prompt download, passing the date as an additional parameter
generatePDF($title, $header, $data, 'absensi_dan_jemput.pdf', $filterDate);
exit();
?>
