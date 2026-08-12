<?php
session_start();
if (!isset($_SESSION['driver_id'])) {
    header("Location: ../auth/login.php");
    exit();
}
require_once __DIR__ . '/../config/database.php';

$driver_id = $_SESSION['driver_id'];
$driver_name = $_SESSION['driver_name'];

// Get earnings data
$paidEarnings = 0;
$paidQuery = mysqli_query($conn, "SELECT SUM(driver_fee) as total, COUNT(*) as count FROM booking WHERE driver_id = '$driver_id' AND payment_status = 'Paid'");
if ($paidQuery) {
    $row = mysqli_fetch_assoc($paidQuery);
    $paidEarnings = $row['total'] ? $row['total'] : 0;
    $paidCount = $row['count'] ? $row['count'] : 0;
}

$pendingEarnings = 0;
$pendingQuery = mysqli_query($conn, "SELECT SUM(driver_fee) as total, COUNT(*) as count FROM booking WHERE driver_id = '$driver_id' AND payment_status = 'Pending'");
if ($pendingQuery) {
    $row = mysqli_fetch_assoc($pendingQuery);
    $pendingEarnings = $row['total'] ? $row['total'] : 0;
    $pendingCount = $row['count'] ? $row['count'] : 0;
}

$totalEarnings = $paidEarnings + $pendingEarnings;

// Set active page for sidebar
$active_page = 'My Earnings';
require_once __DIR__ . '/../includes/sidebar.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Earnings - RideRent Pro</title>
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
            <h1><i class="fas fa-dollar-sign"></i> My Earnings</h1>
            <p>Track your payment history and earnings</p>
        </div>

        <!-- Earnings Summary -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon" style="background: linear-gradient(135deg, #6C5CE7, #5B4CE6);">
                    <i class="fas fa-dollar-sign"></i>
                </div>
                <div class="stat-content">
                    <h3>Total Earnings</h3>
                    <p>৳<?php echo number_format($totalEarnings, 2); ?></p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background: linear-gradient(135deg, #00C9A7, #009B80);">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-content">
                    <h3>Paid Amount</h3>
                    <p>৳<?php echo number_format($paidEarnings, 2); ?></p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background: linear-gradient(135deg, #FD7E14, #E67E22);">
                    <i class="fas fa-hourglass-half"></i>
                </div>
                <div class="stat-content">
                    <h3>Pending Amount</h3>
                    <p>৳<?php echo number_format($pendingEarnings, 2); ?></p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background: linear-gradient(135deg, #2F80ED, #1A5BB5);">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <div class="stat-content">
                    <h3>Total Trips</h3>
                    <p><?php echo $paidCount + $pendingCount; ?></p>
                </div>
            </div>
        </div>

        <!-- Payment History -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Payment History</h3>
            </div>
            <div class="card-body">
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Booking ID</th>
                                <th>Customer</th>
                                <th>Vehicle</th>
                                <th>Date</th>
                                <th>Days</th>
                                <th>Driver Fee</th>
                                <th>Payment Status</th>
                                <th>Booking Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $result = mysqli_query($conn, "SELECT b.*, c.full_name AS customer_name, v.vehicle_name
                                FROM booking b
                                LEFT JOIN customer c ON b.customer_id = c.customer_id
                                LEFT JOIN vehicle v ON b.vehicle_id = v.vehicle_id
                                WHERE b.driver_id = '$driver_id'
                                ORDER BY b.booking_date DESC");
                            
                            if($result && mysqli_num_rows($result) > 0) {
                                while($row = mysqli_fetch_assoc($result)) {
                                    $paymentClass = '';
                                    if ($row['payment_status'] == 'Paid') $paymentClass = 'badge-success';
                                    elseif ($row['payment_status'] == 'Pending') $paymentClass = 'badge-warning';
                                    else $paymentClass = 'badge-secondary';
                                    
                                    $statusClass = '';
                                    if ($row['booking_status'] == 'Confirmed' || $row['booking_status'] == 'Driver_Requested') $statusClass = 'badge-success';
                                    elseif ($row['booking_status'] == 'Completed') $statusClass = 'badge-info';
                                    elseif ($row['booking_status'] == 'Cancelled') $statusClass = 'badge-danger';
                                    else $statusClass = 'badge-danger';
                                    
                                    echo "<tr>
                                        <td>#{$row['booking_id']}</td>
                                        <td>{$row['customer_name']}</td>
                                        <td>{$row['vehicle_name']}</td>
                                        <td>{$row['booking_date']}</td>
                                        <td>{$row['total_days']}</td>
                                        <td><strong>৳{$row['driver_fee']}</strong></td>
                                        <td><span class='badge {$paymentClass}'>{$row['payment_status']}</span></td>
                                        <td><span class='badge {$statusClass}'>{$row['booking_status']}</span></td>
                                    </tr>";
                                }
                            } else {
                                echo "<tr><td colspan='9' style='text-align: center; padding: 30px;'>No earnings history found.</td></tr>";
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