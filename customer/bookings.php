<?php
session_start();
if (!isset($_SESSION['customer_id'])) {
    header("Location: ../auth/login.php");
    exit();
}
require_once __DIR__ . '/../config/database.php';

$customer_id = $_SESSION['customer_id'];

// Handle cancellation
if(isset($_GET['cancel']) && isset($_GET['id'])) {
    $id = $_GET['id'];
    // Only allow cancellation of pending bookings
    mysqli_query($conn, "UPDATE booking SET booking_status = 'Cancelled' WHERE booking_id = '$id' AND customer_id = '$customer_id' AND booking_status = 'Pending'");
    
    // Make vehicle available again
    $booking = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM booking WHERE booking_id = '$id'"));
    if($booking) {
        mysqli_query($conn, "UPDATE vehicle SET availability = 'Available' WHERE vehicle_id = '{$booking['vehicle_id']}'");
        if($booking['driver_id']) {
            mysqli_query($conn, "UPDATE driver SET availability = 'Available' WHERE driver_id = '{$booking['driver_id']}'");
        }
    }
    
    header("Location: bookings.php");
    exit();
}

// Set active page for sidebar
$active_page = 'My Bookings';
require_once __DIR__ . '/../includes/sidebar.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Bookings - RideRent Pro</title>
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
            <h1><i class="fas fa-calendar-check"></i> My Bookings</h1>
            <p>View and manage your vehicle rentals</p>
        </div>

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Booking History</h3>
            </div>
            <div class="card-body">
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Vehicle</th>
                                <th>Driver</th>
                                <th>Pickup</th>
                                <th>Dropoff</th>
                                <th>Start Date</th>
                                <th>End Date</th>
                                <th>Days</th>
                                <th>Total Price</th>
                                <th>Status</th>
                                <th>Payment</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $result = mysqli_query($conn, "SELECT b.*, v.vehicle_name, d.full_name AS driver_name 
                                FROM booking b 
                                LEFT JOIN vehicle v ON b.vehicle_id = v.vehicle_id 
                                LEFT JOIN driver d ON b.driver_id = d.driver_id 
                                WHERE b.customer_id = '$customer_id'
                                ORDER BY b.booking_id DESC");
                            while($row = mysqli_fetch_assoc($result)) {
                                $statusClass = '';
                                if ($row['booking_status'] == 'Confirmed') $statusClass = 'badge-success';
                                elseif ($row['booking_status'] == 'Pending') $statusClass = 'badge-warning';
                                elseif ($row['booking_status'] == 'Completed') $statusClass = 'badge-info';
                                else $statusClass = 'badge-danger';
                                
                                $paymentClass = $row['payment_status'] == 'Paid' ? 'badge-success' : 'badge-warning';
                                
                                echo "<tr>
                                    <td>#{$row['booking_id']}</td>
                                    <td>{$row['vehicle_name']}</td>
                                    <td>{$row['driver_name']}</td>
                                    <td>{$row['pickup_location']}</td>
                                    <td>{$row['dropoff_location']}</td>
                                    <td>{$row['start_date']}</td>
                                    <td>{$row['end_date']}</td>
                                    <td>{$row['total_days']}</td>
                                    <td>৳{$row['total_price']}</td>
                                    <td><span class='badge {$statusClass}'>{$row['booking_status']}</span></td>
                                    <td><span class='badge {$paymentClass}'>{$row['payment_status']}</span></td>
                                    <td>";
                                
                                if($row['booking_status'] == 'Pending') {
                                    echo "<a href='bookings.php?cancel={$row['booking_id']}&id={$row['booking_id']}' class='btn btn-danger btn-sm' onclick='return confirm(\"Are you sure you want to cancel this booking?\")'>Cancel</a>";
                                }
                                if($row['payment_status'] == 'Pending' && $row['booking_status'] != 'Cancelled') {
                                    echo "<a href='payment.php?booking_id={$row['booking_id']}' class='btn btn-success btn-sm'><i class='fas fa-credit-card'></i> Pay Now</a>";
                                }
                                if($row['payment_status'] == 'Paid') {
                                    echo "<a href='payment.php?booking_id={$row['booking_id']}' class='btn btn-info btn-sm'><i class='fas fa-print'></i> Receipt</a>";
                                }
                                if($row['booking_status'] == 'Completed') {
                                    echo "<a href='add_review.php?booking_id={$row['booking_id']}' class='btn btn-success btn-sm'><i class='fas fa-star'></i> Add Review</a>";
                                }
                                
                                echo "</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>