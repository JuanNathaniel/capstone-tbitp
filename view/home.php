<!DOCTYPE html>
<html lang="en">
<?php
    session_start();

    // Cek apakah pengguna sudah login
    if (!isset($_SESSION['admin_id'])) {
        header("Location: login.php");
        exit();
    }

    // Regenerasi ID sesi untuk keamanan ekstra
    session_regenerate_id(true);

    // Sertakan file koneksi
    include '../includes/koneksi.php';
?>



<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Home</title>
    <link href="../scss/custom.scss" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container-fluid">
        <div class="row">
            <?php include 'sidebar.php'; ?>

            <!-- Konten Utama -->
            <main class="col-md-9 ms-sm-auto col-lg-10 mt-4">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-8 rounded p-3 mx-2" style="background-color: #AB886D;">
                            <!-- Carousel Gambar -->
                            <div id="carouselExampleAutoplaying" class="carousel slide" data-bs-ride="carousel" data-bs-interval="3000">
                                <div class="carousel-inner">
                                    <div class="carousel-item active">
                                        <img src="asset/image1.jpg?<?php echo time(); ?>" style="object-fit: cover; height: 400px;" class="d-block w-100" alt="Image 1">
                                    </div>
                                    <div class="carousel-item">
                                        <img src="asset/image2.jpg?<?php echo time(); ?>" style="object-fit: cover; height: 400px;" class="d-block w-100" alt="Image 2">
                                    </div>
                                    <div class="carousel-item">
                                        <img src="asset/image3.jpg?<?php echo time(); ?>" style="object-fit: cover; height: 400px;" class="d-block w-100" alt="Image 3">
                                    </div>
                                </div>
                                <a class="carousel-control-prev" href="#carouselExampleAutoplaying" role="button" data-bs-slide="prev">
                                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                    <span class="visually-hidden">Previous</span>
                                </a>
                                <a class="carousel-control-next" href="#carouselExampleAutoplaying" role="button" data-bs-slide="next">
                                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                    <span class="visually-hidden">Next</span>
                                </a>
                            </div>

                            <!-- Akhir Carousel Gambar -->
                            <br>
                            <p class="h7 text-white">Foto-foto yang terlihat di sini menggambarkan suasana penuh kebersamaan dan semangat di TPA Firdaus. Di setiap momen yang diabadikan, terlihat anak-anak yang antusias belajar, menggali ilmu agama dengan penuh perhatian dan rasa ingin tahu. Setiap senyum yang terlihat adalah harapan untuk masa depan yang lebih cerah, di mana anak-anak ini tidak hanya tumbuh menjadi cerdas dalam hal pengetahuan, tetapi juga bijak dalam menyikapi kehidupan.</p>
                            <p class="h7 text-white">Semoga Allah SWT senantiasa membimbing mereka di jalan yang benar dan memberikan keberkahan dalam setiap langkah mereka.</p>
                        </div>

                        <div class="col-sm-3 rounded p-3 mx-4" style="background-color: #AB886D;">
                            <h2 class="text-white">About us</h2>
                            <p class="h7 text-white">Kami adalah lembaga pendidikan yang penuh kasih, dengan pengajar yang berkompeten, bertujuan membentuk generasi muda yang cerdas, berakhlak mulia, dan taat beragama. Di TPA Firdaus, kami percaya pendidikan agama adalah fondasi penting untuk masa depan yang lebih baik. Kami tidak hanya mengajarkan pengetahuan agama, tetapi juga mengembangkan karakter dan kepribadian anak agar menjadi pribadi yang penuh empati, tanggung jawab, dan integritas. Melalui pendidikan yang baik, kami berkomitmen membekali mereka dengan nilai-nilai yang bermanfaat bagi kehidupan dunia dan akhirat.</p>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Bootstrap JavaScript dan Ikon -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.5.0/font/bootstrap-icons.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
