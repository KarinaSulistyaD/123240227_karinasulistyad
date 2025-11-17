<?php
session_start();
include('config.php');

if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];

    $query = "SELECT * FROM users WHERE username = '$username'";
    $result = mysqli_query($conn, $query);

    if ($user = mysqli_fetch_assoc($result)) {
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role']; // Menentukan hak akses
            set_message("Login berhasil! Selamat datang, " . $user['username'] . ".");
            header('Location: dashboard.php');
            exit();
        } else {
            set_message("Username atau Password salah.", "danger");
        }
    } else {
        set_message("Username atau Password salah.", "danger");
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Airline Booking System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #6a6ad9; display: flex; justify-content: center; align-items: center; min-height: 100vh; }
        .login-card { width: 100%; max-width: 400px; padding: 30px; background: white; border-radius: 10px; box-shadow: 0 4px 8px rgba(0,0,0,.1); }
    </style>
</head>
<body>
    <div class="login-card">
        <h3 class="text-center mb-4">LOGIN</h3>
        <?php display_message(); ?>
        <form method="POST">
            <div class="mb-3">
                <input type="text" class="form-control" id="username" name="username" placeholder="Username" required>
            </div>
            <div class="mb-4">
                <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
            </div>
            <button type="submit" class="btn btn-primary w-100 mb-3">Login</button>
        </form>
        <p class="mt-2 text-center small">Belum punya akun? <a href="register.php">Daftar di sini</a></p>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>