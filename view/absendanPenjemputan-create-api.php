<?php
header("Content-Type: application/json");
include_once __DIR__ . '/../includes/koneksi.php';

if (!isset($pdo)) {
    echo json_encode([
        "success" => false,
        "message" => "Koneksi database tidak ditemukan."
    ]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        "success" => false,
        "message" => "Hanya menerima request POST."
    ]);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);

if (
    !isset($data['id_anak']) ||
    !isset($data['tanggal']) ||
    !isset($data['nama_pengantar']) ||
    !isset($data['jam_datang']) ||
    !isset($data['nama_penjemput']) ||
    !isset($data['jam_jemput'])
) {
    echo json_encode([
        "success" => false,
        "message" => "Semua data wajib diisi."
    ]);
    exit;
}

// Validasi apakah id_anak ada di tabel 'anak'
$stmt_check_anak = $pdo->prepare("SELECT COUNT(*) FROM anak WHERE id = :id_anak");
$stmt_check_anak->execute([':id_anak' => $data['id_anak']]);
$anak_exists = $stmt_check_anak->fetchColumn();

if (!$anak_exists) {
    echo json_encode([
        "success" => false,
        "message" => "ID anak tidak ditemukan di database."
    ]);
    exit;
}

try {
    $pdo->beginTransaction();

    $stmt_pengantar = $pdo->prepare("
        INSERT INTO pengantar (nama_pengantar, jam_datang, paraf) 
        VALUES (:nama_pengantar, :jam_datang, :paraf_pengantar)
    ");
    $stmt_pengantar->execute([
        ':nama_pengantar' => $data['nama_pengantar'],
        ':jam_datang' => $data['jam_datang'],
        ':paraf_pengantar' => isset($data['paraf_pengantar']) && $data['paraf_pengantar'] ? 1 : 0
    ]);
    $id_pengantar = $pdo->lastInsertId();

    $stmt_penjemput = $pdo->prepare("
        INSERT INTO penjemput (nama_penjemput, jam_jemput, paraf) 
        VALUES (:nama_penjemput, :jam_jemput, :paraf_penjemput)
    ");
    $stmt_penjemput->execute([
        ':nama_penjemput' => $data['nama_penjemput'],
        ':jam_jemput' => $data['jam_jemput'],
        ':paraf_penjemput' => isset($data['paraf_penjemput']) && $data['paraf_penjemput'] ? 1 : 0
    ]);
    $id_penjemput = $pdo->lastInsertId();

    $stmt_absensi = $pdo->prepare("
        INSERT INTO absensi_dan_jemput (id_anak, id_pengantar, id_penjemput, date)
        VALUES (:id_anak, :id_pengantar, :id_penjemput, :tanggal)
    ");
    $stmt_absensi->execute([
        ':id_anak' => $data['id_anak'],
        ':id_pengantar' => $id_pengantar,
        ':id_penjemput' => $id_penjemput,
        ':tanggal' => $data['tanggal']
    ]);

    $pdo->commit();

    echo json_encode([
        "success" => true,
        "message" => "Data berhasil ditambahkan."
    ]);
} catch (PDOException $e) {
    $pdo->rollBack();
    echo json_encode([
        "success" => false,
        "message" => "Gagal menambahkan data.",
        "error" => $e->getMessage()
    ]);
}
?>
