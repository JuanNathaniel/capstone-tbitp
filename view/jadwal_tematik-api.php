<?php

include '../includes/koneksi.php';

// Mengatur header untuk API (format JSON)
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Handling Tema Creation
    if (isset($_POST['create_tema'])) {
        $tema = $_POST['tema'];
        $id_admin = 1;
        $date = date('Y-m-d');
        $sql = "INSERT INTO jadwal_tematik (id_admin, tema, date) VALUES ('$id_admin', '$tema', '$date')";
        echo ($conn->query($sql) === TRUE) ? "Tema berhasil ditambahkan" : "Error: " . $conn->error;
        exit;
    }
    
    // Handling Kegiatan Addition
    elseif (isset($_POST['add_kegiatan'])) {
        $id_tematik = $_POST['id_tematik'];
        $kd = $_POST['kd'];
        $sub_tema = $_POST['sub_tema'];
        $jumlah_minggu = $_POST['jumlah_minggu'];
        $date = $_POST['date'];
        $kegiatan_bersama = $_POST['kegiatan_bersama'];
        $sql = "INSERT INTO jadwal_kegiatan (id_tematik, kd, sub_tema, jumlah_minggu, date, kegiatan_bersama)
                VALUES ('$id_tematik', '$kd', '$sub_tema', '$jumlah_minggu', '$date', '$kegiatan_bersama')";
        echo ($conn->query($sql) === TRUE) ? "Kegiatan berhasil ditambahkan" : "Error: " . $conn->error;
        exit;
    }

    // Handling Kegiatan Update
    elseif (isset($_POST['update_kegiatan'])) {
        $id = $_POST['kegiatan_id'];
        $kd = $_POST['kd'];
        $sub_tema = $_POST['sub_tema'];
        $jumlah_minggu = $_POST['jumlah_minggu'];
        $date = $_POST['date'];
        $kegiatan_bersama = $_POST['kegiatan_bersama'];
        $sql = "UPDATE jadwal_kegiatan SET kd='$kd', sub_tema='$sub_tema', jumlah_minggu='$jumlah_minggu', date='$date', kegiatan_bersama='$kegiatan_bersama' WHERE id='$id'";
        echo ($conn->query($sql) === TRUE) ? "Kegiatan berhasil diperbarui" : "Error: " . $conn->error;
        exit;
    }

    // Handling Tema Update
    elseif (isset($_POST['update_tema'])) {
        $tema_id = $_POST['tema_id'];
        $tema_baru = $_POST['tema_baru'];
        $sql = "UPDATE jadwal_tematik SET tema='$tema_baru' WHERE id='$tema_id'";
        echo ($conn->query($sql) === TRUE) ? "Tema berhasil diperbarui" : "Error: " . $conn->error;
        exit;
    }

    // Handling Kegiatan Deletion
    elseif (isset($_POST['delete_kegiatan'])) {
        $id = $_POST['kegiatan_id'];
        $sql = "DELETE FROM jadwal_kegiatan WHERE id='$id'";
        echo ($conn->query($sql) === TRUE) ? "Kegiatan berhasil dihapus" : "Error: " . $conn->error;
        exit;
    }

    // Handling Tema Deletion
    elseif (isset($_POST['delete_tema'])) {
        $tema_id = isset($_POST['tema_id']) ? (int)$_POST['tema_id'] : 0;
        
        // Log untuk debugging
        error_log("Attempting to delete tema with ID: " . $tema_id);
        
        if ($tema_id <= 0) {
            echo "Error: Invalid tema ID";
            exit;
        }
        
        try {
            // Start transaction
            $conn->begin_transaction();
            
            // Delete related kegiatan
            $delete_kegiatan = "DELETE FROM jadwal_kegiatan WHERE id_tematik = ?";
            $stmt_kegiatan = $conn->prepare($delete_kegiatan);
            $stmt_kegiatan->bind_param("i", $tema_id);
            $stmt_kegiatan->execute();
            
            // Delete tema
            $delete_tema = "DELETE FROM jadwal_tematik WHERE id = ?";
            $stmt_tema = $conn->prepare($delete_tema);
            $stmt_tema->bind_param("i", $tema_id);
            $stmt_tema->execute();
            
            if ($stmt_tema->affected_rows > 0) {
                $conn->commit();
                echo "Tema dan kegiatan terkait berhasil dihapus";
            } else {
                throw new Exception("Tema dengan ID " . $tema_id . " tidak ditemukan");
            }
            
        } catch (Exception $e) {
            $conn->rollback();
            echo "Error: " . $e->getMessage();
            error_log("Error deleting tema: " . $e->getMessage());
        }
        
        exit;
    }

    elseif ($_SERVER['REQUEST_METHOD'] == 'GET') {
    // Handle GET request to fetch data
    if (isset($_GET['get_tema'])) {
        // Fetch all "Tema" data
        $sql = "SELECT * FROM jadwal_tematik";
        $result = $conn->query($sql);
        
        $temas = [];
        while ($row = $result->fetch_assoc()) {
            $temas[] = $row;
        }
        
        echo json_encode($temas);  // Send data as JSON
        exit;
    }

    if (isset($_GET['get_kegiatan'])) {
        // Fetch all "Kegiatan" data based on a specific "Tema"
        $id_tematik = $_GET['id_tematik'];
        $sql = "SELECT * FROM jadwal_kegiatan WHERE id_tematik = '$id_tematik'";
        $result = $conn->query($sql);
        
        $kegiatans = [];
        while ($row = $result->fetch_assoc()) {
            $kegiatans[] = $row;
        }
        
        echo json_encode($kegiatans);  // Send data as JSON
        exit;
    }
}
}
?>
