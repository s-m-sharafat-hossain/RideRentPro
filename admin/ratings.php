<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: ../auth/login.php");
    exit();
}
require_once __DIR__ . '/../config/database.php';

// Get ratings statistics
$avg_driver_rating = 0;
$driver_result = mysqli_query($conn, "SELECT AVG(rating) as avg_rating FROM driver");
if($driver_result) {
    $row = mysqli_fetch_assoc($driver_result);
    $avg_driver_rating = $row['avg_rating'] ? round($row['avg_rating'], 2) : 0;
}

$total_drivers = 0;
$driver_count = mysqli_query($conn, "SELECT COUNT(*) as count FROM driver");
if($driver_count) {
    $row = mysqli_fetch_assoc($driver_count);
    $total_drivers = $row['count'];
}

$avg_vehicle_rating = 0;
$vehicle_result = mysqli_query($conn, "SELECT AVG(rating) as avg_rating FROM vehicle");
if($vehicle_result) {
    $row = mysqli_fetch_assoc($vehicle_result);
    $avg_vehicle_rating = $row['avg_rating'] ? round($row['avg_rating'], 2) : 0;
}

$total_vehicles = 0;
$vehicle_count = mysqli_query($conn, "SELECT COUNT(*) as count FROM vehicle");
if($vehicle_count) {
    $row = mysqli_fetch_assoc($vehicle_count);
    $total_vehicles = $row['count'];
}

$active_page = 'Ratings';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ratings Overview - RideRent Pro</title>
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
            <h1><i class="fas fa-star-half-alt"></i> Ratings Overview</h1>
            <p>Driver and vehicle ratings statistics</p>
        </div>

        <!-- Stats Grid -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon" style="background: linear-gradient(135deg, #2F80ED, #1A5BB5);">
                    <i class="fas fa-id-card"></i>
                </div>
                <div class="stat-content">
                    <h3>Total Drivers</h3>
                    <p><?php echo $total_drivers; ?></p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background: linear-gradient(135deg, #FD7E14, #E67E22);">
                    <i class="fas fa-star"></i>
                </div>
                <div class="stat-content">
                    <h3>Avg Driver Rating</h3>
                    <p><?php echo $avg_driver_rating; ?> ⭐</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background: linear-gradient(135deg, #00C9A7, #009B80);">
                    <i class="fas fa-car"></i>
                </div>
                <div class="stat-content">
                    <h3>Total Vehicles</h3>
                    <p><?php echo $total_vehicles; ?></p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background: linear-gradient(135deg, #E84393, #D63031);">
                    <i class="fas fa-star"></i>
                </div>
                <div class="stat-content">
                    <h3>Avg Vehicle Rating</h3>
                    <p><?php echo $avg_vehicle_rating; ?> ⭐</p>
                </div>
            </div>
        </div>

        <!-- Driver Ratings Table -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-id-card"></i> Driver Ratings</h3>
            </div>
            <div class="card-body">
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Experience</th>
                                <th>Rating</th>
                                <th>Rating Count</th>
                                <th>Availability</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $result = mysqli_query($conn, "SELECT * FROM driver ORDER BY rating DESC");
                            if (!$result) {
                                echo "<tr><td colspan='5' style='text-align: center; padding: 30px;'>Error loading drivers: " . mysqli_error($conn) . "</td></tr>";
                            } else {
                                while($row = mysqli_fetch_assoc($result)) {
                                    $availClass = $row['availability'] == 'Available' ? 'badge-success' : 'badge-warning';
                                    echo "<tr>
                                        <td>{$row['driver_id']}</td>
                                        <td>{$row['full_name']}</td>
                                        <td>{$row['email']}</td>
                                        <td>{$row['experience_years']} years</td>
                                        <td><span style='color: #FD7E14;'>{$row['rating']} &#9733;</span></td>
                                        <td>{$row['rating_count']}</td>
                                        <td><span class='badge {$availClass}'>{$row['availability']}</span></td>
                                    </tr>";
                                }
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Vehicle Ratings Table -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-car"></i> Vehicle Ratings</h3>
            </div>
            <div class="card-body">
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Vehicle Name</th>
                                <th>Brand</th>
                                <th>Model</th>
                                <th>Type</th>
                                <th>Rating</th>
                                <th>Rating Count</th>
                                <th>Availability</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $result = mysqli_query($conn, "SELECT * FROM vehicle ORDER BY rating DESC");
                            if (!$result) {
                                echo "<tr><td colspan='7' style='text-align: center; padding: 30px;'>Error loading vehicles: " . mysqli_error($conn) . "</td></tr>";
                            } else {
                                while($row = mysqli_fetch_assoc($result)) {
                                    $availClass = $row['availability'] == 'Available' ? 'badge-success' : 'badge-warning';
                                    $statusClass = $row['approval_status'] == 'Approved' ? 'badge-success' : 'badge-warning';
                                    echo "<tr>
                                        <td>{$row['vehicle_id']}</td>
                                        <td>{$row['vehicle_name']}</td>
                                        <td>{$row['brand']}</td>
                                        <td>{$row['model']}</td>
                                        <td>{$row['vehicle_type']}</td>
                                        <td><span style='color: #FD7E14;'>{$row['rating']} &#9733;</span></td>
                                        <td>{$row['rating_count']}</td>
                                        <td><span class='badge {$availClass}'>{$row['availability']}</span></td>
                                        <td><span class='badge {$statusClass}'>{$row['approval_status']}</span></td>
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