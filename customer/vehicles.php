<?php
session_start();
if (!isset($_SESSION['customer_id'])) {
    header("Location: ../auth/login.php");
    exit();
}
require_once __DIR__ . '/../config/database.php';

$search = "";
$filter_type = "";
if (isset($_GET['search'])) {
    $search = mysqli_real_escape_string($conn, $_GET['search']);
}
if (isset($_GET['type'])) {
    $filter_type = mysqli_real_escape_string($conn, $_GET['type']);
}

$sql = "SELECT v.*, 
        (SELECT AVG(rating) FROM reviews WHERE target_type = 'vehicle' AND target_id = v.vehicle_id AND status = 'approved') as avg_rating,
        (SELECT COUNT(*) FROM reviews WHERE target_type = 'vehicle' AND target_id = v.vehicle_id AND status = 'approved') as review_count
        FROM vehicle v WHERE availability = 'Available' AND approval_status = 'Approved'";
if ($search != "") {
    $sql .= " AND (vehicle_name LIKE '%$search%' OR brand LIKE '%$search%' OR model LIKE '%$search%' OR location LIKE '%$search%')";
}
if ($filter_type != "") {
    $sql .= " AND vehicle_type = '$filter_type'";
}
$sql .= " ORDER BY vehicle_id DESC";

$result = mysqli_query($conn, $sql);

$active_page = 'Browse Vehicles';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Browse Vehicles - RideRent Pro</title>
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
            <h1><i class="fas fa-car"></i> Browse Vehicles</h1>
            <p>Find and rent the perfect vehicle for your needs</p>
        </div>

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Search & Filter</h3>
            </div>
            <div class="card-body">
                <form method="GET" action="vehicles.php" style="display: grid; grid-template-columns: 1fr 1fr auto auto; gap: 15px; align-items: end;">
                    <div class="form-group" style="margin: 0;">
                        <input type="text" name="search" class="form-control" placeholder="Search vehicles..." value="<?php echo htmlspecialchars($search); ?>">
                    </div>
                    <div class="form-group" style="margin: 0;">
                        <select name="type" class="form-select">
                            <option value="">All Types</option>
                            <option value="Sedan" <?php echo $filter_type == 'Sedan' ? 'selected' : ''; ?>>Sedan</option>
                            <option value="SUV" <?php echo $filter_type == 'SUV' ? 'selected' : ''; ?>>SUV</option>
                            <option value="Microbus" <?php echo $filter_type == 'Microbus' ? 'selected' : ''; ?>>Microbus</option>
                            <option value="Van" <?php echo $filter_type == 'Van' ? 'selected' : ''; ?>>Van</option>
                            <option value="Bike" <?php echo $filter_type == 'Bike' ? 'selected' : ''; ?>>Bike</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Search</button>
                    <a href="vehicles.php" class="btn btn-secondary"><i class="fas fa-redo"></i> Reset</a>
                </form>
            </div>
        </div>

        <div class="vehicle-grid">
            <?php
            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
            ?>
            <div class="vehicle-card">
                <?php if(!empty($row['image'])) { ?>
                    <img src="../assets/uploads/<?php echo $row['image']; ?>" alt="<?php echo $row['vehicle_name']; ?>">
                <?php } else { ?>
                    <img src="https://via.placeholder.com/300x200?text=No+Image" alt="<?php echo $row['vehicle_name']; ?>">
                <?php } ?>
                <div class="vehicle-info">
                    <h4><?php echo $row['vehicle_name']; ?></h4>
                    <p>
                        <strong><?php echo $row['brand']; ?> <?php echo $row['model']; ?></strong> (<?php echo $row['year']; ?>)<br>
                        <?php echo $row['vehicle_type']; ?> • <?php echo $row['seat_capacity']; ?> Seats • <?php echo $row['transmission']; ?><br>
                        <i class="fas fa-map-marker-alt"></i> <?php echo $row['location']; ?>
                    </p>
                    <?php if($row['avg_rating'] && $row['review_count'] > 0) { ?>
                        <div style="margin: 10px 0;">
                            <span style="color: #FD7E14;"><?php echo number_format($row['avg_rating'], 1); ?> &#9733;</span>
                            <small style="color: var(--medium-gray);">(<?php echo $row['review_count']; ?> reviews)</small>
                        </div>
                    <?php } ?>
                    <div style="display: flex; justify-content: space-between; align-items: center; margin: 15px 0;">
                        <span style="color: var(--accent-pink); font-size: 24px; font-weight: 700;">৳<?php echo $row['price_per_day']; ?>/day</span>
                        <span class="badge badge-success">Available</span>
                    </div>
                    <div style="display: flex; gap: 10px;">
                        <a href="book_vehicle.php?id=<?php echo $row['vehicle_id']; ?>" class="btn btn-primary" style="flex: 1;">Book Now</a>
                        <a href="compare.php?add=<?php echo $row['vehicle_id']; ?>" class="btn btn-secondary"><i class="fas fa-balance-scale"></i></a>
                    </div>
                </div>
            </div>
            <?php
                }
            } else {
                echo "<div class='no-data'><h4>No vehicles found matching your criteria.</h4></div>";
            }
            ?>
        </div>
    </div>
</div>

</body>
</html>