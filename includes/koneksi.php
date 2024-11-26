<?php
$host = 'localhost';
$dbname = 'capstone_tpa';
$username = 'root';
$password = '';

// Koneksi dengan PDO
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Koneksi PDO gagal: " . $e->getMessage());
}

// Koneksi dengan MySQLi
$mysqli = new mysqli($host, $username, $password, $dbname);
if ($mysqli->connect_error) {
    die("Koneksi MySQLi gagal: " . $mysqli->connect_error);
}

// Default $conn menggunakan MySQLi
$conn = $mysqli;

// Jika Anda ingin $conn menggunakan PDO sebagai default, ganti baris di atas menjadi:
// $conn = $pdo;
?>
