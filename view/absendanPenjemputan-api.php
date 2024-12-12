<?php
header("Content-Type: application/json");
include '../includes/koneksi.php';

// Mengambil data absensi
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Inisialisasi filter
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

    // Tambahkan kondisi filter tanggal
    if ($filterDate) {
        $sql .= " AND DATE(absen.date) = :filterDate";
    }

    // Tambahkan kondisi filter bulan
    if ($filterMonth) {
        $currentYear = date('Y'); // Sesuaikan dengan tahun sekarang
        $sql .= " AND MONTH(absen.date) = :filterMonth AND YEAR(absen.date) = :filterYear";
    }

    $stmt = $pdo->prepare($sql);

    // Bind parameter untuk filter tanggal
    if ($filterDate) {
        $stmt->bindParam(':filterDate', $filterDate);
    }

    // Bind parameter untuk filter bulan
    if ($filterMonth) {
        $currentYear = date('Y'); // Tahun sekarang
        $stmt->bindParam(':filterMonth', $filterMonth, PDO::PARAM_INT);
        $stmt->bindParam(':filterYear', $currentYear, PDO::PARAM_INT);
    }

    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Mengembalikan data dalam format JSON
    echo json_encode([
        'status' => 'success',
        'data' => $results
    ]);
}

// Menghapus data absensi
elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    // Ambil ID dari query parameter
    parse_str(file_get_contents("php://input"), $_DELETE);
    $deleteId = isset($_DELETE['id']) ? intval($_DELETE['id']) : 0;

    if ($deleteId > 0) {
        $deleteStmt = $pdo->prepare("DELETE FROM absensi_dan_jemput WHERE id = :id");
        $deleteStmt->bindParam(':id', $deleteId, PDO::PARAM_INT);

        if ($deleteStmt->execute()) {
            echo json_encode([
                'status' => 'success',
                'message' => 'Data berhasil dihapus'
            ]);
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'Penghapusan data gagal'
            ]);
        }
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'ID tidak valid'
        ]);
    }
}

else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Method not allowed'
    ]);
}
