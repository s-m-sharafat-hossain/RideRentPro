<?php
session_start();
if (!isset($_SESSION['driver_id'])) {
    header("Location: ../auth/login.php");
    exit();
}
require_once __DIR__ . '/../config/database.php';

$driver_id = $_SESSION['driver_id'];

// Set active page for sidebar
$active_page = 'My Bookings';

if (!isset($_GET['id'])) {
    header("Location: bookings.php");
    exit();
}

$booking_id = $_GET['id'];

// Get booking details (only driver's bookings)
$sql = "SELECT b.*, c.full_name AS customer_name, c.phone_1 AS customer_phone, c.email AS customer_email, c.address AS customer_address,
        v.vehicle_name, v.brand, v.model, v.vehicle_type, v.fuel_type, v.transmission, v.seat_capacity, v.image AS vehicle_image,
        o.full_name AS owner_name, o.phone AS owner_phone
        FROM booking b
        LEFT JOIN customer c ON b.customer_id = c.customer_id
        LEFT JOIN vehicle v ON b.vehicle_id = v.vehicle_id
        LEFT JOIN vehicle_owner o ON v.owner_id = o.owner_id
        WHERE b.booking_id = '$booking_id' AND b.driver_id = '$driver_id'";

$result = mysqli_query($conn, $sql);

if (!$result || mysqli_num_rows($result) == 0) {
    echo "Booking not found or you don't have permission to view this booking!";
    exit();
}

$booking = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Details - RideRent Pro</title>
    <link rel="stylesheet" href="../assets/css/new-theme.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
<script src="../assets/js/theme.js"></script>

<!-- Dashboard Container -->
<div class="dashboard-container">
    <?php require_once __DIR__ . '/../includes/sidebar.php'; ?>

    <!-- Main Content -->
    <div class="main-content">
        <div class="page-header">
            <h1><i class="fas fa-file-alt"></i> Booking Details</h1>
            <p>View complete booking information</p>
        </div>

        <a href="bookings.php" class="btn btn-secondary" style="margin-bottom: 20px;"><i class="fas fa-arrow-left"></i> Back to Bookings</a>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px;">
            <!-- Booking Summary -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Booking Summary</h3>
                </div>
                <div class="card-body">
                    <table style="width: 100%;">
                        <tr><th style="padding: 10px; background: var(--off-white); font-weight: 600;">Booking ID:</th><td style="padding: 10px;">#<?php echo $booking['booking_id']; ?></td></tr>
                        <tr><th style="padding: 10px; background: var(--off-white); font-weight: 600;">Booking Date:</th><td style="padding: 10px;"><?php echo $booking['booking_date']; ?></td></tr>
                        <tr><th style="padding: 10px; background: var(--off-white); font-weight: 600;">Start Date:</th><td style="padding: 10px;"><?php echo $booking['start_date']; ?></td></tr>
                        <tr><th style="padding: 10px; background: var(--off-white); font-weight: 600;">End Date:</th><td style="padding: 10px;"><?php echo $booking['end_date']; ?></td></tr>
                        <tr><th style="padding: 10px; background: var(--off-white); font-weight: 600;">Total Days:</th><td style="padding: 10px;"><?php echo $booking['total_days']; ?></td></tr>
                        <tr><th style="padding: 10px; background: var(--off-white); font-weight: 600;">Driver Fee:</th><td style="padding: 10px; color: var(--accent-pink); font-weight: 700;">৳<?php echo number_format($booking['driver_fee'], 2); ?></td></tr>
                        <tr><th style="padding: 10px; background: var(--off-white); font-weight: 600;">Total Price:</th><td style="padding: 10px; color: var(--accent-pink); font-weight: 700;">৳<?php echo number_format($booking['total_price'], 2); ?></td></tr>
                        <tr><th style="padding: 10px; background: var(--off-white); font-weight: 600;">Booking Status:</th><td style="padding: 10px;"><span class='badge badge-success'><?php echo $booking['booking_status']; ?></span></td></tr>
                        <tr><th style="padding: 10px; background: var(--off-white); font-weight: 600;">Payment Status:</th><td style="padding: 10px;"><span class='badge badge-success'><?php echo $booking['payment_status']; ?></span></td></tr>
                    </table>
                </div>
            </div>

            <!-- Vehicle Information -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Vehicle Information</h3>
                </div>
                <div class="card-body">
                    <?php if(!empty($booking['vehicle_image'])) { ?>
                        <img src="../assets/uploads/<?php echo $booking['vehicle_image']; ?>" alt="<?php echo $booking['vehicle_name']; ?>" style="width: 100%; height: 200px; object-fit: cover; border-radius: var(--radius-md); margin-bottom: 20px;">
                    <?php } else { ?>
                        <img src="https://via.placeholder.com/400x200?text=No+Image" alt="<?php echo $booking['vehicle_name']; ?>" style="width: 100%; height: 200px; object-fit: cover; border-radius: var(--radius-md); margin-bottom: 20px;">
                    <?php } ?>
                    
                    <h2><?php echo $booking['vehicle_name']; ?></h2>
                    <p style="color: var(--medium-gray);"><?php echo $booking['brand']; ?> <?php echo $booking['model']; ?></p>
                    
                    <hr style="margin: 20px 0;">
                    
                    <table style="width: 100%;">
                        <tr><th style="padding: 10px; background: var(--off-white); font-weight: 600;">Type:</th><td style="padding: 10px;"><?php echo $booking['vehicle_type']; ?></td></tr>
                        <tr><th style="padding: 10px; background: var(--off-white); font-weight: 600;">Fuel:</th><td style="padding: 10px;"><?php echo $booking['fuel_type']; ?></td></tr>
                        <tr><th style="padding: 10px; background: var(--off-white); font-weight: 600;">Transmission:</th><td style="padding: 10px;"><?php echo $booking['transmission']; ?></td></tr>
                        <tr><th style="padding: 10px; background: var(--off-white); font-weight: 600;">Seats:</th><td style="padding: 10px;"><?php echo $booking['seat_capacity']; ?></td></tr>
                        <tr><th style="padding: 10px; background: var(--off-white); font-weight: 600;">Owner:</th><td style="padding: 10px;"><?php echo $booking['owner_name']; ?> (<?php echo $booking['owner_phone']; ?>)</td></tr>
                    </table>
                </div>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px; margin-top: 30px;">
            <!-- Customer Information -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Customer Information</h3>
                </div>
                <div class="card-body">
                    <table style="width: 100%;">
                        <tr><th style="padding: 10px; background: var(--off-white); font-weight: 600;">Name:</th><td style="padding: 10px;"><?php echo $booking['customer_name']; ?></td></tr>
                        <tr><th style="padding: 10px; background: var(--off-white); font-weight: 600;">Phone:</th><td style="padding: 10px;">📞 <?php echo $booking['customer_phone']; ?></td></tr>
                        <tr><th style="padding: 10px; background: var(--off-white); font-weight: 600;">Email:</th><td style="padding: 10px;">✉️ <?php echo $booking['customer_email']; ?></td></tr>
                        <tr><th style="padding: 10px; background: var(--off-white); font-weight: 600;">Address:</th><td style="padding: 10px;">📍 <?php echo $booking['customer_address']; ?></td></tr>
                    </table>
                </div>
            </div>

            <!-- Location Information -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Location Information</h3>
                </div>
                <div class="card-body">
                    <table style="width: 100%;">
                        <tr><th style="padding: 10px; background: var(--off-white); font-weight: 600;">Pickup Location:</th><td style="padding: 10px;">📍 <?php echo $booking['pickup_location']; ?></td></tr>
                        <tr><th style="padding: 10px; background: var(--off-white); font-weight: 600;">Dropoff Location:</th><td style="padding: 10px;">📍 <?php echo $booking['dropoff_location']; ?></td></tr>
                        <tr><th style="padding: 10px; background: var(--off-white); font-weight: 600;">Special Requests:</th><td style="padding: 10px;"><?php echo $booking['special_requests'] ? $booking['special_requests'] : 'None'; ?></td></tr>
                    </table>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="card" style="margin-top: 30px;">
            <div class="card-header">
                <h3 class="card-title">Actions</h3>
            </div>
            <div class="card-body">
                <?php if($booking['booking_status'] == 'Confirmed' || $booking['booking_status'] == 'Driver_Requested') { ?>
                    <a href="bookings.php?id=<?php echo $booking['booking_id']; ?>&status=Completed" class="btn btn-success" onclick="return confirm('Mark this booking as completed?')">
                        <i class="fas fa-check-circle"></i> Mark as Completed
                    </a>
                <?php } elseif($booking['booking_status'] == 'Completed') { ?>
                    <button class="btn btn-secondary" disabled>
                        <i class="fas fa-check-circle"></i> Booking Completed
                    </button>
                <?php } ?>
                <a href="bookings.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Bookings
                </a>
            </div>
        </div>
    </div>
</div>

</body>
</html>