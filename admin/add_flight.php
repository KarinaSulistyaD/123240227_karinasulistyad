<?php
session_start();
include('../config.php');


if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    set_message("Akses ditolak. Anda harus login sebagai Admin.", "danger");
    header('Location: ../dashboard.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil dan sanitasi input
    $flight_no = mysqli_real_escape_string($conn, $_POST['flight_no']);
    $airline_name = mysqli_real_escape_string($conn, $_POST['airline_name']);
    $departure_city = mysqli_real_escape_string($conn, $_POST['departure_city']);
    $destination_city = mysqli_real_escape_string($conn, $_POST['destination_city']);
    $departure_time = mysqli_real_escape_string($conn, $_POST['departure_time']);
    $arrival_time = mysqli_real_escape_string($conn, $_POST['arrival_time']);
    $total_seats = intval($_POST['total_seats']);
    $price = intval($_POST['price']);
    $seats_booked = 0; // Penerbangan baru, kursi terisi 0

    $query = "INSERT INTO flights (flight_no, airline_name, departure_city, destination_city, departure_time, arrival_time, total_seats, seats_booked, price) 
              VALUES ('$flight_no', '$airline_name', '$departure_city', '$destination_city', '$departure_time', '$arrival_time', $total_seats, $seats_booked, $price)";

    if (mysqli_query($conn, $query)) {
        set_message("Penerbangan  " . $flight_no . "  berhasil ditambahkan.", "success");
        header('Location: ../dashboard.php');
        exit();
    } else {
        set_message("Gagal menambahkan penerbangan: " . mysqli_error($conn), "danger");
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Penerbangan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .card-form { max-width: 600px; }
    </style>
</head>
<body>
    <div class="container my-5">
        <div class="card p-4 mx-auto card-form">
            <h4 class="card-title text-center mb-4 border-bottom pb-2">Tambah Penerbangan Baru</h4>
            <?php display_message(); ?>
            <form method="POST">
                <div class="mb-3">
                    <label for="flight_no" class="form-label small">Kode Penerbangan (misal: GA101)</label>
                    <input type="text" class="form-control" id="flight_no" name="flight_no" required>
                </div>
                <div class="mb-3">
                    <label for="airline_name" class="form-label small">Nama Maskapai</label>
                    <input type="text" class="form-control" id="airline_name" name="airline_name" required>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="departure_city" class="form-label small">Kota Keberangkatan</label>
                        <input type="text" class="form-control" id="departure_city" name="departure_city" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="destination_city" class="form-label small">Kota Kedatangan</label>
                        <input type="text" class="form-control" id="destination_city" name="destination_city" required>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="departure_time" class="form-label small">Waktu Keberangkatan</label>
                        <input type="datetime-local" class="form-control" id="departure_time" name="departure_time" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="arrival_time" class="form-label small">Waktu Kedatangan</label>
                        <input type="datetime-local" class="form-control" id="arrival_time" name="arrival_time" required>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="total_seats" class="form-label small">Total Kursi</label>
                        <input type="number" class="form-control" id="total_seats" name="total_seats" min="1" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="price" class="form-label small">Harga Tiket (Rp)</label>
                        <input type="number" class="form-control" id="price" name="price" min="0" required>
                    </div>
                </div>
                <button type="submit" class="btn btn-success w-100">Tambah Penerbangan</button>
            </form>
            <a href="../dashboard.php" class="btn btn-link mt-3 text-secondary">← Kembali</a>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>