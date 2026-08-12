<?php
session_start();
if (!isset($_SESSION['driver_id'])) {
    header("Location: ../auth/login.php");
    exit();
}
require_once __DIR__ . '/../config/database.php';

$driver_id = $_SESSION['driver_id'];
$driver_name = $_SESSION['driver_name'];

// Get driver's bookings
$totalBookings = 0;
$bookingQuery = mysqli_query($conn, "SELECT * FROM booking WHERE driver_id = '$driver_id'");
if ($bookingQuery) { $totalBookings = mysqli_num_rows($bookingQuery); }

// Get active bookings (not completed)
$activeBookings = 0;
$activeQuery = mysqli_query($conn, "SELECT * FROM booking WHERE driver_id = '$driver_id' AND booking_status IN ('Confirmed', 'Driver_Requested')");
if ($activeQuery) { $activeBookings = mysqli_num_rows($activeQuery); }

// Get completed bookings
$completedBookings = 0;
$completedQuery = mysqli_query($conn, "SELECT * FROM booking WHERE driver_id = '$driver_id' AND booking_status = 'Completed'");
if ($completedQuery) { $completedBookings = mysqli_num_rows($completedQuery); }

// Get total earnings (paid bookings)
$totalEarnings = 0;
$earningsQuery = mysqli_query($conn, "SELECT SUM(driver_fee) as total FROM booking WHERE driver_id = '$driver_id' AND payment_status = 'Paid'");
if ($earningsQuery) {
    $row = mysqli_fetch_assoc($earningsQuery);
    $totalEarnings = $row['total'] ? $row['total'] : 0;
}

// Get pending earnings (unpaid bookings)
$pendingEarnings = 0;
$pendingQuery = mysqli_query($conn, "SELECT SUM(driver_fee) as total FROM booking WHERE driver_id = '$driver_id' AND payment_status = 'Pending'");
if ($pendingQuery) {
    $row = mysqli_fetch_assoc($pendingQuery);
    $pendingEarnings = $row['total'] ? $row['total'] : 0;
}

// Get driver info
$driverInfo = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM driver WHERE driver_id = '$driver_id'"));

// Set active page for sidebar
$active_page = 'Dashboard';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Driver Dashboard - RideRent Pro</title>
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
            <h1><i class="fas fa-tachometer-alt"></i> Driver Dashboard</h1>
            <p>Welcome back, <?php echo $driver_name; ?>!</p>
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
                    <h3>Active</h3>
                    <p><?php echo $activeBookings; ?></p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background: linear-gradient(135deg, #00C9A7, #009B80);">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-content">
                    <h3>Completed</h3>
                    <p><?php echo $completedBookings; ?></p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background: linear-gradient(135deg, #6C5CE7, #5B4CE6);">
                    <i class="fas fa-dollar-sign"></i>
                </div>
                <div class="stat-content">
                    <h3>Paid Earnings</h3>
                    <p>৳<?php echo number_format($totalEarnings, 2); ?></p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background: linear-gradient(135deg, #FD7E14, #E67E22);">
                    <i class="fas fa-hourglass-half"></i>
                </div>
                <div class="stat-content">
                    <h3>Pending</h3>
                    <p>৳<?php echo number_format($pendingEarnings, 2); ?></p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background: linear-gradient(135deg, #E84393, #D63031);">
                    <i class="fas fa-star"></i>
                </div>
                <div class="stat-content">
                    <h3>My Rating</h3>
                    <p><?php echo $driverInfo['rating']; ?> ⭐</p>
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
                                <th>Start Date</th>
                                <th>End Date</th>
                                <th>Status</th>
                                <th>Payment</th>
                                <th>Fee</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $bookingQuery = mysqli_query($conn, "SELECT b.*, c.full_name AS customer_name, c.phone_1 AS customer_phone, v.vehicle_name, v.vehicle_type, v.brand
                                FROM booking b
                                LEFT JOIN customer c ON b.customer_id = c.customer_id
                                LEFT JOIN vehicle v ON b.vehicle_id = v.vehicle_id
                                WHERE b.driver_id = '$driver_id'
                                ORDER BY b.booking_id DESC LIMIT 5");
                            if ($bookingQuery && mysqli_num_rows($bookingQuery) > 0) {
                                while ($row = mysqli_fetch_assoc($bookingQuery)) {
                                    $statusClass = '';
                                    if ($row['booking_status'] == 'Confirmed' || $row['booking_status'] == 'Driver_Requested') $statusClass = 'badge-success';
                                    elseif ($row['booking_status'] == 'Pending') $statusClass = 'badge-warning';
                                    elseif ($row['booking_status'] == 'Completed') $statusClass = 'badge-info';
                                    elseif ($row['booking_status'] == 'Cancelled') $statusClass = 'badge-danger';
                                    else $statusClass = 'badge-danger';

                                    $displayStatus = $row['booking_status'];
                                    if($row['booking_status'] == 'Driver_Requested') $displayStatus = 'Assigned';

                                    $paymentClass = '';
                                    if ($row['payment_status'] == 'Paid') $paymentClass = 'badge-success';
                                    elseif ($row['payment_status'] == 'Pending') $paymentClass = 'badge-warning';
                                    else $paymentClass = 'badge-secondary';

                                    echo "<tr><td>#{$row['booking_id']}</td>
                                          <td>{$row['customer_name']}<br><small>📞 {$row['customer_phone']}</small></td>
                                          <td>{$row['vehicle_name']}<br><small>{$row['brand']} {$row['vehicle_type']}</small></td>
                                          <td>{$row['start_date']}</td>
                                          <td>{$row['end_date']}</td>
                                          <td><span class='badge {$statusClass}'>{$displayStatus}</span></td>
                                          <td><span class='badge {$paymentClass}'>{$row['payment_status']}</span></td>
                                          <td>৳{$row['driver_fee']}</td>
                                          <td><a href='booking_details.php?id={$row['booking_id']}' class='btn btn-info btn-sm'><i class='fas fa-eye'></i> View</a></td></tr>";
                                }
                            } else {
                                echo "<tr><td colspan='9'>No recent bookings found.</td></tr>";
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