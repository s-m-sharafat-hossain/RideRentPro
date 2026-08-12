<?php
session_start();
if (!isset($_SESSION['owner_id'])) {
    header("Location: ../auth/login.php");
    exit();
}
require_once __DIR__ . '/../config/database.php';

// Check which owner table exists
$owner_table = "owner"; // default
$check_owner = mysqli_query($conn, "SELECT 1 FROM owner LIMIT 1");
if(!$check_owner) {
    $check_vehicle_owner = mysqli_query($conn, "SELECT 1 FROM vehicle_owner LIMIT 1");
    if($check_vehicle_owner) {
        $owner_table = "vehicle_owner";
    }
}

$owner_id = $_SESSION['owner_id'];

// Handle Status Update
if(isset($_GET['status']) && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $status = mysqli_real_escape_string($conn, $_GET['status']);
    $valid_statuses = ['Confirmed', 'Completed', 'Cancelled', 'Pending'];

    if(in_array($status, $valid_statuses)) {
        $update_query = "UPDATE booking b INNER JOIN vehicle v ON b.vehicle_id = v.vehicle_id
            SET b.booking_status = '$status' WHERE b.booking_id = '$id' AND v.owner_id = '$owner_id'";
        if(mysqli_query($conn, $update_query)) {
            header("Location: bookings.php?updated=1");
        } else {
            header("Location: bookings.php?error=update_failed");
        }
    } else {
        header("Location: bookings.php?error=invalid_status");
    }
    exit();
}

// Set active page for sidebar
$active_page = 'Bookings';
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
            <p>Manage bookings for your vehicles</p>
        </div>

        <?php if(isset($_GET['updated'])): ?>
            <div class="alert alert-success">
                Booking status updated successfully!
            </div>
        <?php endif; ?>

        <?php if(isset($_GET['error'])): ?>
            <div class="alert alert-danger">
                <?php
                if($_GET['error'] == 'update_failed') {
                    echo "Error updating booking status. Please try again.";
                } elseif($_GET['error'] == 'invalid_status') {
                    echo "Invalid status update.";
                } else {
                    echo "An error occurred. Please try again.";
                }
                ?>
            </div>
        <?php endif; ?>

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
                                <th>Vehicle</th>
                                <th>Driver</th>
                                <th>Start Date</th>
                                <th>End Date</th>
                                <th>Days</th>
                                <th>Total Price</th>
                                <th>Status</th>
                                <th>Payment</th>
                                <th>Payment Method</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $result = mysqli_query($conn, "SELECT b.*, c.full_name AS customer_name, v.vehicle_name, d.full_name AS driver_name
                                FROM booking b
                                LEFT JOIN customer c ON b.customer_id = c.customer_id
                                LEFT JOIN vehicle v ON b.vehicle_id = v.vehicle_id
                                LEFT JOIN driver d ON b.driver_id = d.driver_id
                                WHERE v.owner_id = '$owner_id'
                                ORDER BY b.booking_id DESC");
                            if($result && mysqli_num_rows($result) > 0) {
                                while($row = mysqli_fetch_assoc($result)) {
                                    $statusClass = '';
                                    if ($row['booking_status'] == 'Confirmed') $statusClass = 'badge-success';
                                    elseif ($row['booking_status'] == 'Pending') $statusClass = 'badge-warning';
                                    elseif ($row['booking_status'] == 'Completed') $statusClass = 'badge-info';
                                    else $statusClass = 'badge-danger';

                                    $paymentClass = $row['payment_status'] == 'Paid' ? 'badge-success' : 'badge-warning';

                                    echo "<tr>
                                        <td>#{$row['booking_id']}</td>
                                        <td>{$row['customer_name']}</td>
                                        <td>{$row['vehicle_name']}</td>
                                        <td>{$row['driver_name']}</td>
                                        <td>{$row['start_date']}</td>
                                        <td>{$row['end_date']}</td>
                                        <td>{$row['total_days']}</td>
                                        <td>৳{$row['total_price']}</td>
                                        <td><span class='badge {$statusClass}'>{$row['booking_status']}</span></td>
                                        <td><span class='badge {$paymentClass}'>{$row['payment_status']}</span></td>
                                        <td>" . ($row['payment_method'] ? htmlspecialchars($row['payment_method']) : '-') . "</td>
                                        <td>
                                            <button onclick=\"window.location.href='bookings.php?id={$row['booking_id']}&status=Confirmed'\" class='btn btn-success btn-sm'>Confirm</button>
                                            <button onclick=\"window.location.href='bookings.php?id={$row['booking_id']}&status=Completed'\" class='btn btn-info btn-sm'>Complete</button>
                                            <button onclick=\"window.location.href='bookings.php?id={$row['booking_id']}&status=Cancelled'\" class='btn btn-danger btn-sm'>Cancel</button>
                                        </td>
                                    </tr>";
                                }
                            } else {
                                echo "<tr><td colspan='12' style='text-align: center; padding: 30px;'>No bookings found for your vehicles.</td></tr>";
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