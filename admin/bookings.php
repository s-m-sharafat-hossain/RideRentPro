<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: ../auth/login.php");
    exit();
}
require_once __DIR__ . '/../config/database.php';

// Handle Status Update
if(isset($_GET['status']) && isset($_GET['id'])) {
    $id = $_GET['id'];
    $status = $_GET['status'];
    mysqli_query($conn, "UPDATE booking SET booking_status = '$status' WHERE booking_id = '$id'");
    header("Location: bookings.php");
    exit();
}

// Handle Payment Status Update
if(isset($_GET['payment_status']) && isset($_GET['id'])) {
    $id = $_GET['id'];
    $payment_status = $_GET['payment_status'];
    mysqli_query($conn, "UPDATE booking SET payment_status = '$payment_status' WHERE booking_id = '$id'");
    header("Location: bookings.php");
    exit();
}

// Handle Delete
if(isset($_GET['delete'])) {
    $id = $_GET['delete'];
    mysqli_query($conn, "DELETE FROM booking WHERE booking_id = '$id'");
    header("Location: bookings.php");
    exit();
}

$active_page = 'Bookings';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bookings Management - RideRent Pro</title>
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
            <h1><i class="fas fa-calendar-check"></i> Bookings Management</h1>
            <p>Manage all vehicle bookings</p>
        </div>

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Bookings List</h3>
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
                                <th>Booking Status</th>
                                <th>Payment Status</th>
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
                                ORDER BY b.booking_id DESC");
                            if (!$result) {
                                echo "<tr><td colspan='11' style='text-align: center; padding: 30px;'>Error loading bookings: " . mysqli_error($conn) . "</td></tr>";
                            } else {
                                while($row = mysqli_fetch_assoc($result)) {
                                $statusClass = '';
                                if ($row['booking_status'] == 'Confirmed' || $row['booking_status'] == 'Driver_Requested') $statusClass = 'badge-success';
                                elseif ($row['booking_status'] == 'Pending') $statusClass = 'badge-warning';
                                elseif ($row['booking_status'] == 'Completed') $statusClass = 'badge-info';
                                elseif ($row['booking_status'] == 'Cancelled') $statusClass = 'badge-danger';
                                else $statusClass = 'badge-danger';

                                $displayStatus = $row['booking_status'];
                                if($row['booking_status'] == 'Driver_Requested') $displayStatus = 'Driver Needed';

                                $paymentClass = '';
                                if ($row['payment_status'] == 'Paid') $paymentClass = 'badge-success';
                                elseif ($row['payment_status'] == 'Pending') $paymentClass = 'badge-warning';
                                else $paymentClass = 'badge-secondary';

                                $driverDisplay = $row['driver_name'] ? $row['driver_name'] : '<span class="text-muted">Not Assigned</span>';

                                echo "<tr>
                                    <td>#{$row['booking_id']}</td>
                                    <td>{$row['customer_name']}</td>
                                    <td>{$row['vehicle_name']}</td>
                                    <td>{$driverDisplay}</td>
                                    <td>{$row['start_date']}</td>
                                    <td>{$row['end_date']}</td>
                                    <td>{$row['total_days']}</td>
                                    <td>৳{$row['total_price']}</td>
                                    <td><span class='badge {$statusClass}'>{$displayStatus}</span></td>
                                    <td><span class='badge {$paymentClass}'>{$row['payment_status']}</span></td>
                                    <td>" . ($row['payment_method'] ? htmlspecialchars($row['payment_method']) : '-') . "</td>
                                    <td>";
                                    if($row['booking_status'] == 'Driver_Requested') {
                                        echo "<a href='driver_assignment.php' class='btn btn-primary btn-sm'>Assign Driver</a> ";
                                    } elseif($row['booking_status'] == 'Confirmed') {
                                        echo "<a href='bookings.php?id={$row['booking_id']}&status=Completed' class='btn btn-info btn-sm'>Complete</a> ";
                                    } elseif($row['booking_status'] == 'Pending') {
                                        echo "<a href='bookings.php?id={$row['booking_id']}&status=Confirmed' class='btn btn-success btn-sm'>Confirm</a> ";
                                    }
                                    if($row['booking_status'] != 'Completed' && $row['booking_status'] != 'Cancelled') {
                                        echo "<a href='bookings.php?id={$row['booking_id']}&status=Cancelled' class='btn btn-danger btn-sm'>Cancel</a> ";
                                    }
                                    if($row['payment_status'] == 'Pending') {
                                        echo "<a href='bookings.php?id={$row['booking_id']}&payment_status=Paid' class='btn btn-success btn-sm'>Mark Paid</a> ";
                                    }
                                    echo "<a href='bookings.php?delete={$row['booking_id']}' class='btn btn-danger btn-sm' onclick='return confirm(\"Are you sure?\")'>Delete</a></td>
                                </tr>";
                            }
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