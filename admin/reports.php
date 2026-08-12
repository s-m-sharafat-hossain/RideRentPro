<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: ../auth/login.php");
    exit();
}
require_once __DIR__ . '/../config/database.php';

// Count Vehicles
$vehicleQuery = mysqli_query($conn, "SELECT * FROM vehicle");
$totalVehicles = mysqli_num_rows($vehicleQuery);

// Available Vehicles
$availableQuery = mysqli_query($conn, "SELECT * FROM vehicle WHERE availability='Available'");
$availableVehicles = mysqli_num_rows($availableQuery);

// Booked Vehicles
$bookedQuery = mysqli_query($conn, "SELECT * FROM vehicle WHERE availability='Booked'");
$bookedVehicles = mysqli_num_rows($bookedQuery);

// Maintenance
$maintenanceQuery = mysqli_query($conn, "SELECT * FROM vehicle WHERE availability='Maintenance'");
$maintenanceVehicles = mysqli_num_rows($maintenanceQuery);

$active_page = 'Reports';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vehicle Reports - RideRent Pro</title>
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
            <h1><i class="fas fa-chart-bar"></i> Vehicle Reports</h1>
            <p>Vehicle statistics and analytics</p>
        </div>

        <!-- Stats Grid -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon" style="background: linear-gradient(135deg, #2F80ED, #1A5BB5);">
                    <i class="fas fa-car"></i>
                </div>
                <div class="stat-content">
                    <h3>Total Vehicles</h3>
                    <p><?php echo $totalVehicles; ?></p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background: linear-gradient(135deg, #00C9A7, #009B80);">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-content">
                    <h3>Available</h3>
                    <p><?php echo $availableVehicles; ?></p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background: linear-gradient(135deg, #E84393, #D6336C);">
                    <i class="fas fa-calendar-times"></i>
                </div>
                <div class="stat-content">
                    <h3>Booked</h3>
                    <p><?php echo $bookedVehicles; ?></p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background: linear-gradient(135deg, #FD7E14, #E67E22);">
                    <i class="fas fa-tools"></i>
                </div>
                <div class="stat-content">
                    <h3>Maintenance</h3>
                    <p><?php echo $maintenanceVehicles; ?></p>
                </div>
            </div>
        </div>

        <!-- Latest Vehicles -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Latest Vehicles</h3>
            </div>
            <div class="card-body">
                <div class="vehicle-grid">
                    <?php
                    $list = mysqli_query($conn, "SELECT * FROM vehicle ORDER BY vehicle_id DESC LIMIT 6");
                    if ($list) {
                        while($row = mysqli_fetch_assoc($list)) {
                        $badgeClass = '';
                        if($row['availability'] == "Available") {
                            $badgeClass = 'badge-success';
                        } elseif($row['availability'] == "Booked") {
                            $badgeClass = 'badge-danger';
                        } else {
                            $badgeClass = 'badge-warning';
                        }
                    ?>
                    <div class="vehicle-card">
                        <img src="../assets/uploads/<?php echo $row['image']; ?>" alt="<?php echo $row['vehicle_name']; ?>">
                        <div class="vehicle-info">
                            <h4><?php echo $row['vehicle_name']; ?></h4>
                            <p><strong>Brand:</strong> <?php echo $row['brand']; ?></p>
                            <p><strong>Price:</strong> ৳<?php echo $row['price_per_day']; ?>/day</p>
                            <span class='badge <?php echo $badgeClass; ?>'><?php echo $row['availability']; ?></span>
                            <span class='badge badge-info'><?php echo $row['approval_status']; ?></span>
                        </div>
                    </div>
                    <?php }
                    } ?>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>