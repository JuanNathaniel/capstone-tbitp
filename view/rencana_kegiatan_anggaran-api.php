<?php
// File: api_rencana_kegiatan_anggaran.php

header("Content-Type: application/json");
include '../includes/koneksi.php';

$method = $_SERVER['REQUEST_METHOD'];
$response = [];

switch ($method) {
    case 'GET':
        // Retrieve all records or a specific record by ID
        if (isset($_GET['id'])) {
            $id = $_GET['id'];
            $sql = "SELECT * FROM rencana_kegiatan_anggaran WHERE id = '$id'";
        } else {
            $sql = "SELECT * FROM rencana_kegiatan_anggaran";
        }

        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $data = [];
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
            $response = ["status" => "success", "data" => $data];
        } else {
            $response = ["status" => "error", "message" => "No data found."];
        }
        break;

    case 'POST':
        // Create a new record
        $nama_dokumen = $_POST['nama_dokumen'];
        $tahun_anggaran = $_POST['tahun_anggaran'];
        $keterangan = $_POST['keterangan'];

        if (isset($_FILES['file_upload'])) {
            $fileName = $_FILES['file_upload']['name'];
            $fileTmpName = $_FILES['file_upload']['tmp_name'];
            $fileExt = pathinfo($fileName, PATHINFO_EXTENSION);
            $newFileName = uniqid('', true) . '.' . $fileExt;
            $uploadDir = '../uploads/rencana_kegiatan_anggaran/';

            if (move_uploaded_file($fileTmpName, $uploadDir . $newFileName)) {
                $sql = "INSERT INTO rencana_kegiatan_anggaran (nama_dokumen, tahun_anggaran, pengumpulan_dokumen, keterangan) 
                        VALUES ('$nama_dokumen', '$tahun_anggaran', '$newFileName', '$keterangan')";

                if ($conn->query($sql) === TRUE) {
                    $response = ["status" => "success", "message" => "Data added successfully."];
                } else {
                    $response = ["status" => "error", "message" => "Database error: " . $conn->error];
                }
            } else {
                $response = ["status" => "error", "message" => "File upload failed."];
            }
        } else {
            $response = ["status" => "error", "message" => "No file uploaded."];
        }
        break;

    case 'PUT':
        // Update an existing record
        parse_str(file_get_contents("php://input"), $putData);

        $id = $putData['id'];
        $nama_dokumen = $putData['nama_dokumen'];
        $tahun_anggaran = $putData['tahun_anggaran'];
        $keterangan = $putData['keterangan'];

        $sql = "UPDATE rencana_kegiatan_anggaran 
                SET nama_dokumen = '$nama_dokumen', tahun_anggaran = '$tahun_anggaran', keterangan = '$keterangan' 
                WHERE id = '$id'";

        if ($conn->query($sql) === TRUE) {
            $response = ["status" => "success", "message" => "Data updated successfully."];
        } else {
            $response = ["status" => "error", "message" => "Database error: " . $conn->error];
        }
        break;

    case 'DELETE':
        // Delete a record
        parse_str(file_get_contents("php://input"), $deleteData);
        $id = $deleteData['id'];

        // Retrieve the file name to delete
        $sql = "SELECT pengumpulan_dokumen FROM rencana_kegiatan_anggaran WHERE id = '$id'";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $filePath = '../uploads/rencana_kegiatan_anggaran/' . $row['pengumpulan_dokumen'];

            if (file_exists($filePath)) {
                unlink($filePath);
            }

            $sql = "DELETE FROM rencana_kegiatan_anggaran WHERE id = '$id'";
            if ($conn->query($sql) === TRUE) {
                $response = ["status" => "success", "message" => "Data deleted successfully."];
            } else {
                $response = ["status" => "error", "message" => "Database error: " . $conn->error];
            }
        } else {
            $response = ["status" => "error", "message" => "No data found with the given ID."];
        }
        break;

    default:
        $response = ["status" => "error", "message" => "Invalid request method."];
}

// Output response in JSON format
echo json_encode($response);
?>
