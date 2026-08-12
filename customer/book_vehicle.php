<?php
session_start();
if (!isset($_SESSION['customer_id'])) {
    header("Location: ../auth/login.php");
    exit();
}
require_once __DIR__ . '/../config/database.php';

$customer_id = $_SESSION['customer_id'];

if (!isset($_GET['id'])) {
    header("Location: vehicles.php");
    exit();
}

$vehicle_id = $_GET['id'];

// Get vehicle details
$vehicle = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM vehicle WHERE vehicle_id = '$vehicle_id'"));

if (!$vehicle) {
    echo "Vehicle not found!";
    exit();
}

// Handle booking
if(isset($_POST['book_vehicle'])) {
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $driver_option = $_POST['driver_option'];
    $pickup_location = mysqli_real_escape_string($conn, $_POST['pickup_location']);
    $dropoff_location = mysqli_real_escape_string($conn, $_POST['dropoff_location']);
    $special_requests = mysqli_real_escape_string($conn, $_POST['special_requests']);
    
    // Calculate days and total price
    $start = new DateTime($start_date);
    $end = new DateTime($end_date);
    $days = $start->diff($end)->days + 1;
    $total_price = $days * $vehicle['price_per_day'];
    
    // Driver fee
    $driver_fee = 0;
    $driver_id = NULL;

    if($driver_option == 'with_driver') {
        $driver_fee = 500 * $days; // ৳500 per day for driver
        // Don't auto-assign driver - let admin assign or leave for manual assignment
        // Set booking status to indicate driver needed
    }
    
    $total_price += $driver_fee;
    
    // Insert booking
    $booking_status = ($driver_option == 'with_driver') ? 'Driver_Requested' : 'Confirmed';
    $sql = "INSERT INTO booking (customer_id, vehicle_id, driver_id, owner_id, start_date, end_date, total_days, price_per_day, total_price, driver_fee, pickup_location, dropoff_location, special_requests, booking_status)
            VALUES ('$customer_id', '$vehicle_id', '$driver_id', '{$vehicle['owner_id']}', '$start_date', '$end_date', '$days', '{$vehicle['price_per_day']}', '$total_price', '$driver_fee', '$pickup_location', '$dropoff_location', '$special_requests', '$booking_status')";
    
    if(mysqli_query($conn, $sql)) {
        $booking_id = mysqli_insert_id($conn);
        
        // Update vehicle availability
        mysqli_query($conn, "UPDATE vehicle SET availability = 'Booked' WHERE vehicle_id = '$vehicle_id'");
        
        // Update driver availability if assigned
        if($driver_id) {
            mysqli_query($conn, "UPDATE driver SET availability = 'Unavailable' WHERE driver_id = '$driver_id'");
        }
        
        // Check if customer wants to pay now or later
        if(isset($_POST['payment_option']) && $_POST['payment_option'] == 'pay_now') {
            header("Location: payment.php?booking_id=$booking_id");
        } else {
            header("Location: bookings.php");
        }
        exit();
    } else {
        $error = "Booking failed: " . mysqli_error($conn);
    }
}

$active_page = 'Browse Vehicles';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Vehicle - RideRent Pro</title>
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
            <h1><i class="fas fa-calendar-plus"></i> Book Vehicle</h1>
            <p>Complete your booking details</p>
        </div>

        <?php if(isset($error)) { ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
            </div>
        <?php } ?>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px;">
            <!-- Vehicle Details -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Vehicle Details</h3>
                </div>
                <div class="card-body">
                    <?php if(!empty($vehicle['image'])) { ?>
                        <img src="../assets/uploads/<?php echo $vehicle['image']; ?>" alt="<?php echo $vehicle['vehicle_name']; ?>" style="width: 100%; height: 250px; object-fit: cover; border-radius: var(--radius-md); margin-bottom: 20px;">
                    <?php } else { ?>
                        <img src="https://via.placeholder.com/400x250?text=No+Image" alt="<?php echo $vehicle['vehicle_name']; ?>" style="width: 100%; height: 250px; object-fit: cover; border-radius: var(--radius-md); margin-bottom: 20px;">
                    <?php } ?>
                    
                    <h2><?php echo $vehicle['vehicle_name']; ?></h2>
                    <p style="color: var(--medium-gray);"><?php echo $vehicle['brand']; ?> <?php echo $vehicle['model']; ?> (<?php echo $vehicle['year']; ?>)</p>
                    
                    <hr style="margin: 20px 0;">
                    
                    <table style="width: 100%;">
                        <tr><th style="padding: 10px; background: var(--off-white); color: var(--dark-gray); font-weight: 600;">Type:</th><td style="padding: 10px;"><?php echo $vehicle['vehicle_type']; ?></td></tr>
                        <tr><th style="padding: 10px; background: var(--off-white); color: var(--dark-gray); font-weight: 600;">Fuel:</th><td style="padding: 10px;"><?php echo $vehicle['fuel_type']; ?></td></tr>
                        <tr><th style="padding: 10px; background: var(--off-white); color: var(--dark-gray); font-weight: 600;">Transmission:</th><td style="padding: 10px;"><?php echo $vehicle['transmission']; ?></td></tr>
                        <tr><th style="padding: 10px; background: var(--off-white); color: var(--dark-gray); font-weight: 600;">Seats:</th><td style="padding: 10px;"><?php echo $vehicle['seat_capacity']; ?></td></tr>
                        <tr><th style="padding: 10px; background: var(--off-white); color: var(--dark-gray); font-weight: 600;">Location:</th><td style="padding: 10px;"><?php echo $vehicle['location']; ?></td></tr>
                    </table>
                    
                    <p style="font-size: 32px; font-weight: 700; color: var(--accent-pink); margin: 20px 0;">৳<?php echo $vehicle['price_per_day']; ?> / day</p>
                    
                    <?php if($vehicle['description']) { ?>
                        <p><strong>Description:</strong><br><?php echo nl2br($vehicle['description']); ?></p>
                    <?php } ?>
                </div>
            </div>

            <!-- Booking Form -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Booking Details</h3>
                </div>
                <div class="card-body">
                    <form method="POST">
                        <div class="form-group">
                            <label class="form-label">Start Date</label>
                            <input type="date" name="start_date" class="form-control" required min="<?php echo date('Y-m-d'); ?>">
                        </div>
                        <div class="form-group">
                            <label class="form-label">End Date</label>
                            <input type="date" name="end_date" class="form-control" required min="<?php echo date('Y-m-d'); ?>">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Driver Option</label>
                            <select name="driver_option" class="form-select" required>
                                <option value="without_driver">Without Driver</option>
                                <option value="with_driver">With Driver (+৳500/day)</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Pickup Location</label>
                            <input type="text" name="pickup_location" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Dropoff Location</label>
                            <input type="text" name="dropoff_location" class="form-control">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Special Requests</label>
                            <textarea name="special_requests" class="form-control" rows="3"></textarea>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Payment Option</label>
                            <div style="margin: 10px 0;">
                                <label style="margin-right: 20px;">
                                    <input type="radio" name="payment_option" value="pay_later" checked> Pay Later
                                </label>
                                <label>
                                    <input type="radio" name="payment_option" value="pay_now"> Pay Now
                                </label>
                            </div>
                            <small class="text-muted">Choose "Pay Now" to complete payment immediately, or "Pay Later" to pay separately.</small>
                        </div>
                        
                        <div style="background: var(--off-white); padding: 20px; border-radius: var(--radius-md); margin: 20px 0;">
                            <h4>Payment Summary</h4>
                            <p>Vehicle rental will be calculated based on your selected dates.</p>
                            <p><strong>Note:</strong> Driver fee (if selected) is ৳500 per day.</p>
                        </div>
                        
                        <button type="submit" name="book_vehicle" class="btn btn-primary" style="width: 100%;">Confirm Booking</button>
                        <a href="vehicles.php" class="btn btn-secondary" style="width: 100%; margin-top: 10px;">Cancel</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>