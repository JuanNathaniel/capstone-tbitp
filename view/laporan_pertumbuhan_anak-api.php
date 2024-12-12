<?php
include '../includes/koneksi.php';
header('Content-Type: application/json');

// Menangani operasi CRUD laporan pertumbuhan anak
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Menambah laporan pertumbuhan anak
    if (isset($_POST['add_laporan'])) {
        $id_anak = $_POST['id_anak'];
        $date = $_POST['date'];
        $nilai_moral_dan_agama = $_POST['nilai_moral_dan_agama'];
        $fisik_motorik_kasar = $_POST['fisik_motorik_kasar'];
        $kognitif = $_POST['kognitif'];
        $bahasa = $_POST['bahasa'];
        $sosial_emosional = $_POST['sosial_emosional'];

        $sql = "INSERT INTO laporan_pertumbuhan_anak_didik (id_anak, date, nilai_moral_dan_agama, fisik_motorik_kasar, kognitif, bahasa, sosial_emosional)
                VALUES ('$id_anak', '$date', '$nilai_moral_dan_agama', '$fisik_motorik_kasar', '$kognitif', '$bahasa', '$sosial_emosional')";

        if ($conn->query($sql) === TRUE) {
            echo json_encode(["message" => "Laporan Pertumbuhan berhasil ditambahkan!"]);
        } else {
            echo json_encode(["error" => "Error: " . $conn->error]);
        }
    }

    // Mengupdate laporan pertumbuhan anak
    if (isset($_POST['update_laporan'])) {
        $id = $_POST['id'];
        $id_anak = $_POST['id_anak'];
        $date = $_POST['date'];
        $nilai_moral_dan_agama = $_POST['nilai_moral_dan_agama'];
        $fisik_motorik_kasar = $_POST['fisik_motorik_kasar'];
        $kognitif = $_POST['kognitif'];
        $bahasa = $_POST['bahasa'];
        $sosial_emosional = $_POST['sosial_emosional'];

        $sql = "UPDATE laporan_pertumbuhan_anak_didik SET 
                    id_anak='$id_anak', date='$date', 
                    nilai_moral_dan_agama='$nilai_moral_dan_agama', 
                    fisik_motorik_kasar='$fisik_motorik_kasar', kognitif='$kognitif', 
                    bahasa='$bahasa', sosial_emosional='$sosial_emosional' 
                WHERE id='$id'";

        if ($conn->query($sql) === TRUE) {
            echo json_encode(["message" => "Laporan Pertumbuhan berhasil diupdate!"]);
        } else {
            echo json_encode(["error" => "Error: " . $conn->error]);
        }
    }

    // Menghapus laporan pertumbuhan anak
    if (isset($_POST['delete_laporan'])) {
        $id = $_POST['id'];

        $sql = "DELETE FROM laporan_pertumbuhan_anak_didik WHERE id='$id'";
        if ($conn->query($sql) === TRUE) {
            echo json_encode(["message" => "Laporan Pertumbuhan berhasil dihapus!"]);
        } else {
            echo json_encode(["error" => "Error: " . $conn->error]);
        }
    }
}

// Pencarian data anak berdasarkan nama
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    // Mencari anak berdasarkan nama
    if (isset($_GET['search'])) {
        $searchKeyword = $_GET['search'];
        $queryAnak = "SELECT * FROM anak WHERE nama LIKE '%$searchKeyword%'";
        $result = $conn->query($queryAnak);

        $anakData = [];
        while ($row = $result->fetch_assoc()) {
            $anakData[] = $row;
        }

        echo json_encode($anakData);
    }
}
?>
