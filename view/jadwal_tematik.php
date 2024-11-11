<?php
session_start();

// Cek apakah pengguna sudah login
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

// Regenerasi ID sesi untuk keamanan ekstra
session_regenerate_id(true);
?>

<?php
$conn = new mysqli("localhost", "root", "", "capstone_tpa");
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// AJAX Request Handlers
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['create_tema'])) {
        $tema = $_POST['tema'];
        $id_admin = 1;
        $date = date('Y-m-d');
        $sql = "INSERT INTO jadwal_tematik (id_admin, tema, date) VALUES ('$id_admin', '$tema', '$date')";
        echo ($conn->query($sql) === TRUE) ? "Tema berhasil ditambahkan" : "Error: " . $conn->error;
        exit;
    } elseif (isset($_POST['add_kegiatan'])) {
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
    } elseif (isset($_POST['update_kegiatan'])) {
        $id = $_POST['kegiatan_id'];
        $kd = $_POST['kd'];
        $sub_tema = $_POST['sub_tema'];
        $jumlah_minggu = $_POST['jumlah_minggu'];
        $date = $_POST['date'];
        $kegiatan_bersama = $_POST['kegiatan_bersama'];
        $sql = "UPDATE jadwal_kegiatan SET kd='$kd', sub_tema='$sub_tema', jumlah_minggu='$jumlah_minggu', date='$date', kegiatan_bersama='$kegiatan_bersama' WHERE id='$id'";
        echo ($conn->query($sql) === TRUE) ? "Kegiatan berhasil diperbarui" : "Error: " . $conn->error;
        exit;
    } elseif (isset($_POST['update_tema'])) {
        $tema_id = $_POST['tema_id'];
        $tema_baru = $_POST['tema_baru'];
        $sql = "UPDATE jadwal_tematik SET tema='$tema_baru' WHERE id='$tema_id'";
        echo ($conn->query($sql) === TRUE) ? "Tema berhasil diperbarui" : "Error: " . $conn->error;
        exit;
    }elseif (isset($_POST['delete_kegiatan'])) {
        $id = $_POST['kegiatan_id'];
        $sql = "DELETE FROM jadwal_kegiatan WHERE id='$id'";
        echo ($conn->query($sql) === TRUE) ? "Kegiatan berhasil dihapus" : "Error: " . $conn->error;
        exit;
    }  } elseif (isset($_POST['delete_tema']) && isset($_POST['tema_id'])) {
    // Ambil tema_id dari POST
    $tema_id = intval($_POST['tema_id']); // Pastikan ID adalah integer
    
    // Debugging: Cek ID yang diterima
    echo "Tema ID yang diterima: " . $tema_id;
    
    // Lakukan pengecekan apakah ID tersebut ada dalam database
    $checkSql = "SELECT id FROM jadwal_tematik WHERE id = ?";
    $stmt = $conn->prepare($checkSql);
    $stmt->bind_param('i', $tema_id); // Binding parameter untuk menghindari SQL Injection
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        // Jika ID ada, lakukan penghapusan
        $deleteSql = "DELETE FROM jadwal_tematik WHERE id = ?";
        $deleteStmt = $conn->prepare($deleteSql);
        $deleteStmt->bind_param('i', $tema_id);
        $deleteStmt->execute();

        // Cek apakah penghapusan berhasil
        if ($deleteStmt->affected_rows > 0) {
            echo "Tema berhasil dihapus.";
        } else {
            echo "Gagal menghapus tema.";
        }
    } else {
        echo "Tema dengan ID tersebut tidak ditemukan.";
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jadwal Tematik</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>

<div class="container-fluid">
        <div class="row">
            <?php include 'sidebar.php'; ?> <!-- Include file sidebar -->

            <!-- Konten Utama -->
            <main class="col-md-9 col-lg-10 ms-auto" style="margin-left: auto;">
                <h2 class="bg-info rounded p-4 text-white transition-bg">Jadwal Tematik</h2>


    <!-- Button untuk Create, Update, dan Delete Tema -->
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createTemaModal">Create Tema</button>
    <button class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#updateTemaModal">Update Tema</button>
    <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteTemaModal">Delete Tema</button>

    <hr>

    <!-- Tabel Tema dan Kegiatan -->
    <?php
    $queryTema = "SELECT * FROM jadwal_tematik";
    $resultTema = $conn->query($queryTema);

    while ($tema = $resultTema->fetch_assoc()):
    ?>
        <h3 class="fw-bold">Tema: <?= htmlspecialchars($tema['tema']) ?></h3>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>No</th>
                    <th>KD</th>
                    <th>Sub Tema</th>
                    <th>Jumlah Minggu</th>
                    <th>Tanggal</th>
                    <th>Kegiatan Bersama</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
$queryKegiatan = "SELECT * FROM jadwal_kegiatan WHERE id_tematik = " . $tema['id'];
$resultKegiatan = $conn->query($queryKegiatan);
$no = 1;

while ($kegiatan = $resultKegiatan->fetch_assoc()):
?>
    <tr data-id="<?= $kegiatan['id'] ?>">
        <td><?= $no++ ?></td>
        <td class="kd"><?= htmlspecialchars($kegiatan['kd']) ?></td>
        <td class="sub_tema"><?= htmlspecialchars($kegiatan['sub_tema']) ?></td>
        <td class="jumlah_minggu"><?= htmlspecialchars($kegiatan['jumlah_minggu']) ?></td>
        <td class="date"><?= htmlspecialchars($kegiatan['date']) ?></td>
        <td class="kegiatan_bersama"><?= htmlspecialchars($kegiatan['kegiatan_bersama']) ?></td>
        <td>
            <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#updateKegiatanModal"
                data-id="<?= $kegiatan['id'] ?>"
                data-kd="<?= $kegiatan['kd'] ?>"
                data-subtema="<?= $kegiatan['sub_tema'] ?>"
                data-jumlah="<?= $kegiatan['jumlah_minggu'] ?>"
                data-date="<?= $kegiatan['date'] ?>"
                data-kegiatan="<?= $kegiatan['kegiatan_bersama'] ?>">Update</button>
            <button class="btn btn-danger btn-sm delete-kegiatan-btn" data-id="<?= $kegiatan['id'] ?>">Delete</button>
        </td>
    </tr>
<?php endwhile; ?>


                <!-- Baris kosong untuk penambahan data baru -->
                <tr>
    <td><?= $no ?></td>
    <form id="addKegiatanForm_<?= $tema['id'] ?>">
        <input type="hidden" name="id_tematik" value="<?= $tema['id'] ?>">
        <td><input type="text" name="kd" class="form-control"></td>
        <td><input type="text" name="sub_tema" class="form-control"></td>
        <td><input type="number" name="jumlah_minggu" class="form-control"></td>
        <td><input type="date" name="date" class="form-control"></td>
        <td><input type="text" name="kegiatan_bersama" class="form-control"></td>
        <td><button type="submit" class="btn btn-primary btn-sm">Add</button></td>
    </form>
</tr>

            </tbody>
        </table>
    <?php endwhile; ?>
</div>

<!-- Modal Update Kegiatan -->
<div class="modal fade" id="updateKegiatanModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header">
                    <h5 class="modal-title">Update Kegiatan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="update_kegiatan" value="1">
                    <input type="hidden" id="kegiatanId" name="kegiatan_id">
                    <div class="mb-3">
                        <label>KD</label>
                        <input type="text" id="updateKd" name="kd" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Sub Tema</label>
                        <input type="text" id="updateSubTema" name="sub_tema" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Jumlah Minggu</label>
                        <input type="number" id="updateJumlah" name="jumlah_minggu" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Tanggal</label>
                        <input type="date" id="updateDate" name="date" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Kegiatan Bersama</label>
                        <input type="text" id="updateKegiatan" name="kegiatan_bersama" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Create Tema -->
<div class="modal fade" id="createTemaModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header">
                    <h5 class="modal-title">Create Tema</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="create_tema" value="1">
                    <div class="mb-3">
                        <label>Tema</label>
                        <input type="text" name="tema" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Tema</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Update Tema -->
<div class="modal fade" id="updateTemaModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header">
                    <h5 class="modal-title">Update Tema</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="update_tema" value="1">
                    <div class="mb-3">
                        <label>Pilih Tema</label>
                        <select name="tema_id" class="form-control" required>
    <?php
    $resultTema = $conn->query("SELECT * FROM jadwal_tematik");
    while ($tema = $resultTema->fetch_assoc()) {
        echo "<option value='{$tema['id']}'>" . htmlspecialchars($tema['tema']) . "</option>";
    }
    ?>
</select>

                    </div>
                    <div class="mb-3">
                        <label>Tema Baru</label>
                        <input type="text" name="tema_baru" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Delete Tema -->
<div class="modal fade" id="deleteTemaModal" tabindex="-1" aria-labelledby="deleteTemaModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form action="delete_tema.php" method="POST" id="deleteTemaForm">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteTemaModalLabel">Hapus Tema</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="delete_tema" value="1">
                    <div class="mb-3">
                        <label for="tema_id">Pilih Tema yang akan dihapus</label>
                        <select name="tema_id" id="tema_id" class="form-control" required>
                            <?php
                            // Menampilkan daftar tema
                            $resultTema = $conn->query("SELECT * FROM jadwal_tematik");
                            while ($tema = $resultTema->fetch_assoc()) {
                                echo "<option value='" . $tema['id'] . "'>" . htmlspecialchars($tema['tema']) . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-danger">Hapus</button>
                </div>
            </div>
        </form>
    </div>
</div>



<script>
$(document).ready(function() {

    $('#addKegiatanForm').on('submit', function (e) {
        e.preventDefault(); // Mencegah reload halaman

        $.ajax({
            url: '', // Biarkan kosong jika ingin dikirim ke halaman yang sama
            type: 'POST',
            data: $(this).serialize() + '&add_kegiatan=1',
            success: function (response) {
                alert(response); // Menampilkan pesan hasil
                location.reload(); // Memuat ulang halaman setelah menambah data
            },
            error: function (xhr, status, error) {
                console.error(xhr.responseText);
            }
        });
    });
});

    // Handle Create Tema AJAX
    $('#createTemaModal form').on('submit', function(event) {
        event.preventDefault();
        var tema = $('input[name="tema"]').val();

        $.ajax({
            type: 'POST',
            url: '',  // Gunakan URL yang sama
            data: { create_tema: 1, tema: tema },
            success: function(response) {
                alert(response);
                $('#createTemaModal').modal('hide');
                location.reload();  // Reload halaman setelah sukses
            }
        });
    });

   $('#updateKegiatanModal form').on('submit', function(event) {
    event.preventDefault();
    var data = $(this).serialize();
    
    $.ajax({
        type: 'POST',
        url: '',  // Form action yang sama
        data: data,
        success: function(response) {
            alert(response); // Tampilkan pesan sukses
            $('#updateKegiatanModal').modal('hide');
            
            // Ambil data baru yang telah diupdate dari form modal
            var kegiatanId = $('#kegiatanId').val();
            var kd = $('#updateKd').val();
            var subTema = $('#updateSubTema').val();
            var jumlahMinggu = $('#updateJumlah').val();
            var date = $('#updateDate').val();
            var kegiatanBersama = $('#updateKegiatan').val();

            // Temukan baris yang sesuai berdasarkan ID
            var row = $('tr[data-id="' + kegiatanId + '"]');
            
            // Update konten baris dengan data baru
            row.find('.kd').text(kd);
            row.find('.sub_tema').text(subTema);
            row.find('.jumlah_minggu').text(jumlahMinggu);
            row.find('.date').text(date);
            row.find('.kegiatan_bersama').text(kegiatanBersama);
        }
    });
});

$('#updateKegiatanModal').on('show.bs.modal', function(event) {
    var button = $(event.relatedTarget); // Tombol yang diklik
    var kegiatanId = button.data('id');
    var kd = button.data('kd');
    var subTema = button.data('subtema');
    var jumlahMinggu = button.data('jumlah');
    var date = button.data('date');
    var kegiatanBersama = button.data('kegiatan');
    
    // Set nilai ke dalam modal
    $('#kegiatanId').val(kegiatanId);
    $('#updateKd').val(kd);
    $('#updateSubTema').val(subTema);
    $('#updateJumlah').val(jumlahMinggu);
    $('#updateDate').val(date);
    $('#updateKegiatan').val(kegiatanBersama);
});


// Menangani pengiriman form per tema
    $('form[id^="addKegiatanForm_"]').on('submit', function (e) {
        e.preventDefault(); // Mencegah reload halaman

        var formId = $(this).attr('id');  // Ambil ID unik form

        $.ajax({
            url: '', // Kirim data ke halaman yang sama
            type: 'POST',
            data: $(this).serialize() + '&add_kegiatan=1', // Ambil data dari form
            success: function (response) {
                alert(response); // Menampilkan pesan hasil
                location.reload(); // Memuat ulang halaman setelah menambah data
            },
            error: function (xhr, status, error) {
                console.error(xhr.responseText);
            }
        });
    });


    // Handle Delete Kegiatan
    $('body').on('click', '.delete-kegiatan-btn', function() {
        var kegiatanId = $(this).data('id');
        if (confirm('Yakin ingin menghapus kegiatan ini?')) {
            $.ajax({
                type: 'POST',
                url: '',
                data: { delete_kegiatan: true, kegiatan_id: kegiatanId },
                success: function(response) {
                    alert(response);
                    location.reload();  // Reload halaman setelah sukses
                }
            });
        }
    });

    // Handle Update Tema
    $('#updateTemaModal form').on('submit', function(event) {
        event.preventDefault();
        var temaId = $('select[name="tema_id"]').val();
        var temaBaru = $('input[name="tema_baru"]').val();

        $.ajax({
            type: 'POST',
            url: '',  // Gunakan URL yang sama
            data: { update_tema: 1, tema_id: temaId, tema_baru: temaBaru },
            success: function(response) {
                alert(response);
                $('#updateTemaModal').modal('hide');
                location.reload();  // Reload halaman setelah sukses
            }
        });
    });

    // Handle Delete Tema
    $('#deleteTemaForm').on('submit', function(event) {
        event.preventDefault();
        
        var temaId = $('select[name="tema_id"]').val(); // Ambil ID tema yang dipilih
        console.log("ID Tema yang dipilih: ", temaId);  // Cek ID yang dipilih di konsol

        $.ajax({
            type: 'POST',
            url: '',  // Pastikan URL sesuai atau kosong jika di halaman yang sama
            data: { delete_tema: true, tema_id: temaId },
            success: function(response) {
                alert(response);  // Tampilkan respons dari server
                $('#deleteTemaModal').modal('hide');  // Tutup modal setelah berhasil
                location.reload();  // Reload halaman setelah sukses
            },
            error: function(xhr, status, error) {
                alert("Terjadi kesalahan: " + error);  // Menangani error jika ada
            }
        });
    });

</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
