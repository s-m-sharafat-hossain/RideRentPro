<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: ../auth/login.php");
    exit();
}
require_once __DIR__ . '/../config/database.php';

// Handle driver assignment
if(isset($_POST['assign_driver'])) {
    $booking_id = intval($_POST['booking_id']);
    $driver_id = intval($_POST['driver_id']);
    
    // Get booking details
    $booking_query = mysqli_query($conn, "SELECT * FROM booking WHERE booking_id = '$booking_id'");
    $booking = $booking_query ? mysqli_fetch_assoc($booking_query) : null;
    
    // Calculate driver fee
    $start = new DateTime($booking['start_date']);
    $end = new DateTime($booking['end_date']);
    $days = $start->diff($end)->days + 1;
    $driver_fee = 500 * $days;
    
    // Update booking with driver
    $new_total = $booking['total_price'] + $driver_fee;
    $update = "UPDATE booking SET driver_id = '$driver_id', driver_fee = '$driver_fee', total_price = '$new_total', booking_status = 'Confirmed' WHERE booking_id = '$booking_id'";
    
    if(mysqli_query($conn, $update)) {
        // Update driver availability
        mysqli_query($conn, "UPDATE driver SET availability = 'Unavailable' WHERE driver_id = '$driver_id'");
        
        header("Location: driver_assignment.php?msg=assigned");
        exit();
    } else {
        $error = "Assignment failed: " . mysqli_error($conn);
    }
}

// Handle driver removal
if(isset($_GET['remove_driver']) && isset($_GET['booking_id'])) {
    $booking_id = intval($_GET['booking_id']);
    
    // Get current driver
    $booking_query = mysqli_query($conn, "SELECT driver_id, driver_fee FROM booking WHERE booking_id = '$booking_id'");
    $booking = $booking_query ? mysqli_fetch_assoc($booking_query) : null;
    
    if($booking && $booking['driver_id']) {
        // Update booking
        $new_total = $booking['total_price'] - $booking['driver_fee'];
        $update = "UPDATE booking SET driver_id = NULL, driver_fee = 0, total_price = '$new_total', booking_status = 'Driver_Requested' WHERE booking_id = '$booking_id'";
        
        if(mysqli_query($conn, $update)) {
            // Update driver availability
            mysqli_query($conn, "UPDATE driver SET availability = 'Available' WHERE driver_id = '{$booking['driver_id']}'");
            
            header("Location: driver_assignment.php?msg=removed");
            exit();
        }
    }
}

$active_page = 'Driver Assignment';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Driver Assignment - RideRent Pro</title>
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
            <h1><i class="fas fa-user-plus"></i> Driver Assignment</h1>
            <p>Assign drivers to bookings that require them</p>
        </div>

        <?php if(isset($_GET['msg'])): ?>
            <div class="alert alert-success">
                <?php 
                if($_GET['msg'] == 'assigned') echo "Driver assigned successfully!";
                if($_GET['msg'] == 'removed') echo "Driver removed successfully!";
                ?>
            </div>
        <?php endif; ?>

        <?php if(isset($error)): ?>
            <div class="alert alert-danger">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <!-- Bookings Needing Drivers -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Bookings Requiring Drivers</h3>
            </div>
            <div class="card-body">
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Booking ID</th>
                                <th>Customer</th>
                                <th>Vehicle</th>
                                <th>Dates</th>
                                <th>Location</th>
                                <th>Current Driver</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $sql = "SELECT b.*, c.full_name as customer_name, v.vehicle_name, v.vehicle_type 
                                    FROM booking b 
                                    LEFT JOIN customer c ON b.customer_id = c.customer_id 
                                    LEFT JOIN vehicle v ON b.vehicle_id = v.vehicle_id 
                                    WHERE b.booking_status = 'Driver_Requested' OR (b.driver_id IS NULL AND b.booking_status = 'Confirmed')
                                    ORDER BY b.booking_date DESC";
                            $result = mysqli_query($conn, $sql);
                            
                            if (mysqli_num_rows($result) > 0) {
                                while ($row = mysqli_fetch_assoc($result)) {
                                    $driver_name = "Not Assigned";
                                    if($row['driver_id']) {
                                        $driver_query = mysqli_query($conn, "SELECT full_name FROM driver WHERE driver_id = '{$row['driver_id']}'");
                                        $driver = $driver_query ? mysqli_fetch_assoc($driver_query) : null;
                                        if($driver) $driver_name = $driver['full_name'];
                                    }
                            ?>
                                    <tr>
                                        <td>#<?php echo $row['booking_id']; ?></td>
                                        <td><?php echo htmlspecialchars($row['customer_name']); ?></td>
                                        <td><?php echo htmlspecialchars($row['vehicle_name']); ?> (<?php echo htmlspecialchars($row['vehicle_type']); ?>)</td>
                                        <td><?php echo $row['start_date']; ?> to <?php echo $row['end_date']; ?></td>
                                        <td><?php echo htmlspecialchars($row['pickup_location']); ?></td>
                                        <td><?php echo $driver_name; ?></td>
                                        <td>
                                            <button onclick="showAssignModal(<?php echo $row['booking_id']; ?>)" class="btn btn-primary btn-sm"><i class="fas fa-user-plus"></i> Assign Driver</button>
                                        </td>
                                    </tr>
                            <?php
                                }
                            } else {
                                echo "<tr><td colspan='7' style='text-align: center; padding: 30px;'>No bookings requiring driver assignment.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Active Driver Assignments -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Active Driver Assignments</h3>
            </div>
            <div class="card-body">
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Booking ID</th>
                                <th>Customer</th>
                                <th>Vehicle</th>
                                <th>Driver</th>
                                <th>Dates</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $sql = "SELECT b.*, c.full_name as customer_name, v.vehicle_name, d.full_name as driver_name 
                                    FROM booking b 
                                    LEFT JOIN customer c ON b.customer_id = c.customer_id 
                                    LEFT JOIN vehicle v ON b.vehicle_id = v.vehicle_id 
                                    LEFT JOIN driver d ON b.driver_id = d.driver_id 
                                    WHERE b.driver_id IS NOT NULL AND b.booking_status IN ('Confirmed', 'Driver_Requested')
                                    ORDER BY b.booking_date DESC";
                            $result = mysqli_query($conn, $sql);
                            
                            if (mysqli_num_rows($result) > 0) {
                                while ($row = mysqli_fetch_assoc($result)) {
                            ?>
                                    <tr>
                                        <td>#<?php echo $row['booking_id']; ?></td>
                                        <td><?php echo htmlspecialchars($row['customer_name']); ?></td>
                                        <td><?php echo htmlspecialchars($row['vehicle_name']); ?></td>
                                        <td><?php echo htmlspecialchars($row['driver_name']); ?></td>
                                        <td><?php echo $row['start_date']; ?> to <?php echo $row['end_date']; ?></td>
                                        <td><span class='badge badge-success'>Active</span></td>
                                        <td>
                                            <a href="driver_assignment.php?remove_driver=1&booking_id=<?php echo $row['booking_id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Remove driver from this booking?')"><i class="fas fa-user-minus"></i> Remove</a>
                                        </td>
                                    </tr>
                            <?php
                                }
                            } else {
                                echo "<tr><td colspan='7' style='text-align: center; padding: 30px;'>No active driver assignments.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Driver Assignment Modal -->
<div id="assignModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000;">
    <div style="position: relative; top: 50%; left: 50%; transform: translate(-50%, -50%); background: white; padding: 30px; border-radius: 10px; width: 400px; max-width: 90%;">
        <h3>Assign Driver</h3>
        <form method="POST">
            <input type="hidden" name="booking_id" id="modal_booking_id">
            <div class="form-group">
                <label>Select Available Driver</label>
                <select name="driver_id" class="form-select" required>
                    <?php
                    $driver_sql = "SELECT * FROM driver WHERE availability = 'Available' AND status = 'Active'";
                    $driver_result = mysqli_query($conn, $driver_sql);
                    if ($driver_result) {
                        while($driver = mysqli_fetch_assoc($driver_result)) {
                        echo "<option value='{$driver['driver_id']}'>{$driver['full_name']} ({$driver['phone']})</option>";
                        }
                    }
                    ?>
                </select>
            </div>
            <div style="display: flex; gap: 10px; margin-top: 20px;">
                <button type="submit" name="assign_driver" class="btn btn-primary" style="flex: 1;">Assign</button>
                <button type="button" onclick="hideAssignModal()" class="btn btn-secondary" style="flex: 1;">Cancel</button>
            </div>
        </form>
    </div>
</div>

<script>
function showAssignModal(bookingId) {
    document.getElementById('modal_booking_id').value = bookingId;
    document.getElementById('assignModal').style.display = 'block';
}

function hideAssignModal() {
    document.getElementById('assignModal').style.display = 'none';
}
</script>

</body>
</html>