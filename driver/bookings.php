<?php
session_start();
if (!isset($_SESSION['driver_id'])) {
    header("Location: ../auth/login.php");
    exit();
}
require_once __DIR__ . '/../config/database.php';

$driver_id = $_SESSION['driver_id'];

// Handle Status Update
if(isset($_GET['status']) && isset($_GET['id'])) {
    $id = $_GET['id'];
    $status = $_GET['status'];

    // Update booking status
    mysqli_query($conn, "UPDATE booking SET booking_status = '$status' WHERE booking_id = '$id' AND driver_id = '$driver_id'");

    // If completing booking, update driver availability
    if($status == 'Completed') {
        mysqli_query($conn, "UPDATE driver SET availability = 'Available' WHERE driver_id = '$driver_id'");

        // Also update vehicle availability
        $booking = mysqli_fetch_assoc(mysqli_query($conn, "SELECT vehicle_id FROM booking WHERE booking_id = '$id'"));
        if($booking) {
            mysqli_query($conn, "UPDATE vehicle SET availability = 'Available' WHERE vehicle_id = '{$booking['vehicle_id']}'");
        }
    }

    header("Location: bookings.php");
    exit();
}

// Set active page for sidebar
$active_page = 'My Bookings';
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
            <p>Manage your assigned bookings</p>
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
                                <th>Customer</th>
                                <th>Contact</th>
                                <th>Vehicle</th>
                                <th>Pickup</th>
                                <th>Dates</th>
                                <th>Days</th>
                                <th>Driver Fee</th>
                                <th>Total</th>
                                <th>Payment</th>
                                <th>Payment Method</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $result = mysqli_query($conn, "SELECT b.*, c.full_name AS customer_name, c.phone_1 AS customer_phone, c.email AS customer_email, v.vehicle_name, v.vehicle_type, v.brand, v.model
                                FROM booking b
                                LEFT JOIN customer c ON b.customer_id = c.customer_id
                                LEFT JOIN vehicle v ON b.vehicle_id = v.vehicle_id
                                WHERE b.driver_id = '$driver_id'
                                ORDER BY b.booking_id DESC");

                            if($result && mysqli_num_rows($result) > 0) {
                                while($row = mysqli_fetch_assoc($result)) {
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

                                    echo "<tr>
                                        <td>#{$row['booking_id']}</td>
                                        <td><strong>{$row['customer_name']}</strong></td>
                                        <td>
                                            <small>📞 {$row['customer_phone']}</small><br>
                                            <small>✉️ {$row['customer_email']}</small>
                                        </td>
                                        <td>
                                            <strong>{$row['vehicle_name']}</strong><br>
                                            <small>{$row['brand']} {$row['model']}</small><br>
                                            <small>{$row['vehicle_type']}</small>
                                        </td>
                                        <td>{$row['pickup_location']}</td>
                                        <td>
                                            <small>Start: {$row['start_date']}</small><br>
                                            <small>End: {$row['end_date']}</small>
                                        </td>
                                        <td>{$row['total_days']}</td>
                                        <td><strong>৳{$row['driver_fee']}</strong></td>
                                        <td><strong>৳{$row['total_price']}</strong></td>
                                        <td><span class='badge {$paymentClass}'>{$row['payment_status']}</span></td>
                                        <td>" . ($row['payment_method'] ? htmlspecialchars($row['payment_method']) : '-') . "</td>
                                        <td><span class='badge {$statusClass}'>{$displayStatus}</span></td>
                                        <td>";
                                            $completeButton = '';
                                            if($row['booking_status'] == 'Confirmed' || $row['booking_status'] == 'Driver_Requested') {
                                                $completeButton = "<a href='bookings.php?id={$row['booking_id']}&status=Completed' class='btn btn-success btn-sm'><i class='fas fa-check'></i> Complete</a> ";
                                            }
                                        echo "<a href='booking_details.php?id={$row['booking_id']}' class='btn btn-info btn-sm'><i class='fas fa-eye'></i> Details</a> " . $completeButton . "</td>
                                    </tr>";
                                }
                            } else {
                                echo "<tr><td colspan='14' style='text-align: center; padding: 30px;'>No bookings assigned to you yet. When admin assigns you to bookings, they will appear here.</td></tr>";
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