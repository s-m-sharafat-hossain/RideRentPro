<?php
session_start();
if (!isset($_SESSION['customer_id'])) {
    header("Location: ../auth/login.php");
    exit();
}
require_once __DIR__ . '/../config/database.php';

$customer_id = $_SESSION['customer_id'];
$customer_name = $_SESSION['customer_name'];

// Get customer's bookings
$totalBookings = 0;
$bookingQuery = mysqli_query($conn, "SELECT * FROM booking WHERE customer_id = '$customer_id'");
if ($bookingQuery) { $totalBookings = mysqli_num_rows($bookingQuery); }

// Get active bookings
$activeBookings = 0;
$activeQuery = mysqli_query($conn, "SELECT * FROM booking WHERE customer_id = '$customer_id' AND booking_status IN ('Pending', 'Confirmed')");
if ($activeQuery) { $activeBookings = mysqli_num_rows($activeQuery); }

// Get total spending
$totalSpending = 0;
$spendingQuery = mysqli_query($conn, "SELECT SUM(total_price) as total FROM booking WHERE customer_id = '$customer_id' AND payment_status = 'Paid'");
if ($spendingQuery) {
    $row = mysqli_fetch_assoc($spendingQuery);
    $totalSpending = $row['total'] ? $row['total'] : 0;
}

// Set active page for sidebar
$active_page = 'Dashboard';
require_once __DIR__ . '/../includes/sidebar.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Dashboard - RideRent Pro</title>
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
            <h1><i class="fas fa-tachometer-alt"></i> Customer Dashboard</h1>
            <p>Welcome back, <?php echo $customer_name; ?>!</p>
        </div>

        <!-- Stats Grid -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon" style="background: linear-gradient(135deg, #2F80ED, #1A5BB5);">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <div class="stat-content">
                    <h3>Total Bookings</h3>
                    <p><?php echo $totalBookings; ?></p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background: linear-gradient(135deg, #00C9A7, #009B80);">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stat-content">
                    <h3>Active Bookings</h3>
                    <p><?php echo $activeBookings; ?></p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background: linear-gradient(135deg, #6C5CE7, #5B4CE6);">
                    <i class="fas fa-dollar-sign"></i>
                </div>
                <div class="stat-content">
                    <h3>Total Spent</h3>
                    <p>$<?php echo number_format($totalSpending, 2); ?></p>
                </div>
            </div>
        </div>

        <!-- Recent Bookings Table -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-clock"></i> Recent Bookings</h3>
                <p class="card-subtitle">Latest 5 bookings</p>
            </div>
            <div class="card-body">
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>#Booking ID</th>
                                <th>Vehicle</th>
                                <th>Driver</th>
                                <th>Start Date</th>
                                <th>End Date</th>
                                <th>Total Price</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $bookingQuery = mysqli_query($conn, "SELECT b.*, v.vehicle_name, d.full_name AS driver_name 
                                FROM booking b 
                                LEFT JOIN vehicle v ON b.vehicle_id = v.vehicle_id 
                                LEFT JOIN driver d ON b.driver_id = d.driver_id 
                                WHERE b.customer_id = '$customer_id'
                                ORDER BY b.booking_id DESC LIMIT 5");
                            if ($bookingQuery && mysqli_num_rows($bookingQuery) > 0) {
                                while ($row = mysqli_fetch_assoc($bookingQuery)) {
                                    $statusClass = '';
                                    if ($row['booking_status'] == 'Confirmed') $statusClass = 'badge-success';
                                    elseif ($row['booking_status'] == 'Pending') $statusClass = 'badge-warning';
                                    elseif ($row['booking_status'] == 'Completed') $statusClass = 'badge-info';
                                    else $statusClass = 'badge-danger';
                                    
                                    echo "<tr><td>#{$row['booking_id']}</td><td>{$row['vehicle_name']}</td><td>{$row['driver_name']}</td>
                                          <td>{$row['start_date']}</td><td>{$row['end_date']}</td><td>৳{$row['total_price']}</td>
                                          <td><span class='badge {$statusClass}'>{$row['booking_status']}</span></td></tr>";
                                }
                            } else {
                                echo "<tr><td colspan='7'>No recent bookings found.</td></tr>";
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