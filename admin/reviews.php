<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: ../auth/login.php");
    exit();
}
require_once __DIR__ . '/../config/database.php';

// Function to update driver rating
function updateDriverRating($conn, $driver_id) {
    // Get all reviews for this driver (including pending for immediate feedback)
    $reviews = mysqli_query($conn, "SELECT rating FROM reviews WHERE target_type = 'driver' AND target_id = '$driver_id'");
    
    if($reviews && mysqli_num_rows($reviews) > 0) {
        $total = 0;
        $count = 0;
        while($row = mysqli_fetch_assoc($reviews)) {
            $total += $row['rating'];
            $count++;
        }
        $avg_rating = $total / $count;
        
        // Update driver table
        mysqli_query($conn, "UPDATE driver SET rating = '$avg_rating', rating_count = '$count' WHERE driver_id = '$driver_id'");
    } else {
        // If no reviews, reset to default
        mysqli_query($conn, "UPDATE driver SET rating = 0.00, rating_count = 0 WHERE driver_id = '$driver_id'");
    }
}

// Function to update vehicle rating
function updateVehicleRating($conn, $vehicle_id) {
    // Get all reviews for this vehicle (including pending for immediate feedback)
    $reviews = mysqli_query($conn, "SELECT rating FROM reviews WHERE target_type = 'vehicle' AND target_id = '$vehicle_id'");
    
    if($reviews && mysqli_num_rows($reviews) > 0) {
        $total = 0;
        $count = 0;
        while($row = mysqli_fetch_assoc($reviews)) {
            $total += $row['rating'];
            $count++;
        }
        $avg_rating = $total / $count;
        
        // Add rating column to vehicle table if it doesn't exist
        $check_column = mysqli_query($conn, "SHOW COLUMNS FROM vehicle LIKE 'rating'");
        if(mysqli_num_rows($check_column) == 0) {
            mysqli_query($conn, "ALTER TABLE vehicle ADD COLUMN rating DECIMAL(3,2) DEFAULT 0.00");
            mysqli_query($conn, "ALTER TABLE vehicle ADD COLUMN rating_count INT DEFAULT 0");
        }
        
        // Update vehicle table
        mysqli_query($conn, "UPDATE vehicle SET rating = '$avg_rating', rating_count = '$count' WHERE vehicle_id = '$vehicle_id'");
    } else {
        // If no reviews, reset to default
        $check_column = mysqli_query($conn, "SHOW COLUMNS FROM vehicle LIKE 'rating'");
        if(mysqli_num_rows($check_column) > 0) {
            mysqli_query($conn, "UPDATE vehicle SET rating = 0.00, rating_count = 0 WHERE vehicle_id = '$vehicle_id'");
        }
    }
}

// Handle approval/rejection
if(isset($_GET['action']) && isset($_GET['id'])) {
    $id = $_GET['id'];
    $action = $_GET['action'];
    $status = ($action == 'approve') ? 'approved' : 'rejected';
    
    // Get review details before updating
    $review_result = mysqli_query($conn, "SELECT * FROM reviews WHERE review_id = '$id'");
    $review = $review_result ? mysqli_fetch_assoc($review_result) : null;
    
    mysqli_query($conn, "UPDATE reviews SET status = '$status' WHERE review_id = '$id'");
    
    // Update ratings if approving a review
    if($action == 'approve') {
        if($review['target_type'] == 'driver') {
            updateDriverRating($conn, $review['target_id']);
        }
        if($review['target_type'] == 'vehicle') {
            updateVehicleRating($conn, $review['target_id']);
        }
    }
    
    header("Location: reviews.php");
    exit();
}

// Handle delete
if(isset($_GET['delete'])) {
    $id = $_GET['delete'];
    
    // Get review details before deleting
    $review_result = mysqli_query($conn, "SELECT * FROM reviews WHERE review_id = '$id'");
    $review = $review_result ? mysqli_fetch_assoc($review_result) : null;
    
    mysqli_query($conn, "DELETE FROM reviews WHERE review_id = '$id'");
    
    // Update ratings after deletion
    if($review['target_type'] == 'driver') {
        updateDriverRating($conn, $review['target_id']);
    }
    if($review['target_type'] == 'vehicle') {
        updateVehicleRating($conn, $review['target_id']);
    }
    
    header("Location: reviews.php");
    exit();
}

// Fetch all reviews
$reviews_query = mysqli_query($conn, "SELECT r.*, c.full_name as customer_name FROM reviews r LEFT JOIN customer c ON r.user_id = c.customer_id ORDER BY r.created_at DESC");

$active_page = 'Reviews';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reviews Management - RideRent Pro</title>
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
            <h1><i class="fas fa-star"></i> Reviews Management</h1>
            <p>Manage customer reviews and feedback</p>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Customer</th>
                                <th>Target Type</th>
                                <th>Target ID</th>
                                <th>Rating</th>
                                <th>Comment</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if($reviews_query && mysqli_num_rows($reviews_query) > 0) {
                                while($row = mysqli_fetch_assoc($reviews_query)) {
                                    $statusClass = '';
                                    if($row['status'] == 'approved') $statusClass = 'badge-success';
                                    elseif($row['status'] == 'pending') $statusClass = 'badge-warning';
                                    else $statusClass = 'badge-danger';
                                    
                                    $stars = '';
                                    for($i = 1; $i <= 5; $i++) {
                                        $stars .= $i <= $row['rating'] ? '⭐' : '☆';
                                    }
                            ?>
                                <tr>
                                    <td><?php echo $row['review_id']; ?></td>
                                    <td><?php echo htmlspecialchars($row['customer_name']); ?></td>
                                    <td><?php echo ucfirst($row['target_type']); ?></td>
                                    <td><?php echo $row['target_id']; ?></td>
                                    <td><?php echo $stars; ?></td>
                                    <td><?php echo htmlspecialchars(substr($row['comment'], 0, 50)) . (strlen($row['comment']) > 50 ? '...' : ''); ?></td>
                                    <td><span class="badge <?php echo $statusClass; ?>"><?php echo ucfirst($row['status']); ?></span></td>
                                    <td><?php echo date('M d, Y', strtotime($row['created_at'])); ?></td>
                                    <td>
                                        <?php if($row['status'] == 'pending') { ?>
                                            <a href="reviews.php?action=approve&id=<?php echo $row['review_id']; ?>" class="btn btn-sm btn-success"><i class="fas fa-check"></i></a>
                                            <a href="reviews.php?action=reject&id=<?php echo $row['review_id']; ?>" class="btn btn-sm btn-danger"><i class="fas fa-times"></i></a>
                                        <?php } ?>
                                        <a href="reviews.php?delete=<?php echo $row['review_id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')"><i class="fas fa-trash"></i></a>
                                    </td>
                                </tr>
                            <?php
                                }
                            } else {
                            ?>
                                <tr>
                                    <td colspan="9" style="text-align: center;">No reviews found</td>
                                </tr>
                            <?php
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