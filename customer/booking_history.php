<?php
session_start();
if (!isset($_SESSION['customer_id'])) {
    header("Location: ../auth/login.php");
    exit();
}
require_once __DIR__ . '/../config/database.php';

$customer_id = $_SESSION['customer_id'];

// Handle filters
$status_filter = isset($_GET['status']) ? mysqli_real_escape_string($conn, $_GET['status']) : '';
$date_from = isset($_GET['date_from']) ? mysqli_real_escape_string($conn, $_GET['date_from']) : '';
$date_to = isset($_GET['date_to']) ? mysqli_real_escape_string($conn, $_GET['date_to']) : '';

// Build query
$sql = "SELECT b.*, v.vehicle_name, v.brand, v.model, d.full_name AS driver_name 
        FROM booking b 
        LEFT JOIN vehicle v ON b.vehicle_id = v.vehicle_id 
        LEFT JOIN driver d ON b.driver_id = d.driver_id 
        WHERE b.customer_id = '$customer_id'";

if($status_filter) {
    $sql .= " AND b.booking_status = '$status_filter'";
}

if($date_from) {
    $sql .= " AND b.start_date >= '$date_from'";
}

if($date_to) {
    $sql .= " AND b.end_date <= '$date_to'";
}

$sql .= " ORDER BY b.booking_id DESC";

$result = mysqli_query($conn, $sql);

// Export functionality
if(isset($_GET['export']) && $_GET['export'] == 'csv') {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="booking_history.csv"');
    
    $output = fopen('php://output', 'w');
    fputcsv($output, ['Booking ID', 'Vehicle', 'Driver', 'Start Date', 'End Date', 'Days', 'Total Price', 'Status', 'Payment Status', 'Payment Method', 'Transaction ID']);
    
    while($row = mysqli_fetch_assoc($result)) {
        fputcsv($output, [
            $row['booking_id'],
            $row['vehicle_name'],
            isset($row['driver_name']) && $row['driver_name'] ? $row['driver_name'] : 'No Driver',
            $row['start_date'],
            $row['end_date'],
            $row['total_days'],
            $row['total_price'],
            $row['booking_status'],
            $row['payment_status'],
            $row['payment_method'] ?? 'N/A',
            $row['transaction_id'] ?? 'N/A'
        ]);
    }
    
    fclose($output);
    exit();
}

$active_page = 'Booking History';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking History - RideRent Pro</title>
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
            <h1><i class="fas fa-history"></i> Booking History</h1>
            <p>View your complete rental history</p>
            <a href="booking_history.php?export=csv" class="btn btn-secondary btn-sm"><i class="fas fa-download"></i> Export CSV</a>
        </div>

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Filter Bookings</h3>
            </div>
            <div class="card-body">
                <form method="GET" action="booking_history.php" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; align-items: end;">
                    <div class="form-group" style="margin: 0;">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="">All Status</option>
                            <option value="Pending" <?php echo $status_filter == 'Pending' ? 'selected' : ''; ?>>Pending</option>
                            <option value="Confirmed" <?php echo $status_filter == 'Confirmed' ? 'selected' : ''; ?>>Confirmed</option>
                            <option value="Completed" <?php echo $status_filter == 'Completed' ? 'selected' : ''; ?>>Completed</option>
                            <option value="Cancelled" <?php echo $status_filter == 'Cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                        </select>
                    </div>
                    <div class="form-group" style="margin: 0;">
                        <label class="form-label">From Date</label>
                        <input type="date" name="date_from" class="form-control" value="<?php echo htmlspecialchars($date_from); ?>">
                    </div>
                    <div class="form-group" style="margin: 0;">
                        <label class="form-label">To Date</label>
                        <input type="date" name="date_to" class="form-control" value="<?php echo htmlspecialchars($date_to); ?>">
                    </div>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-filter"></i> Filter</button>
                    <a href="booking_history.php" class="btn btn-secondary"><i class="fas fa-redo"></i> Reset</a>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Vehicle</th>
                                <th>Driver</th>
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
                                        <td>
                                            <strong>{$row['vehicle_name']}</strong><br>
                                            <small>{$row['brand']} {$row['model']}</small>
                                        </td>
                                        <td>" . (isset($row['driver_name']) && $row['driver_name'] ? $row['driver_name'] : 'No Driver') . "</td>
                                        <td>{$row['start_date']}</td>
                                        <td>{$row['end_date']}</td>
                                        <td>{$row['total_days']}</td>
                                        <td><strong>৳{$row['total_price']}</strong></td>
                                        <td><span class='badge {$statusClass}'>{$row['booking_status']}</span></td>
                                        <td><span class='badge {$paymentClass}'>{$row['payment_status']}</span></td>
                                        <td>";
                                        if($row['payment_status'] == 'Pending' && $row['booking_status'] != 'Cancelled') {
                                            echo "<a href='payment.php?booking_id={$row['booking_id']}' class='btn btn-success btn-sm'><i class='fas fa-credit-card'></i> Pay</a>";
                                        }
                                        if($row['payment_status'] == 'Paid') {
                                            echo "<a href='payment.php?booking_id={$row['booking_id']}' class='btn btn-info btn-sm'><i class='fas fa-print'></i> Receipt</a>";
                                        }
                                    echo "</td></tr>";
                                }
                            } else {
                                echo "<tr><td colspan='10' style='text-align: center; padding: 30px;'>No booking history found.</td></tr>";
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