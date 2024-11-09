<?php
// Koneksi ke database
$servername = "localhost";  // ganti dengan nama server Anda
$username = "root";         // ganti dengan username database Anda
$password = "";             // ganti dengan password database Anda
$dbname = "nama_database";  // ganti dengan nama database Anda

$conn = new mysqli($servername, $username, $password, $dbname);

// Cek koneksi
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Menangani penambahan kegiatan
if (isset($_POST['add_kegiatan'])) {
    $kd = $_POST['kd'];
    $subTema = $_POST['sub_tema'];
    $jumlah = $_POST['jumlah'];
    $date = $_POST['date'];
    $kegiatan = $_POST['kegiatan_bersama'];

    $query = "INSERT INTO kegiatan (kd, sub_tema, jumlah, date, kegiatan_bersama) 
              VALUES ('$kd', '$subTema', '$jumlah', '$date', '$kegiatan')";

    if ($conn->query($query) === TRUE) {
        echo "Kegiatan berhasil ditambahkan.";
    } else {
        echo "Error: " . $conn->error;
    }
}

// Menangani update kegiatan
if (isset($_POST['update_kegiatan'])) {
    $id = $_POST['id'];
    $kd = $_POST['kd'];
    $subTema = $_POST['sub_tema'];
    $jumlah = $_POST['jumlah'];
    $date = $_POST['date'];
    $kegiatan = $_POST['kegiatan_bersama'];

    $query = "UPDATE kegiatan SET kd='$kd', sub_tema='$subTema', jumlah='$jumlah', date='$date', kegiatan_bersama='$kegiatan' 
              WHERE id='$id'";

    if ($conn->query($query) === TRUE) {
        echo "Kegiatan berhasil diperbarui.";
    } else {
        echo "Error: " . $conn->error;
    }
}

// Menangani penghapusan kegiatan
if (isset($_POST['delete_kegiatan'])) {
    $id = $_POST['kegiatan_id'];

    $query = "DELETE FROM kegiatan WHERE id='$id'";

    if ($conn->query($query) === TRUE) {
        echo "Kegiatan berhasil dihapus.";
    } else {
        echo "Error: " . $conn->error;
    }
}

// Menangani penambahan tema
if (isset($_POST['create_tema'])) {
    $tema = $_POST['tema'];

    $query = "INSERT INTO tema (tema) VALUES ('$tema')";

    if ($conn->query($query) === TRUE) {
        echo "Tema berhasil dibuat.";
    } else {
        echo "Error: " . $conn->error;
    }
}

// Menangani penghapusan tema
if (isset($_POST['delete_tema'])) {
    $tema_id = $_POST['tema_id'];

    $query = "DELETE FROM tema WHERE id='$tema_id'";

    if ($conn->query($query) === TRUE) {
        echo "Tema berhasil dihapus.";
    } else {
        echo "Error: " . $conn->error;
    }
}

$conn->close();
?>
