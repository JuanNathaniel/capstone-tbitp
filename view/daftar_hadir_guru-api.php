<?php
header("Content-Type: application/json");
include '../includes/koneksi.php';

// Response array
$response = ["success" => false, "message" => "", "data" => []];

try {
    // Handle POST request (Insert data)
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Ambil data dari request body
        $input = json_decode(file_get_contents("php://input"), true);

        $id_guru = $input['id_guru'] ?? '';
        $jam_datang = $input['jam_datang'] ?? '';
        $jam_pulang = $input['jam_pulang'] ?? '';
        $keterangan = $input['keterangan'] ?? '';
        $tanda_tangan = isset($input['tanda_tangan']) ? (int)$input['tanda_tangan'] : 0;
        $tanggal = $input['tanggal'] ?? '';

        // Validasi input
        if (empty($id_guru) || empty($jam_datang) || empty($jam_pulang) || empty($keterangan) || empty($tanggal)) {
            $response['message'] = "Semua field harus diisi.";
            echo json_encode($response);
            exit;
        }

        // Query untuk menyimpan data
        $stmt = $conn->prepare("INSERT INTO daftar_hadir_guru (id_guru, jam_datang, jam_pulang, keterangan, tanda_tangan1, date) 
                                 VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $id_guru, $jam_datang, $jam_pulang, $keterangan, $tanda_tangan, $tanggal);

        if ($stmt->execute()) {
            $response['success'] = true;
            $response['message'] = "Data berhasil disimpan!";
        } else {
            $response['message'] = "Gagal menyimpan data: " . $conn->error;
        }

        $stmt->close();

    // Handle GET request (Fetch data)
    } elseif ($_SERVER["REQUEST_METHOD"] == "GET") {
        $selectedDate = $_GET['tanggal'] ?? '';

        $sql = "SELECT guru.id_guru, guru.nama, daftar_hadir_guru.id_daftarhadirguru, daftar_hadir_guru.jam_datang, 
                       daftar_hadir_guru.jam_pulang, daftar_hadir_guru.tanda_tangan1, daftar_hadir_guru.date, daftar_hadir_guru.keterangan
                FROM daftar_hadir_guru
                JOIN guru ON guru.id_guru = daftar_hadir_guru.id_guru";

        if (!empty($selectedDate)) {
            $sql .= " WHERE daftar_hadir_guru.date = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $selectedDate);
        } else {
            $stmt = $conn->prepare($sql);
        }

        if ($stmt->execute()) {
            $result = $stmt->get_result();
            $data = $result->fetch_all(MYSQLI_ASSOC);

            $response['success'] = true;
            $response['data'] = $data;
        } else {
            $response['message'] = "Gagal mengambil data: " . $conn->error;
        }

        $stmt->close();

    } else {
        $response['message'] = "Metode HTTP tidak didukung.";
    }
} catch (Exception $e) {
    $response['message'] = "Terjadi kesalahan: " . $e->getMessage();
}

// Close database connection
$conn->close();

// Return JSON response
echo json_encode($response);
