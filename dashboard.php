<?php
session_start();
include('config.php');

// Ambil semua data penerbangan
$flights_query = "SELECT * FROM flights ORDER BY departure_time ASC";
$flights_result = mysqli_query($conn, $flights_query);
$flights = mysqli_fetch_all($flights_result, MYSQLI_ASSOC);

// Data untuk section 'Pemesanan Saya' (User) atau 'Semua Pemesanan' (Admin)
$is_logged_in = isset($_SESSION['user_id']);
$user_bookings = [];
$all_bookings = [];

if ($is_logged_in) {
    if ($_SESSION['role'] === 'user') {
        // Ambil data pemesanan user yang sedang login
        $user_id = $_SESSION['user_id'];
        $bookings_query = "
            SELECT 
                b.identity_number, b.passenger_name, b.booking_time, 
                f.flight_no, f.airline_name, f.departure_city, f.destination_city, f.price
            FROM bookings b
            JOIN flights f ON b.flight_id = f.id
            WHERE b.user_id = $user_id
            ORDER BY b.booking_time DESC";
        $bookings_result = mysqli_query($conn, $bookings_query);
        $user_bookings = mysqli_fetch_all($bookings_result, MYSQLI_ASSOC);
    } elseif ($_SESSION['role'] === 'admin') {
        // Ambil semua data pemesanan untuk Admin
        $all_bookings_query = "
            SELECT 
                b.id AS booking_id, b.identity_number, b.passenger_name, b.booking_time, 
                f.flight_no, f.airline_name, f.departure_city, f.destination_city, f.price,
                u.username, u.full_name AS user_fullname
            FROM bookings b
            JOIN flights f ON b.flight_id = f.id
            JOIN users u ON b.user_id = u.id
            ORDER BY b.booking_time DESC";
        $all_bookings_result = mysqli_query($conn, $all_bookings_query);
        $all_bookings = mysqli_fetch_all($all_bookings_result, MYSQLI_ASSOC);
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Airline Booking System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .navbar { background-color: #4c4cc5 !important; }
        .flight-card { min-height: 250px; }
        .admin-action-buttons a { width: 100%; margin-bottom: 5px; }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container-fluid container">
            <a class="navbar-brand" href="dashboard.php">Airline Booking System</a>
            <div class="d-flex">
                <?php if ($is_logged_in): ?>
                    <span class="navbar-text me-3 text-white">
                        Selamat datang,  <?php echo $_SESSION['username']; ?>  (<?php echo $_SESSION['role']; ?>)
                    </span>
                    <a href="logout.php" class="btn btn-light btn-sm">Logout</a>
                <?php else: ?>
                    <a href="index.php" class="btn btn-light btn-sm">Login</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <div class="container my-4">
        <?php display_message(); ?>

        [cite_start]<?php if ($is_logged_in && $_SESSION['role'] === 'admin'): // Admin View[cite: 18, 95]?>
            
            <h4 class="mb-3">✈️ Manajemen Penerbangan</h4>
            <div class="mb-4">
                <a href="admin/add_flight.php" class="btn btn-success btn-sm">➕ Tambah Penerbangan</a>
            </div>

            <div class="row">
                <?php foreach ($flights as $flight): ?>
                    <div class="col-md-4 mb-4">
                        <div class="card flight-card">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo $flight['flight_no'] . ' - ' . $flight['airline_name']; ?></h5>
                                <p class="card-text small">
                                    Rute:  <?php echo $flight['departure_city'] . ' - ' . $flight['destination_city']; ?> <br>
                                    Keberangkatan: <?php echo format_datetime($flight['departure_time']); ?><br>
                                    Kedatangan: <?php echo format_datetime($flight['arrival_time']); ?><br>
                                    Kursi:  <?php echo $flight['seats_booked'] . '/' . $flight['total_seats']; ?>  (Sisa: <?php echo $flight['total_seats'] - $flight['seats_booked']; ?>)<br>
                                    Harga:  <?php echo format_price($flight['price']); ?> 
                                </p>
                                <div class="row admin-action-buttons">
                                    <div class="col-6">
                                        <a href="admin/edit_flight.php?id=<?php echo $flight['id']; ?>" class="btn btn-primary btn-sm">Edit</a>
                                    </div>
                                    <div class="col-6">
                                        <a href="admin/delete_flight.php?id=<?php echo $flight['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus penerbangan ini? Semua pemesanan terkait juga akan terhapus.')">Hapus</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <hr>

            <h4 class="mb-3">🧾 Semua Pemesanan</h4>
            <div class="table-responsive">
                <?php if (!empty($all_bookings)): ?>
                <table class="table table-striped table-sm">
                    <thead>
                        <tr class="table-primary">
                            <th>Kode Booking</th>
                            <th>Penerbangan</th>
                            <th>Nama Pemesan</th>
                            <th>Nama Penumpang</th>
                            <th>Harga</th>
                            <th>Status</th>
                            <th>Tanggal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($all_bookings as $booking): ?>
                        <tr>
                            <td><?php echo substr(md5($booking['booking_id'] . $booking['identity_number']), 0, 15); ?></td>
                            <td><?php echo $booking['flight_no']; ?></td>
                            <td><?php echo $booking['user_fullname']; ?></td>
                            <td><?php echo $booking['passenger_name']; ?></td>
                            <td><?php echo format_price($booking['price']); ?></td>
                            <td><span class="badge bg-success">Confirmed</span></td>
                            <td><?php echo format_datetime($booking['booking_time']); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php else: ?>
                <div class="alert alert-info">Belum ada pemesanan yang tercatat.</div>
                <?php endif; ?>
            </div>

        [cite_start]<?php else: // Regular User View & Guest View[cite: 19, 59]?>

            <h4 class="mb-3">🎟️ Pesan Tiket Pesawat</h4>
            <div class="row">
                <?php foreach ($flights as $flight): ?>
                    <?php 
                        $seats_available = $flight['total_seats'] - $flight['seats_booked'];
                        $is_full = $seats_available <= 0;
                    ?>
                    <div class="col-md-4 mb-4">
                        <div class="card flight-card">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo $flight['flight_no'] . ' - ' . $flight['airline_name']; ?></h5>
                                <p class="card-text small">
                                    Rute:  <?php echo $flight['departure_city'] . ' - ' . $flight['destination_city']; ?> <br>
                                    Keberangkatan: <?php echo format_datetime($flight['departure_time']); ?><br>
                                    Kedatangan: <?php echo format_datetime($flight['arrival_time']); ?><br>
                                    Kursi Tersedia:  <?php echo $seats_available; ?> / <?php echo $flight['total_seats']; ?> <br>
                                    Harga:  <?php echo format_price($flight['price']); ?> 
                                </p>
                                
                                <?php if ($is_logged_in): ?>
                                    <?php if ($is_full): ?>
                                        <button class="btn btn-secondary w-100" disabled>Penuh</button>
                                    <?php else: ?>
                                        <a href="user/booking.php?flight_id=<?php echo $flight['id']; ?>" class="btn btn-primary w-100">Pesan Tiket</a>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <button class="btn btn-secondary w-100" disabled>Login untuk Memesan</button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <hr>

            <h4 class="mb-3">🛒 Pemesanan Saya</h4>
            <?php if ($is_logged_in && $_SESSION['role'] === 'user'): ?>
                <?php if (!empty($user_bookings)): ?>
                    <div class="table-responsive">
                    <table class="table table-striped table-sm">
                        <thead>
                            <tr class="table-primary">
                                <th>Kode Booking</th>
                                <th>Penerbangan</th>
                                <th>Rute</th>
                                <th>Nama Penumpang</th>
                                <th>Harga</th>
                                <th>Status</th>
                                <th>Tanggal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($user_bookings as $booking): ?>
                            <tr>
                                <td><?php echo substr(md5($booking['identity_number'] . $booking['booking_time']), 0, 15); ?></td>
                                <td><?php echo $booking['flight_no']; ?></td>
                                <td><?php echo $booking['departure_city'] . ' - ' . $booking['destination_city']; ?></td>
                                <td><?php echo $booking['passenger_name']; ?></td>
                                <td><?php echo format_price($booking['price']); ?></td>
                                <td><span class="badge bg-success">Confirmed</span></td>
                                <td><?php echo format_datetime($booking['booking_time']); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info">Anda belum melakukan pemesanan apapun.</div>
                <?php endif; ?>
            <?php elseif ($is_logged_in && $_SESSION['role'] === 'admin'): ?>
                 <div class="alert alert-warning">Anda login sebagai Admin. Silakan lihat bagian  Semua Pemesanan  di bawah.</div>
            <?php else: ?>
                 <div class="alert alert-secondary">Silakan Login untuk melihat riwayat pemesanan Anda.</div>
            <?php endif; ?>

        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>