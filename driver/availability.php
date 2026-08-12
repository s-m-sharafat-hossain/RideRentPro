<?php
session_start();
if (!isset($_SESSION['driver_id'])) {
    header("Location: ../auth/login.php");
    exit();
}
require_once __DIR__ . '/../config/database.php';

$driver_id = $_SESSION['driver_id'];

// Handle availability toggle
if(isset($_POST['toggle_availability'])) {
    $new_status = $_POST['availability'];
    mysqli_query($conn, "UPDATE driver SET availability = '$new_status' WHERE driver_id = '$driver_id'");
    header("Location: availability.php");
    exit();
}

// Get current availability
$driver_info = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM driver WHERE driver_id = '$driver_id'"));
$current_status = $driver_info['availability'];

// Set active page for sidebar
$active_page = 'Availability';
require_once __DIR__ . '/../includes/sidebar.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Availability Status - RideRent Pro</title>
    <link rel="stylesheet" href="../assets/css/new-theme.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
<script src="../assets/js/theme.js"></script>

<!-- Dashboard Container -->
<div class="dashboard-container">

    <!-- Main Content -->
    <div class="main-content">
        <div class="page-header">
            <h1><i class="fas fa-toggle-on"></i> Availability Status</h1>
            <p>Manage your availability for bookings</p>
        </div>

        <div class="card">
            <div class="card-body" style="text-align: center; padding: 50px;">
                <div style="margin-bottom: 30px;">
                    <?php if($current_status == 'Available') { ?>
                        <i class="fas fa-check-circle" style="font-size: 80px; color: #00C9A7; margin-bottom: 20px;"></i>
                        <h2 style="color: #00C9A7;">You are Available</h2>
                        <p style="color: var(--medium-gray);">You can receive new booking assignments</p>
                    <?php } else { ?>
                        <i class="fas fa-times-circle" style="font-size: 80px; color: #E74C3C; margin-bottom: 20px;"></i>
                        <h2 style="color: #E74C3C;">You are Unavailable</h2>
                        <p style="color: var(--medium-gray);">You won't receive new booking assignments</p>
                    <?php } ?>
                </div>

                <form method="POST">
                    <input type="hidden" name="availability" value="<?php echo $current_status == 'Available' ? 'Unavailable' : 'Available'; ?>">
                    <button type="submit" name="toggle_availability" class="btn <?php echo $current_status == 'Available' ? 'btn-danger' : 'btn-success'; ?> btn-lg">
                        <i class="fas fa-power-off"></i> 
                        <?php echo $current_status == 'Available' ? 'Go Unavailable' : 'Go Available'; ?>
                    </button>
                </form>

                <div style="margin-top: 40px; text-align: left; max-width: 500px; margin-left: auto; margin-right: auto;">
                    <h4><i class="fas fa-info-circle"></i> What this means:</h4>
                    <ul style="color: var(--medium-gray); line-height: 1.8;">
                        <li><strong>Available:</strong> Admin can assign you to new bookings and customers can request you as their driver.</li>
                        <li><strong>Unavailable:</strong> You won't be assigned to new bookings, but existing bookings will continue as normal.</li>
                        <li><strong>Tip:</strong> Set yourself to unavailable when you're on vacation, sick, or need a break.</li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Your Statistics</h3>
            </div>
            <div class="card-body">
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px;">
                    <div style="text-align: center; padding: 20px; background: var(--off-white); border-radius: var(--radius-md);">
                        <i class="fas fa-calendar-check" style="font-size: 30px; color: #2F80ED; margin-bottom: 10px;"></i>
                        <h4>Total Bookings</h4>
                        <p style="font-size: 24px; font-weight: 700; color: var(--dark-gray);">
                            <?php 
                            $total = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM booking WHERE driver_id = '$driver_id'"));
                            echo $total['count'];
                            ?>
                        </p>
                    </div>
                    <div style="text-align: center; padding: 20px; background: var(--off-white); border-radius: var(--radius-md);">
                        <i class="fas fa-check-circle" style="font-size: 30px; color: #00C9A7; margin-bottom: 10px;"></i>
                        <h4>Completed Trips</h4>
                        <p style="font-size: 24px; font-weight: 700; color: var(--dark-gray);">
                            <?php 
                            $completed = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM booking WHERE driver_id = '$driver_id' AND booking_status = 'Completed'"));
                            echo $completed['count'];
                            ?>
                        </p>
                    </div>
                    <div style="text-align: center; padding: 20px; background: var(--off-white); border-radius: var(--radius-md);">
                        <i class="fas fa-star" style="font-size: 30px; color: #FD7E14; margin-bottom: 10px;"></i>
                        <h4>Your Rating</h4>
                        <p style="font-size: 24px; font-weight: 700; color: var(--dark-gray);"><?php echo $driver_info['rating']; ?> ⭐</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>