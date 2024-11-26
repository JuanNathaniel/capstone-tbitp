<?php
session_start();

// // Koneksi ke database
// $host = 'localhost';
// $dbname = 'capstone_tpa';
// $username = 'root';
// $password = '';

// Sertakan file koneksi
include '../includes/koneksi.php';

// Inisialisasi variabel $error
$error = null;

// Jika form login disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Ambil data admin berdasarkan email
    // $query = $conn->prepare("SELECT * FROM admin_tpa WHERE email = :email");
    // $query->bindParam(':email', $email);
    // $query->execute();
    // $admin = $query->fetch(PDO::FETCH_ASSOC);
    $query = $conn->prepare("SELECT * FROM admin_tpa WHERE email = ?");
    $query->bind_param("s", $email); // "s" berarti string
    $query->execute();
    $result = $query->get_result();
    $admin = $result->fetch_assoc();


    // Jika email ditemukan dan password cocok
    if ($admin && password_verify($password, $admin['password'])) {
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['nama'] = $admin['nama'];
        header("Location: home.php"); // arahkan ke halaman home
        exit();
    } else {
        $error = "Email atau password salah!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login TPA Firdaus</title>
    <link href="../scss/custom.scss" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .gradient-custom {
            background: #6a11cb;
            background: linear-gradient(to right, rgba(106, 17, 203, 1), rgba(37, 117, 252, 1));
        }
        .form-label {
            text-align: left;
            display: block;
        }
    </style>
</head>
<body>
    <section class="vh-100 gradient-custom">
        <div class="container py-5 h-100">
            <div class="row d-flex justify-content-center align-items-center h-100">
                <div class="col-12 col-md-8 col-lg-6 col-xl-5">
                    <div class="card bg-dark text-white" style="border-radius: 1rem;">
                        <div class="card-body p-5 text-center">
                            <div class="mb-md-5 mt-md-4 pb-2">
                                <h2 class="fw-bold mb-2 text-uppercase">Login TPA Firdaus</h2>
                                <p class="text-white-50 mb-5">Please enter your email and password!</p>

                                <!-- Tampilkan pesan error jika login gagal -->
                                <?php if ($error): ?>
                                    <div class="alert alert-danger"><?php echo $error; ?></div>
                                <?php endif; ?>

                                <form action="login.php" method="POST">
                                    <div class="form-outline form-white mb-4">
                                        <label class="form-label" for="typeEmailX">Email</label>
                                        <input type="email" name="email" id="typeEmailX" class="form-control form-control-lg" placeholder="user123@gmail.com" required/>
                                    </div>

                                    <div class="form-outline form-white mb-4">
                                        <label class="form-label" for="typePasswordX">Password</label>
                                        <input type="password" name="password" id="typePasswordX" class="form-control form-control-lg" required/>
                                    </div>

                                    <button class="btn btn-outline-light btn-lg px-5" type="submit">Login</button>
                                </form>

                                <p class="small mb-5 pb-lg-2"><a class="text-white-50" href="#!">Forgot password?</a></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.5.0/font/bootstrap-icons.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
