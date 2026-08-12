<?php
session_start();
if (!isset($_SESSION['driver_id'])) {
    header("Location: ../auth/login.php");
    exit();
}
require_once __DIR__ . '/../config/database.php';

$driver_id = $_SESSION['driver_id'];

// Set active page for sidebar
$active_page = 'Performance';
require_once __DIR__ . '/../includes/sidebar.php';

// Get driver info
$driver_info = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM driver WHERE driver_id = '$driver_id'"));

// Performance metrics
$total_bookings = 0;
$completed_bookings = 0;
$cancelled_bookings = 0;
$total_earnings = 0;
$total_distance = 0;
$total_hours = 0;

$stats_query = mysqli_query($conn, "SELECT * FROM booking WHERE driver_id = '$driver_id'");
if($stats_query) {
    while($row = mysqli_fetch_assoc($stats_query)) {
        $total_bookings++;
        if($row['booking_status'] == 'Completed') {
            $completed_bookings++;
            $total_earnings += $row['driver_fee'];
            $total_hours += $row['total_days'] * 8; // Assume 8 hours per day
        } elseif($row['booking_status'] == 'Cancelled') {
            $cancelled_bookings++;
        }
    }
}

// Calculate completion rate
$completion_rate = $total_bookings > 0 ? round(($completed_bookings / $total_bookings) * 100, 1) : 0;

// Get recent reviews
$reviews_query = mysqli_query($conn, "SELECT r.*, c.full_name AS customer_name FROM reviews r 
    LEFT JOIN customer c ON r.user_id = c.customer_id 
    WHERE r.target_type = 'driver' AND r.target_id = '$driver_id' AND r.status = 'approved'
    ORDER BY r.created_at DESC LIMIT 5");

// Use database rating for consistency
$driver_rating = $driver_info['rating'];
$driver_rating_count = $driver_info['rating_count'];

// Monthly earnings data
$monthly_earnings = [];
for($i = 5; $i >= 0; $i--) {
    $month = date('m', strtotime("-$i months"));
    $year = date('Y', strtotime("-$i months"));
    $month_name = date('M', strtotime("-$i months"));
    
    $earnings = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(driver_fee) as total FROM booking 
        WHERE driver_id = '$driver_id' AND payment_status = 'Paid' 
        AND MONTH(start_date) = '$month' AND YEAR(start_date) = '$year'"));
    
    $monthly_earnings[] = [
        'month' => $month_name,
        'earnings' => $earnings['total'] ? $earnings['total'] : 0
    ];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Performance - RideRent Pro</title>
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
            <h1><i class="fas fa-chart-line"></i> My Performance</h1>
            <p>Track your performance and earnings</p>
        </div>

        <!-- Performance Stats -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon" style="background: linear-gradient(135deg, #2F80ED, #1A5BB5);">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <div class="stat-content">
                    <h3>Total Bookings</h3>
                    <p><?php echo $total_bookings; ?></p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background: linear-gradient(135deg, #00C9A7, #009B80);">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-content">
                    <h3>Completed</h3>
                    <p><?php echo $completed_bookings; ?></p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background: linear-gradient(135deg, #E74C3C, #C0392B);">
                    <i class="fas fa-times-circle"></i>
                </div>
                <div class="stat-content">
                    <h3>Cancelled</h3>
                    <p><?php echo $cancelled_bookings; ?></p>
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
                    <i class="fas fa-percentage"></i>
                </div>
                <div class="stat-content">
                    <h3>Completion Rate</h3>
                    <p><?php echo $completion_rate; ?>%</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background: linear-gradient(135deg, #E84393, #D63031);">
                    <i class="fas fa-star"></i>
                </div>
                <div class="stat-content">
                    <h3>Rating</h3>
                    <p><?php echo number_format($driver_rating, 1); ?> ⭐ (<?php echo $driver_rating_count; ?>)</p>
                </div>
            </div>
        </div>

        <!-- Monthly Earnings Chart -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Monthly Earnings (Last 6 Months)</h3>
            </div>
            <div class="card-body">
                <div style="display: flex; align-items: flex-end; gap: 20px; height: 300px; padding: 20px;">
                    <?php
                    $max_earnings = max(array_column($monthly_earnings, 'earnings'));
                    if($max_earnings == 0) $max_earnings = 1;
                    
                    foreach($monthly_earnings as $data) {
                        $height = ($data['earnings'] / $max_earnings) * 250;
                        $color = $data['earnings'] > 0 ? 'var(--primary)' : 'var(--medium-gray)';
                    ?>
                    <div style="flex: 1; display: flex; flex-direction: column; align-items: center;">
                        <div style="width: 50px; height: <?php echo $height; ?>px; background: <?php echo $color; ?>; border-radius: 5px 5px 0 0; transition: height 0.3s;"></div>
                        <p style="margin-top: 10px; font-weight: 600;"><?php echo $data['month']; ?></p>
                        <p style="font-size: 14px; color: var(--medium-gray);">৳<?php echo number_format($data['earnings']); ?></p>
                    </div>
                    <?php } ?>
                </div>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px;">
            <!-- Recent Reviews -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Recent Reviews</h3>
                </div>
                <div class="card-body">
                    <?php
                    if($reviews_query && mysqli_num_rows($reviews_query) > 0) {
                        while($review = mysqli_fetch_assoc($reviews_query)) {
                            $stars = '';
                            for($i = 1; $i <= 5; $i++) {
                                $stars .= $i <= $review['rating'] ? '⭐' : '☆';
                            }
                    ?>
                    <div style="padding: 15px; border-bottom: 1px solid var(--border-color);">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                            <strong><?php echo $review['customer_name']; ?></strong>
                            <span><?php echo $stars; ?></span>
                        </div>
                        <p style="color: var(--medium-gray); font-size: 14px;"><?php echo $review['comment']; ?></p>
                        <small style="color: var(--light-gray);"><?php echo date('M d, Y', strtotime($review['created_at'])); ?></small>
                    </div>
                    <?php
                        }
                    } else {
                        echo "<p style='text-align: center; color: var(--medium-gray); padding: 20px;'>No reviews yet.</p>";
                    }
                    ?>
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
                                <strong>Maintain High Availability</strong>
                                <p style="color: var(--medium-gray); font-size: 14px;">Keep your availability status updated to receive more booking opportunities.</p>
                            </div>
                        </div>
                        <div style="display: flex; gap: 15px; align-items: start;">
                            <i class="fas fa-clock" style="color: #2F80ED; font-size: 20px; margin-top: 3px;"></i>
                            <div>
                                <strong>Be Punctual</strong>
                                <p style="color: var(--medium-gray); font-size: 14px;">Arrive on time for pickups to maintain good customer ratings.</p>
                            </div>
                        </div>
                        <div style="display: flex; gap: 15px; align-items: start;">
                            <i class="fas fa-car" style="color: #00C9A7; font-size: 20px; margin-top: 3px;"></i>
                            <div>
                                <strong>Keep Vehicle Clean</strong>
                                <p style="color: var(--medium-gray); font-size: 14px;">Maintain cleanliness of assigned vehicles for better customer experience.</p>
                            </div>
                        </div>
                        <div style="display: flex; gap: 15px; align-items: start;">
                            <i class="fas fa-smile" style="color: #E84393; font-size: 20px; margin-top: 3px;"></i>
                            <div>
                                <strong>Provide Excellent Service</strong>
                                <p style="color: var(--medium-gray); font-size: 14px;">Friendly behavior and professional attitude lead to better tips and ratings.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>