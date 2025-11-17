<?php
session_start();
include('config.php');

if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = mysqli_real_escape_string($conn, $_POST['full_name']);
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($password !== $confirm_password) {
        set_message("Konfirmasi Password tidak cocok.", "danger");
    } else {
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);
        $role = 'user'; // Default role saat registrasi adalah user biasa

        // Cek duplikasi username
        $check_query = "SELECT id FROM users WHERE username = '$username' OR email = '$email'";
        if (mysqli_num_rows(mysqli_query($conn, $check_query)) > 0) {
            set_message("Username atau Email sudah digunakan.", "danger");
        } else {
            $insert_query = "INSERT INTO users (full_name, username, email, password, role) 
                             VALUES ('$full_name', '$username', '$email', '$hashed_password', '$role')";
            
            if (mysqli_query($conn, $insert_query)) {
                set_message("Registrasi berhasil! Silakan login.", "success"); 
                header('Location: index.php');
                exit();
            } else {
                set_message("Registrasi gagal: " . mysqli_error($conn), "danger");
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi - Airline Booking System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #6a6ad9; display: flex; justify-content: center; align-items: center; min-height: 100vh; }
        .register-card { width: 100%; max-width: 450px; padding: 30px; background: white; border-radius: 10px; box-shadow: 0 4px 8px rgba(0,0,0,.1); }
    </style>
</head>
<body>
    <div class="register-card">
        <h3 class="text-center mb-4">REGISTER</h3>
        <?php display_message(); ?>
        <form method="POST">
            <div class="mb-3">
                <input type="text" class="form-control" id="full_name" name="full_name" placeholder="Nama Lengkap" required>
            </div>
            <div class="mb-3">
                <input type="text" class="form-control" id="username" name="username" placeholder="Username" required>
            </div>
            <div class="mb-3">
                <input type="email" class="form-control" id="email" name="email" placeholder="Email" required>
            </div>
            <div class="mb-3">
                <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
            </div>
            <div class="mb-4">
                <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Konfirmasi Password" required>
            </div>
            <button type="submit" class="btn btn-primary w-100 mb-3">Daftar</button>
        </form>
        <p class="mt-2 text-center small">Sudah punya akun? <a href="index.php">Login di sini</a></p>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>