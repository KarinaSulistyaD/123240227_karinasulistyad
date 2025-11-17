<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


$host = 'localhost';
$user = 'root';
$pass = '';
$db = 'airline_db'; 

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}


function set_message($message, $type = 'success') {
    $_SESSION['message'] = $message;
    $_SESSION['message_type'] = $type;
}


function display_message() {
    if (isset($_SESSION['message'])) {
        echo '<div class="alert alert-' . $_SESSION['message_type'] . ' alert-dismissible fade show" role="alert">';
        echo $_SESSION['message'];
        echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
        unset($_SESSION['message']);
        unset($_SESSION['message_type']);
    }
}


function format_datetime($dt) {

    return date('d/m/Y H:i', strtotime($dt));
}


function format_price($price) {
    return 'Rp ' . number_format($price, 0, ',', '.');
}

