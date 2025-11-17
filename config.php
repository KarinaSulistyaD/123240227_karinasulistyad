<?php
// Pastikan session dimulai di awal setiap file yang memerlukan session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Kredensial Database (Sesuaikan dengan setting Anda)
$host = 'localhost';
$user = 'root';
$pass = '';
$db = 'airline_db'; 

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// Fungsi untuk membuat Flash Message
function set_message($message, $type = 'success') {
    $_SESSION['message'] = $message;
    $_SESSION['message_type'] = $type;
}

// Fungsi untuk menampilkan Flash Message
function display_message() {
    if (isset($_SESSION['message'])) {
        echo '<div class="alert alert-' . $_SESSION['message_type'] . ' alert-dismissible fade show" role="alert">';
        echo $_SESSION['message'];
        echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
        unset($_SESSION['message']);
        unset($_SESSION['message_type']);
    }
}

// Fungsi untuk format tanggal dan waktu
function format_datetime($dt) {
    // Format yang lebih mudah dibaca, misal: 22/11/2025 14:15
    return date('d/m/Y H:i', strtotime($dt));
}

// Fungsi untuk format harga
function format_price($price) {
    return 'Rp ' . number_format($price, 0, ',', '.');
}

