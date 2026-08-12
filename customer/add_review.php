<?php
session_start();
if (!isset($_SESSION['customer_id'])) {
    header("Location: ../auth/login.php");
    exit();
}
require_once __DIR__ . '/../config/database.php';

$customer_id = $_SESSION['customer_id'];
$booking_id = isset($_GET['booking_id']) ? $_GET['booking_id'] : '';

// Get booking details
$booking = mysqli_fetch_assoc(mysqli_query($conn, "SELECT b.*, v.vehicle_name, d.full_name AS driver_name 
    FROM booking b 
    LEFT JOIN vehicle v ON b.vehicle_id = v.vehicle_id 
    LEFT JOIN driver d ON b.driver_id = d.driver_id 
    WHERE b.booking_id = '$booking_id' AND b.customer_id = '$customer_id'"));

if (!$booking) {
    header("Location: bookings.php");
    exit();
}

// Check if review already exists
$existing_review = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM reviews WHERE user_type = 'customer' AND user_id = '$customer_id' AND target_type = 'vehicle' AND target_id = '{$booking['vehicle_id']}'"));

// Handle review submission
if(isset($_POST['submit_review'])) {
    $rating = $_POST['rating'];
    $comment = mysqli_real_escape_string($conn, $_POST['comment']);
    $target_type = $_POST['target_type'];
    $target_id = ($target_type == 'vehicle') ? $booking['vehicle_id'] : $booking['driver_id'];
    
    // Delete existing review if any
    if($existing_review) {
        mysqli_query($conn, "DELETE FROM reviews WHERE review_id = '{$existing_review['review_id']}'");
    }
    
    // Insert new review
    $sql = "INSERT INTO reviews (user_type, user_id, target_type, target_id, rating, comment, status) 
            VALUES ('customer', '$customer_id', '$target_type', '$target_id', '$rating', '$comment', 'pending')";
    
    if(mysqli_query($conn, $sql)) {
        // Update ratings immediately for better user experience
        if($target_type == 'driver' && $booking['driver_id']) {
            updateDriverRating($conn, $booking['driver_id']);
        }
        if($target_type == 'vehicle') {
            updateVehicleRating($conn, $booking['vehicle_id']);
        }
        
        header("Location: bookings.php?review=success");
        exit();
    } else {
        $error = "Review submission failed: " . mysqli_error($conn);
    }
}

// Function to update driver rating
function updateDriverRating($conn, $driver_id) {
    // Get all approved reviews for this driver
    $reviews = mysqli_query($conn, "SELECT rating FROM reviews WHERE target_type = 'driver' AND target_id = '$driver_id' AND status = 'approved'");
    
    if($reviews && mysqli_num_rows($reviews) > 0) {
        $total = 0;
        $count = 0;
        while($row = mysqli_fetch_assoc($reviews)) {
            $total += $row['rating'];
            $count++;
        }
        $avg_rating = $total / $count;
        
        // Update driver table
        mysqli_query($conn, "UPDATE driver SET rating = '$avg_rating', rating_count = '$count' WHERE driver_id = '$driver_id'");
    }
}

// Function to update vehicle rating
function updateVehicleRating($conn, $vehicle_id) {
    // Get all reviews for this vehicle (including pending ones for immediate feedback)
    $reviews = mysqli_query($conn, "SELECT rating FROM reviews WHERE target_type = 'vehicle' AND target_id = '$vehicle_id'");
    
    if($reviews && mysqli_num_rows($reviews) > 0) {
        $total = 0;
        $count = 0;
        while($row = mysqli_fetch_assoc($reviews)) {
            $total += $row['rating'];
            $count++;
        }
        $avg_rating = $total / $count;
        
        // Add rating column to vehicle table if it doesn't exist
        $check_column = mysqli_query($conn, "SHOW COLUMNS FROM vehicle LIKE 'rating'");
        if(mysqli_num_rows($check_column) == 0) {
            mysqli_query($conn, "ALTER TABLE vehicle ADD COLUMN rating DECIMAL(3,2) DEFAULT 0.00");
            mysqli_query($conn, "ALTER TABLE vehicle ADD COLUMN rating_count INT DEFAULT 0");
        }
        
        // Update vehicle table
        mysqli_query($conn, "UPDATE vehicle SET rating = '$avg_rating', rating_count = '$count' WHERE vehicle_id = '$vehicle_id'");
    }
}

$active_page = 'My Bookings';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Review - RideRent Pro</title>
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
            <h1><i class="fas fa-star"></i> Add Review</h1>
            <p>Share your experience</p>
        </div>

        <?php if(isset($error)) { ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
            </div>
        <?php } ?>

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Booking Details</h3>
            </div>
            <div class="card-body">
                <p><strong>Vehicle:</strong> <?php echo $booking['vehicle_name']; ?></p>
                <p><strong>Driver:</strong> <?php echo $booking['driver_name'] ? $booking['driver_name'] : 'No Driver'; ?></p>
                <p><strong>Booking ID:</strong> #<?php echo $booking['booking_id']; ?></p>
                <p><strong>Total Price:</strong> ৳<?php echo $booking['total_price']; ?></p>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Write Your Review</h3>
            </div>
            <div class="card-body">
                <form method="POST">
                    <div class="form-group">
                        <label class="form-label">What would you like to review?</label>
                        <select name="target_type" class="form-select" required onchange="toggleDriverOption()">
                            <option value="vehicle">Vehicle Only</option>
                            <option value="driver" <?php echo $booking['driver_id'] ? '' : 'disabled'; ?>>Driver Only</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Rating</label>
                        <div class="rating-stars">
                            <?php for($i = 5; $i >= 1; $i--) { ?>
                                <input type="radio" id="star<?php echo $i; ?>" name="rating" value="<?php echo $i; ?>" required>
                                <label for="star<?php echo $i; ?>" class="star"><i class="fas fa-star"></i></label>
                            <?php } ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Your Review</label>
                        <textarea name="comment" class="form-control" rows="5" placeholder="Share your experience..." required></textarea>
                    </div>

                    <button type="submit" name="submit_review" class="btn btn-primary">
                        <i class="fas fa-paper-plane"></i> Submit Review
                    </button>
                    <a href="bookings.php" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
.rating-stars {
    display: flex;
    flex-direction: row-reverse;
    justify-content: flex-end;
    gap: 5px;
}

.rating-stars input {
    display: none;
}

.rating-stars label {
    cursor: pointer;
    font-size: 30px;
    color: #ddd;
    transition: color 0.2s;
}

.rating-stars input:checked ~ label,
.rating-stars label:hover,
.rating-stars label:hover ~ label {
    color: #FD7E14;
}
</style>

</body>
</html>