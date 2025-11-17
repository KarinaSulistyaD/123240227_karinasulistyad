<?php
session_start();
include('../config.php');

// Pengecekan Akses User Biasa [cite: 25, 26]
if (!isset($_SESSION['user_id'])) {
    set_message("Anda harus login untuk mengakses halaman pemesanan.", "danger");
    header('Location: ../index.php'); // Arahkan ke halaman login
    exit();
}
if ($_SESSION['role'] !== 'user') {
    set_message("Admin tidak dapat melakukan pemesanan tiket di sini.", "danger");
    header('Location: ../dashboard.php');
    exit();
}

$flight_id = isset($_GET['flight_id']) ? intval($_GET['flight_id']) : 0;

// Ambil data penerbangan
$flight_query = "SELECT * FROM flights WHERE id = $flight_id";
$flight_result = mysqli_query($conn, $flight_query);
$flight = mysqli_fetch_assoc($flight_result);

if (!$flight) {
    set_message("Penerbangan tidak ditemukan.", "danger");
    header('Location: ../dashboard.php');
    exit();
}

$seats_available = $flight['total_seats'] - $flight['seats_booked'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $passenger_name = mysqli_real_escape_string($conn, $_POST['passenger_name']);
    $identity_number = mysqli_real_escape_string($conn, $_POST['identity_number']);

    // Cek ketersediaan kursi saat POST
    if ($seats_available <= 0) {
        set_message("Pemesanan gagal. Kursi penerbangan sudah penuh.", "danger");
        header("Location: booking.php?flight_id=$flight_id");
        exit();
    }

    mysqli_begin_transaction($conn);

    try {
        // 1. Insert data pemesanan
        $insert_booking = "INSERT INTO bookings (user_id, flight_id, passenger_name, identity_number) 
                           VALUES ($user_id, $flight_id, '$passenger_name', '$identity_number')";
        if (!mysqli_query($conn, $insert_booking)) {
            throw new Exception("Gagal menyimpan pemesanan.");
        }

        // 2. Update jumlah kursi terisi
        $update_seats = "UPDATE flights SET seats_booked = seats_booked + 1 WHERE id = $flight_id AND seats_booked < total_seats";
        if (!mysqli_query($conn, $update_seats) || mysqli_affected_rows($conn) == 0) {
            throw new Exception("Gagal update kursi atau kursi sudah penuh (Terjadi konflik).");
        }

        mysqli_commit($conn);
        set_message("Pemesanan tiket  " . $flight['flight_no'] . "  berhasil! Lihat di bagian 'Pemesanan Saya'.", "success");
        header('Location: ../dashboard.php');
        exit();

    } catch (Exception $e) {
        mysqli_rollback($conn);
        set_message("Pemesanan gagal: " . $e->getMessage(), "danger");
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesan Tiket - <?php echo $flight['flight_no']; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container-fluid container">
            <a class="navbar-brand" href="../dashboard.php">Airline Booking System</a>
            <a href="../logout.php" class="btn btn-light btn-sm">Logout</a>
        </div>
    </nav>
    <div class="container my-5">
        <div class="card p-4 mx-auto" style="max-width: 500px;">
            <h4 class="card-title text-center mb-4 border-bottom pb-2">Pesan Tiket Pesawat</h4>
            <?php display_message(); ?>
            
            <div class="p-3 mb-3" style="border: 1px solid #ccc; border-radius: 5px;">
                <h6 class="mb-1 text-primary"><?php echo $flight['flight_no'] . ' - ' . $flight['airline_name']; ?></h6>
                <p class="mb-1 small">Rute:  <?php echo $flight['departure_city'] . ' - ' . $flight['destination_city']; ?> </p>
                <p class="mb-1 small">Keberangkatan: <?php echo format_datetime($flight['departure_time']); ?></p>
                <p class="mb-1 small">Harga:  <?php echo format_price($flight['price']); ?> </p>
                <p class="mb-0 small">Kursi Tersedia:  <?php echo $seats_available; ?> </p>
            </div>
            
            <?php if ($seats_available > 0): ?>
                <form method="POST">
                    <div class="mb-3">
                        <label for="passenger_name" class="form-label small">Nama Penumpang</label>
                        <input type="text" class="form-control" id="passenger_name" name="passenger_name" required>
                    </div>
                    <div class="mb-4">
                        <label for="identity_number" class="form-label small">Nomor ID (KTP/Paspor)</label>
                        <input type="text" class="form-control" id="identity_number" name="identity_number" required>
                    </div>
                    <button type="submit" class="btn btn-success w-100">Konfirmasi Pemesanan</button>
                </form>
            <?php else: ?>
                <div class="alert alert-danger">Maaf, penerbangan ini sudah penuh.</div>
            <?php endif; ?>

            <a href="../dashboard.php" class="btn btn-link mt-3 text-secondary">← Kembali ke Dashboard</a>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>