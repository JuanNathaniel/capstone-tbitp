<?php
header("Content-Type: application/json");

include '../includes/koneksi.php';

// Cek metode HTTP
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        if (isset($_GET['id'])) {
            // Ambil data aturan penjemputan berdasarkan ID
            $id = $conn->real_escape_string($_GET['id']);
            $sql = "SELECT * FROM aturan_penjemputan WHERE id = '$id'";
        } else {
            // Ambil semua data aturan penjemputan
            $sql = "SELECT * FROM aturan_penjemputan";
        }

        $result = $conn->query($sql);
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        echo json_encode(["status" => "success", "data" => $data]);
        break;

    case 'POST':
        $input = json_decode(file_get_contents('php://input'), true);
        if (!isset($input['waktu_keterlambatan_penjemputan']) || !isset($input['charge'])) {
            echo json_encode(["status" => "error", "message" => "Missing parameters"]);
            http_response_code(400);
            exit;
        }

        $waktu = $conn->real_escape_string($input['waktu_keterlambatan_penjemputan']);
        $charge = $conn->real_escape_string($input['charge']);
        $sql = "INSERT INTO aturan_penjemputan (waktu_keterlambatan_penjemputan, charge) VALUES ('$waktu', '$charge')";

        if ($conn->query($sql) === TRUE) {
            echo json_encode(["status" => "success", "message" => "Data inserted successfully"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Error: " . $conn->error]);
            http_response_code(500);
        }
        break;

    case 'PUT':
        $input = json_decode(file_get_contents('php://input'), true);
        if (!isset($input['id']) || !isset($input['waktu_keterlambatan_penjemputan']) || !isset($input['charge'])) {
            echo json_encode(["status" => "error", "message" => "Missing parameters"]);
            http_response_code(400);
            exit;
        }

        $id = $conn->real_escape_string($input['id']);
        $waktu = $conn->real_escape_string($input['waktu_keterlambatan_penjemputan']);
        $charge = $conn->real_escape_string($input['charge']);
        $sql = "UPDATE aturan_penjemputan SET waktu_keterlambatan_penjemputan = '$waktu', charge = '$charge' WHERE id = '$id'";

        if ($conn->query($sql) === TRUE) {
            echo json_encode(["status" => "success", "message" => "Data updated successfully"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Error: " . $conn->error]);
            http_response_code(500);
        }
        break;

    case 'DELETE':
        $input = json_decode(file_get_contents('php://input'), true);
        if (!isset($input['id'])) {
            echo json_encode(["status" => "error", "message" => "Missing ID"]);
            http_response_code(400);
            exit;
        }

        $id = $conn->real_escape_string($input['id']);
        $sql = "DELETE FROM aturan_penjemputan WHERE id = '$id'";

        if ($conn->query($sql) === TRUE) {
            echo json_encode(["status" => "success", "message" => "Data deleted successfully"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Error: " . $conn->error]);
            http_response_code(500);
        }
        break;

    default:
        echo json_encode(["status" => "error", "message" => "Invalid request method"]);
        http_response_code(405);
        break;
}

$conn->close();
?>
