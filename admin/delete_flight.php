<?php
session_start();
include('../config.php');

// Pengecekan Akses Admin [cite: 28, 29]
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    set_message("Akses ditolak. Anda harus login sebagai Admin.", "danger");
    header('Location: ../dashboard.php');
    exit();
}

$flight_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($flight_id > 0) {
    $flight_query = "SELECT flight_no FROM flights WHERE id = $flight_id";
    $flight_result = mysqli_query($conn, $flight_query);
    $flight = mysqli_fetch_assoc($flight_result);

    if ($flight) {
        $flight_no = $flight['flight_no'];

        mysqli_begin_transaction($conn);

        try {
            // 1. Hapus semua pemesanan terkait penerbangan ini (CASCADE DELETE)
            $delete_bookings = "DELETE FROM bookings WHERE flight_id = $flight_id";
            if (!mysqli_query($conn, $delete_bookings)) {
                throw new Exception("Gagal menghapus pemesanan terkait.");
            }

            // 2. Hapus penerbangan
            $delete_flight = "DELETE FROM flights WHERE id = $flight_id";
            if (!mysqli_query($conn, $delete_flight)) {
                throw new Exception("Gagal menghapus data penerbangan.");
            }

            mysqli_commit($conn);
            set_message("Penerbangan  " . $flight_no . "  berhasil dihapus (beserta pemesanannya).", "success");
            
        } catch (Exception $e) {
            mysqli_rollback($conn);
            set_message("Gagal menghapus penerbangan: " . $e->getMessage(), "danger");
        }
    } else {
        set_message("Penerbangan tidak ditemukan.", "danger");
    }
} else {
    set_message("ID Penerbangan tidak valid.", "danger");
}

header('Location: ../dashboard.php');
exit();
?>