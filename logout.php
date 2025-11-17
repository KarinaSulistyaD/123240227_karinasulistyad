<?php
session_start();

// Hapus semua variabel session
$_SESSION = array();

// Hancurkan session
session_destroy();

// Set pesan sukses sebelum redirect
// Digunakan karena display_message ada di file lain
$_SESSION['message'] = "Anda berhasil logout.";
$_SESSION['message_type'] = "success";

// Redirect ke halaman login (index.php)
header("Location: index.php");
exit();
?>