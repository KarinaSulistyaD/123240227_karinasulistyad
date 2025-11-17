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

$flight_query = "SELECT * FROM flights WHERE id = $flight_id";
$flight_result = mysqli_query($conn, $flight_query);
$flight = mysqli_fetch_assoc($flight_result);

if (!$flight) {
    set_message("Penerbangan tidak ditemukan.", "danger");
    header('Location: ../dashboard.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $airline_name = mysqli_real_escape_string($conn, $_POST['airline_name']);
    $departure_city = mysqli_real_escape_string($conn, $_POST['departure_city']);
    $destination_city = mysqli_real_escape_string($conn, $_POST['destination_city']);
    $departure_time = mysqli_real_escape_string($conn, $_POST['departure_time']);
    $arrival_time = mysqli_real_escape_string($conn, $_POST['arrival_time']);
    $total_seats = intval($_POST['total_seats']);
    $price = intval($_POST['price']);
    
    // Validasi: Total kursi tidak boleh kurang dari kursi yang sudah dipesan
    if ($total_seats < $flight['seats_booked']) {
        set_message("Gagal menyimpan. Total Kursi tidak boleh kurang dari kursi yang sudah terisi ( {$flight['seats_booked']} ).", "danger");
    } else {
        $query = "UPDATE flights SET 
                    airline_name = '$airline_name', 
                    departure_city = '$departure_city', 
                    destination_city = '$destination_city', 
                    departure_time = '$departure_time', 
                    arrival_time = '$arrival_time', 
                    total_seats = $total_seats, 
                    price = $price 
                  WHERE id = $flight_id";

        if (mysqli_query($conn, $query)) {
            set_message("Data penerbangan  " . $flight['flight_no'] . "  berhasil diubah.", "success");
            header('Location: ../dashboard.php');
            exit();
        } else {
            set_message("Gagal mengubah data penerbangan: " . mysqli_error($conn), "danger");
        }
    }
}

// Ubah format datetime dari database ke format input HTML (YYYY-MM-DDThh:mm)
$dep_time_input = date('Y-m-d\TH:i', strtotime($flight['departure_time']));
$arr_time_input = date('Y-m-d\TH:i', strtotime($flight['arrival_time']));
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Penerbangan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .card-form { max-width: 600px; }
    </style>
</head>
<body>
    <div class="container my-5">
        <div class="card p-4 mx-auto card-form">
            <h4 class="card-title text-center mb-4 border-bottom pb-2">Edit Data Penerbangan</h4>
            <?php display_message(); ?>
            <form method="POST">
                <div class="mb-3">
                    <label for="flight_no" class="form-label small">Kode Penerbangan (Read-only)</label>
                    <input type="text" class="form-control" id="flight_no" value="<?php echo $flight['flight_no']; ?>" readonly>
                </div>
                <div class="mb-3">
                    <label for="airline_name" class="form-label small">Nama Maskapai</label>
                    <input type="text" class="form-control" id="airline_name" name="airline_name" value="<?php echo htmlspecialchars($flight['airline_name']); ?>" required>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="departure_city" class="form-label small">Kota Keberangkatan</label>
                        <input type="text" class="form-control" id="departure_city" name="departure_city" value="<?php echo htmlspecialchars($flight['departure_city']); ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="destination_city" class="form-label small">Kota Kedatangan</label>
                        <input type="text" class="form-control" id="destination_city" name="destination_city" value="<?php echo htmlspecialchars($flight['destination_city']); ?>" required>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="departure_time" class="form-label small">Waktu Keberangkatan</label>
                        <input type="datetime-local" class="form-control" id="departure_time" name="departure_time" value="<?php echo $dep_time_input; ?>" required>
                    </div>
                    <div class="col-md-6 mb-6">
                        <label for="arrival_time" class="form-label small">Waktu Kedatangan</label>
                        <input type="datetime-local" class="form-control" id="arrival_time" name="arrival_time" value="<?php echo $arr_time_input; ?>" required>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="total_seats" class="form-label small">Total Kursi (Terisi: <?php echo $flight['seats_booked']; ?>)</label>
                        <input type="number" class="form-control" id="total_seats" name="total_seats" min="<?php echo $flight['seats_booked']; ?>" value="<?php echo $flight['total_seats']; ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="price" class="form-label small">Harga Tiket (Rp)</label>
                        <input type="number" class="form-control" id="price" name="price" min="0" value="<?php echo $flight['price']; ?>" required>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary w-100">Simpan Perubahan</button>
            </form>
            <a href="../dashboard.php" class="btn btn-link mt-3 text-secondary">← Kembali</a>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>