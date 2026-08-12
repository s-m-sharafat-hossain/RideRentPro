<?php
session_start();
if (!isset($_SESSION['customer_id'])) {
    header("Location: ../auth/login.php");
    exit();
}
require_once __DIR__ . '/../config/database.php';

$customer_id = $_SESSION['customer_id'];

// Check if compare_vehicles table exists, if not create it
$check_table = mysqli_query($conn, "SHOW TABLES LIKE 'compare_vehicles'");
$has_table = $check_table && mysqli_num_rows($check_table) > 0;

if(!$has_table) {
    // Create the table if it doesn't exist
    $create_table = mysqli_query($conn, "CREATE TABLE compare_vehicles (
        id INT AUTO_INCREMENT PRIMARY KEY,
        customer_id INT NOT NULL,
        vehicle_id INT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    if(!$create_table) {
        // If table creation fails, we'll handle this gracefully
        $has_table = false;
    } else {
        $has_table = true;
    }
}

// Handle add to compare
if(isset($_GET['add']) && $has_table) {
    $vehicle_id = $_GET['add'];
    // Check if already in compare (max 3 vehicles)
    $count_query = mysqli_query($conn, "SELECT COUNT(*) as count FROM compare_vehicles WHERE customer_id = '$customer_id'");
    if($count_query) {
        $count = mysqli_fetch_assoc($count_query);
        if($count['count'] < 3) {
            $check_query = mysqli_query($conn, "SELECT * FROM compare_vehicles WHERE customer_id = '$customer_id' AND vehicle_id = '$vehicle_id'");
            if($check_query) {
                $check = mysqli_fetch_assoc($check_query);
                if(!$check) {
                    mysqli_query($conn, "INSERT INTO compare_vehicles (customer_id, vehicle_id) VALUES ('$customer_id', '$vehicle_id')");
                }
            }
        }
    }
    header("Location: compare.php");
    exit();
}

// Handle remove from compare
if(isset($_GET['remove']) && $has_table) {
    $vehicle_id = $_GET['remove'];
    mysqli_query($conn, "DELETE FROM compare_vehicles WHERE customer_id = '$customer_id' AND vehicle_id = '$vehicle_id'");
    header("Location: compare.php");
    exit();
}

// Get compare vehicles
$compare_query = null;
if($has_table) {
    $compare_query = mysqli_query($conn, "SELECT c.*, v.* FROM compare_vehicles c
        JOIN vehicle v ON c.vehicle_id = v.vehicle_id
        WHERE c.customer_id = '$customer_id'
        ORDER BY c.created_at DESC");
}

$active_page = 'Compare';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Compare Vehicles - RideRent Pro</title>
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
            <h1><i class="fas fa-balance-scale"></i> Compare Vehicles</h1>
            <p>Compare up to 3 vehicles side by side</p>
        </div>

        <?php
        $vehicles = [];
        if($compare_query && mysqli_num_rows($compare_query) > 0) {
            while($row = mysqli_fetch_assoc($compare_query)) {
                $vehicles[] = $row;
            }
        }
        
        if(empty($vehicles) && !$has_table) {
            echo "<div class='card'><div class='card-body'><div style='text-align: center; padding: 40px;'><h4>Compare feature is not available. Please contact administrator to set up the compare_vehicles table.</h4></div></div></div>";
        } elseif(empty($vehicles)) {
            echo "<div class='card'><div class='card-body'><div style='text-align: center; padding: 40px;'><h4>No vehicles to compare. <a href='vehicles.php'>Browse vehicles</a> and add them to compare!</h4></div></div></div>";
        } else {
        ?>
        <div class="card">
            <div class="card-body">
                <div style="display: grid; grid-template-columns: repeat(<?php echo count($vehicles); ?>, 1fr); gap: 20px; overflow-x: auto;">
                    <?php foreach($vehicles as $vehicle) { ?>
                    <div style="border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 20px; text-align: center;">
                        <?php if(!empty($vehicle['image'])) { ?>
                            <img src="../assets/uploads/<?php echo $vehicle['image']; ?>" alt="<?php echo $vehicle['vehicle_name']; ?>" style="width: 100%; height: 150px; object-fit: cover; border-radius: var(--radius-md); margin-bottom: 15px;">
                        <?php } else { ?>
                            <img src="https://via.placeholder.com/300x150?text=No+Image" alt="<?php echo $vehicle['vehicle_name']; ?>" style="width: 100%; height: 150px; object-fit: cover; border-radius: var(--radius-md); margin-bottom: 15px;">
                        <?php } ?>
                        
                        <h3><?php echo $vehicle['vehicle_name']; ?></h3>
                        <p style="color: var(--accent-pink); font-size: 24px; font-weight: 700; margin: 10px 0;">৳<?php echo $vehicle['price_per_day']; ?>/day</p>
                        
                        <table style="width: 100%; margin: 15px 0; text-align: left;">
                            <tr><td><strong>Brand:</strong></td><td><?php echo $vehicle['brand']; ?></td></tr>
                            <tr><td><strong>Model:</strong></td><td><?php echo $vehicle['model']; ?></td></tr>
                            <tr><td><strong>Year:</strong></td><td><?php echo $vehicle['year']; ?></td></tr>
                            <tr><td><strong>Type:</strong></td><td><?php echo $vehicle['vehicle_type']; ?></td></tr>
                            <tr><td><strong>Fuel:</strong></td><td><?php echo $vehicle['fuel_type']; ?></td></tr>
                            <tr><td><strong>Transmission:</strong></td><td><?php echo $vehicle['transmission']; ?></td></tr>
                            <tr><td><strong>Seats:</strong></td><td><?php echo $vehicle['seat_capacity']; ?></td></tr>
                            <tr><td><strong>Location:</strong></td><td><?php echo $vehicle['location']; ?></td></tr>
                        </table>
                        
                        <div style="display: flex; gap: 10px; flex-direction: column;">
                            <a href="book_vehicle.php?id=<?php echo $vehicle['vehicle_id']; ?>" class="btn btn-primary">Book Now</a>
                            <a href="compare.php?remove=<?php echo $vehicle['vehicle_id']; ?>" class="btn btn-danger" onclick="return confirm('Remove from comparison?')">Remove</a>
                        </div>
                    </div>
                    <?php } ?>
                </div>
            </div>
        <?php
        }
        ?>

    </div>
</div>

</body>
</html>