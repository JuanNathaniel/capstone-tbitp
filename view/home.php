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
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 mt-4">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-8 rounded p-3 mx-2" style="background-color: #AB886D;">
                            <!-- Carousel Gambar -->
                            <div id="carouselExampleAutoplaying" class="carousel slide" data-bs-ride="carousel" data-bs-interval="3000">
                                <div class="carousel-inner">
                                    <div class="carousel-item active">
                                        <img src="asset/image1.jpg" style="object-fit: cover; height: 400px;" class="d-block w-100" alt="Image 1">
                                    </div>
                                    <div class="carousel-item">
                                        <img src="asset/image2.jpg" style="object-fit: cover; height: 400px;" class="d-block w-100" alt="Image 2">
                                    </div>
                                    <div class="carousel-item">
                                        <img src="asset/image3.jpg" style="object-fit: cover; height: 400px;" class="d-block w-100" alt="Image 3">
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
                        </div>

                        <div class="col-sm-3 rounded p-3 mx-4" style="background-color: #AB886D;">
                            <h2 class="text-white">About us</h2>
                            <p class="h7 text-white"> yang penuh kasih dan mendukung. Dengan pengajar yang berkompeten, kami bertujuan membentuk generasi yang cerdas, berakhlak mulia, dan taat beragama. Di TPA Firdaus, kami percaya pendidikan agama adalah fondasi penting untuk masa depan yang lebih baik.</p>
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
