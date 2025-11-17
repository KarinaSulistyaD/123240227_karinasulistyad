// File: test_hash.php
<?php
// Ini adalah password yang harus Anda gunakan untuk login: admin123
$raw_password = 'admin123'; 
$new_hash = password_hash($raw_password, PASSWORD_BCRYPT);

echo "HASH BARU yang harus disalin: <br>";
echo "<b>" . $new_hash . "</b>";
?>