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

// Get all vehicles for this owner with ratings
$vehicles_query = mysqli_query($conn, "SELECT v.*, 
        (SELECT AVG(rating) FROM reviews WHERE target_type = 'vehicle' AND target_id = v.vehicle_id) as avg_rating,
        (SELECT COUNT(*) FROM reviews WHERE target_type = 'vehicle' AND target_id = v.vehicle_id) as review_count
        FROM vehicle v WHERE owner_id = '$owner_id'");

// Get overall statistics
$total_vehicles = 0;
$total_bookings = 0;
$total_earnings = 0;
$avg_rating = 0;

if($vehicles_query) {
    $total_vehicles = mysqli_num_rows($vehicles_query);
    
    while($vehicle = mysqli_fetch_assoc($vehicles_query)) {
        // Get bookings for this vehicle
        $bookings = mysqli_query($conn, "SELECT * FROM booking WHERE vehicle_id = '{$vehicle['vehicle_id']}'");
        if($bookings) {
            $total_bookings += mysqli_num_rows($bookings);
            
            while($booking = mysqli_fetch_assoc($bookings)) {
                if($booking['payment_status'] == 'Paid') {
                    $total_earnings += $booking['total_price'];
                }
            }
        }
        
        // Add to average rating
        $avg_rating += isset($vehicle['avg_rating']) ? $vehicle['avg_rating'] : 0;
    }
    
    if($total_vehicles > 0) {
        $avg_rating = $avg_rating / $total_vehicles;
    }
}

// Reset the query pointer for display
mysqli_data_seek($vehicles_query, 0);

// Set active page for sidebar
$active_page = 'Vehicle Performance';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vehicle Performance - RideRent Pro</title>
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
            <h1><i class="fas fa-chart-line"></i> Vehicle Performance</h1>
            <p>Track your vehicles' performance and earnings</p>
        </div>

        <!-- Overall Stats -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon" style="background: linear-gradient(135deg, #2F80ED, #1A5BB5);">
                    <i class="fas fa-car"></i>
                </div>
                <div class="stat-content">
                    <h3>Total Vehicles</h3>
                    <p><?php echo $total_vehicles; ?></p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background: linear-gradient(135deg, #00C9A7, #009B80);">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <div class="stat-content">
                    <h3>Total Bookings</h3>
                    <p><?php echo $total_bookings; ?></p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background: linear-gradient(135deg, #6C5CE7, #5B4CE6);">
                    <i class="fas fa-dollar-sign"></i>
                </div>
                <div class="stat-content">
                    <h3>Total Earnings</h3>
                    <p>৳<?php echo number_format($total_earnings, 2); ?></p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background: linear-gradient(135deg, #FD7E14, #E67E22);">
                    <i class="fas fa-star"></i>
                </div>
                <div class="stat-content">
                    <h3>Avg Rating</h3>
                    <p><?php echo number_format($avg_rating, 1); ?> &#9733;</p>
                </div>
            </div>
        </div>

        <!-- Individual Vehicle Performance -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Individual Vehicle Performance</h3>
            </div>
            <div class="card-body">
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Vehicle</th>
                                <th>Type</th>
                                <th>Bookings</th>
                                <th>Earnings</th>
                                <th>Rating</th>
                                <th>Availability</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if($vehicles_query && mysqli_num_rows($vehicles_query) > 0) {
                                while($vehicle = mysqli_fetch_assoc($vehicles_query)) {
                                    // Get vehicle-specific stats
                                    $vehicle_bookings = 0;
                                    $vehicle_earnings = 0;
                                    
                                    $bookings = mysqli_query($conn, "SELECT * FROM booking WHERE vehicle_id = '{$vehicle['vehicle_id']}'");
                                    if($bookings) {
                                        $vehicle_bookings = mysqli_num_rows($bookings);
                                        
                                        while($booking = mysqli_fetch_assoc($bookings)) {
                                            if($booking['payment_status'] == 'Paid') {
                                                $vehicle_earnings += $booking['total_price'];
                                            }
                                        }
                                    }
                                    
                                    $availClass = $vehicle['availability'] == 'Available' ? 'badge-success' : 'badge-warning';
                                    $statusClass = $vehicle['approval_status'] == 'Approved' ? 'badge-success' : 'badge-warning';
                                    
                                    echo "<tr>
                                        <td>
                                            <strong>{$vehicle['vehicle_name']}</strong><br>
                                            <small>{$vehicle['brand']} {$vehicle['model']}</small>
                                        </td>
                                        <td>{$vehicle['vehicle_type']}</td>
                                        <td>{$vehicle_bookings}</td>
                                        <td><strong>৳" . number_format($vehicle_earnings, 2) . "</strong></td>
                                        <td>
                                            <span style='color: #FD7E14;'>" . (isset($vehicle['avg_rating']) ? number_format($vehicle['avg_rating'], 1) : '0.0') . " &#9733;</span>
                                            <small>(" . (isset($vehicle['review_count']) ? $vehicle['review_count'] : '0') . " reviews)</small>
                                        </td>
                                        <td><span class='badge {$availClass}'>{$vehicle['availability']}</span></td>
                                        <td><span class='badge {$statusClass}'>{$vehicle['approval_status']}</span></td>
                                    </tr>";
                                }
                            } else {
                                echo "<tr><td colspan='7' style='text-align: center; padding: 30px;'>No vehicles found. <a href='vehicles/add_vehicle.php'>Add your first vehicle</a></td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Performance Tips -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Performance Tips</h3>
            </div>
            <div class="card-body">
                <div style="display: flex; flex-direction: column; gap: 15px;">
                    <div style="display: flex; gap: 15px; align-items: start;">
                        <i class="fas fa-lightbulb" style="color: #FD7E14; font-size: 20px; margin-top: 3px;"></i>
                        <div>
                            <strong>Maintain High Vehicle Quality</strong>
                            <p style="color: var(--medium-gray); font-size: 14px;">Regular maintenance and clean vehicles lead to better ratings and more bookings.</p>
                        </div>
                    </div>
                    <div style="display: flex; gap: 15px; align-items: start;">
                        <i class="fas fa-clock" style="color: #2F80ED; font-size: 20px; margin-top: 3px;"></i>
                        <div>
                            <strong>Competitive Pricing</strong>
                            <p style="color: var(--medium-gray); font-size: 14px;">Monitor market rates and adjust pricing to stay competitive while maintaining profitability.</p>
                        </div>
                    </div>
                    <div style="display: flex; gap: 15px; align-items: start;">
                        <i class="fas fa-star" style="color: #E84393; font-size: 20px; margin-top: 3px;"></i>
                        <div>
                            <strong>Encourage Reviews</strong>
                            <p style="color: var(--medium-gray); font-size: 14px;">Higher-rated vehicles get more bookings. Encourage satisfied customers to leave reviews.</p>
                        </div>
                    </div>
                    <div style="display: flex; gap: 15px; align-items: start;">
                        <i class="fas fa-calendar-check" style="color: #00C9A7; font-size: 20px; margin-top: 3px;"></i>
                        <div>
                            <strong>Optimize Availability</strong>
                            <p style="color: var(--medium-gray); font-size: 14px;">Keep vehicles available during peak periods to maximize booking opportunities.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>