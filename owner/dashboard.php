<?php
session_start();
if (!isset($_SESSION['owner_id'])) {
    header("Location: ../auth/login.php");
    exit();
}
require_once __DIR__ . '/../config/database.php';

$owner_id = $_SESSION['owner_id'];
$owner_name = $_SESSION['owner_name'];

// Get owner's vehicles
$totalVehicles = 0;
$vehicleQuery = mysqli_query($conn, "SELECT * FROM vehicle WHERE owner_id = '$owner_id'");
if ($vehicleQuery) { $totalVehicles = mysqli_num_rows($vehicleQuery); }

// Get owner's bookings
$totalBookings = 0;
$bookingQuery = mysqli_query($conn, "SELECT b.* FROM booking b 
    INNER JOIN vehicle v ON b.vehicle_id = v.vehicle_id 
    WHERE v.owner_id = '$owner_id'");
if ($bookingQuery) { $totalBookings = mysqli_num_rows($bookingQuery); }

// Get total earnings
$totalEarnings = 0;
$earningsQuery = mysqli_query($conn, "SELECT SUM(total_price) as total FROM booking b 
    INNER JOIN vehicle v ON b.vehicle_id = v.vehicle_id 
    WHERE v.owner_id = '$owner_id' AND payment_status = 'Paid'");
if ($earningsQuery) {
    $row = mysqli_fetch_assoc($earningsQuery);
    $totalEarnings = $row['total'] ? $row['total'] : 0;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Owner Dashboard - RideRent Pro</title>
    <link rel="stylesheet" href="../assets/css/new-theme.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
<script src="../assets/js/theme.js"></script>

<!-- Dashboard Container -->
<div class="dashboard-container">
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-header">
            <h2><i class="fas fa-car-side"></i> RideRent Pro</h2>
        </div>
        <div class="sidebar-nav">
            <ul>
                <li><a href="dashboard.php" class="active"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                <li><a href="vehicles/vehicle_list.php"><i class="fas fa-car"></i> My Vehicles</a></li>
                <li><a href="vehicles/add_vehicle.php"><i class="fas fa-plus-circle"></i> Add Vehicle</a></li>
                <li><a href="bookings.php"><i class="fas fa-calendar-check"></i> Bookings</a></li>
                <li><a href="drivers.php"><i class="fas fa-id-card"></i> Drivers</a></li>
                <li><a href="../auth/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </div>
        <div class="sidebar-footer">
            <button class="theme-toggle" onclick="toggleTheme()" style="width: 100%; justify-content: center;">
                <i class="fas fa-moon"></i>
                <span>Dark Mode</span>
            </button>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="page-header">
            <h1><i class="fas fa-tachometer-alt"></i> Owner Dashboard</h1>
            <p>Welcome back, <?php echo $owner_name; ?>!</p>
        </div>

        <!-- Stats Grid -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon" style="background: linear-gradient(135deg, #2F80ED, #1A5BB5);">
                    <i class="fas fa-car"></i>
                </div>
                <div class="stat-content">
                    <h3>My Vehicles</h3>
                    <p><?php echo $totalVehicles; ?></p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background: linear-gradient(135deg, #00C9A7, #009B80);">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <div class="stat-content">
                    <h3>Total Bookings</h3>
                    <p><?php echo $totalBookings; ?></p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background: linear-gradient(135deg, #6C5CE7, #5B4CE6);">
                    <i class="fas fa-dollar-sign"></i>
                </div>
                <div class="stat-content">
                    <h3>Total Earnings</h3>
                    <p>$<?php echo number_format($totalEarnings, 2); ?></p>
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
                                <th>Customer</th>
                                <th>Vehicle</th>
                                <th>Total Price</th>
                                <th>Status</th>
                                <th>Payment</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $bookingQuery = mysqli_query($conn, "SELECT b.*, c.full_name AS customer_name, v.vehicle_name 
                                FROM booking b 
                                LEFT JOIN customer c ON b.customer_id = c.customer_id 
                                LEFT JOIN vehicle v ON b.vehicle_id = v.vehicle_id 
                                WHERE v.owner_id = '$owner_id'
                                ORDER BY b.booking_id DESC LIMIT 5");
                            if ($bookingQuery && mysqli_num_rows($bookingQuery) > 0) {
                                while ($row = mysqli_fetch_assoc($bookingQuery)) {
                                    $statusClass = '';
                                    if ($row['booking_status'] == 'Confirmed') $statusClass = 'badge-success';
                                    elseif ($row['booking_status'] == 'Pending') $statusClass = 'badge-warning';
                                    elseif ($row['booking_status'] == 'Completed') $statusClass = 'badge-info';
                                    else $statusClass = 'badge-danger';
                                    
                                    $paymentClass = $row['payment_status'] == 'Paid' ? 'badge-success' : 'badge-warning';
                                    
                                    echo "<tr><td>#{$row['booking_id']}</td><td>{$row['customer_name']}</td><td>{$row['vehicle_name']}</td>
                                          <td>৳{$row['total_price']}</td>
                                          <td><span class='badge {$statusClass}'>{$row['booking_status']}</span></td>
                                          <td><span class='badge {$paymentClass}'>{$row['payment_status']}</span></td></tr>";
                                }
                            } else {
                                echo "<tr><td colspan='6'>No recent bookings found.</td></tr>";
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