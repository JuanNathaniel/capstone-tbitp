<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Home</title>
    <link href="../scss/custom.scss" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Styling Sidebar */
        .sidebar {
            background-color: #333;
            padding-top: 20px;
            height: 100vh;
        }

        .btn-outline-primary {
            color: white;
            border-color: white;
        }

        .btn-outline-primary:hover {
            color: grey;
            border-color: grey;
        }

        .dropdown-toggle {
            color: white;
            cursor: pointer;
        }

        /* Styling dropdown agar tidak hilang */
        .dropdown-menu {
            padding: 0;
            background-color: #333;
            font-size: 14px;
        }

        .dropdown-item {
            color: white;
            padding: 8px 16px;
            
        }

        /* Warna berubah saat dropdown item ditekan */
        .dropdown-item:active, .dropdown-item:focus {
            background-color: #133E87; /* Sesuaikan warna yang diinginkan */
            color: #fff; /* Sesuaikan warna teks jika perlu */
            outline: none; /* Menghilangkan border fokus default */
        }

        .dropdown-item:hover {
            background-color: #133E87;
        }

        /* Logout button styling */
        .logout {
            color: white;
            margin-top: 10px;
        }

        .logout:hover {
            color: white;
        }

        .list-unstyled {
            font-size: 13px;
        }
    </style>
</head>

<body>
    <div class="container-fluid">
        <div class="row">
            <?php include 'sidebar.php'; ?>

            <div class="hover-trigger"></div>

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
</html>