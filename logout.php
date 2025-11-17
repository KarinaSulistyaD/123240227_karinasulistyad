<?php
session_start();

// Hapus semua variabel session
$_SESSION = array();


session_destroy();

$_SESSION['message'] = "Anda berhasil logout.";
$_SESSION['message_type'] = "success";

header("Location: index.php");
exit();
?>