<?php
session_start();
if (!isset($_SESSION['owner_id'])) {
    header("Location: ../../auth/login.php");
    exit();
}
require_once __DIR__ . '/../../config/database.php';

$owner_id = $_SESSION['owner_id'];

$search = "";
if (isset($_GET['search'])) {
    $search = mysqli_real_escape_string($conn, $_GET['search']);
}

if ($search != "") {
    $sql = "SELECT v.*, 
            (SELECT AVG(rating) FROM reviews WHERE target_type = 'vehicle' AND target_id = v.vehicle_id) as avg_rating,
            (SELECT COUNT(*) FROM reviews WHERE target_type = 'vehicle' AND target_id = v.vehicle_id) as review_count
            FROM vehicle v WHERE owner_id = '$owner_id' AND (vehicle_name LIKE '%$search%'
            OR brand LIKE '%$search%'
            OR model LIKE '%$search%'
            OR location LIKE '%$search%')
            ORDER BY vehicle_id ASC";
} else {
    $sql = "SELECT v.*, 
            (SELECT AVG(rating) FROM reviews WHERE target_type = 'vehicle' AND target_id = v.vehicle_id) as avg_rating,
            (SELECT COUNT(*) FROM reviews WHERE target_type = 'vehicle' AND target_id = v.vehicle_id) as review_count
            FROM vehicle v WHERE owner_id = '$owner_id' ORDER BY vehicle_id ASC";
}

$result = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vehicle List - RideRent Pro</title>
    <link rel="stylesheet" href="../../assets/css/new-theme.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
<script src="../../assets/js/theme.js"></script>

<!-- Dashboard Container -->
<div class="dashboard-container">
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-header">
            <h2><i class="fas fa-car-side"></i> RideRent Pro</h2>
        </div>
        <div class="sidebar-nav">
            <ul>
                <li><a href="../dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                <li><a href="vehicle_list.php" class="active"><i class="fas fa-car"></i> My Vehicles</a></li>
                <li><a href="add_vehicle.php"><i class="fas fa-plus-circle"></i> Add Vehicle</a></li>
                <li><a href="../bookings.php"><i class="fas fa-calendar-check"></i> Bookings</a></li>
                <li><a href="../drivers.php"><i class="fas fa-id-card"></i> Drivers</a></li>
                <li><a href="../../auth/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </div>
        <div class="sidebar-footer">
            <button class="theme-toggle" onclick="toggleTheme()" style="width: 100%; justify-content: center;">
                <i class="fas fa-moon"></i>
                <span>Dark Mode</span>
            </button>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="page-header">
            <h1><i class="fas fa-car"></i> My Vehicles</h1>
            <p>Manage your vehicle fleet</p>
        </div>

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Search Vehicles</h3>
            </div>
            <div class="card-body">
                <form class="form-container" method="GET" action="vehicle_list.php" style="margin: 0; max-width: 100%; padding: 20px;">
                    <div class="form-group">
                        <input type="text" name="search" class="form-control" placeholder="Search by name, brand, model or location" value="<?php echo htmlspecialchars($search); ?>">
                    </div>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Search</button>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">All Vehicles</h3>
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
                                <th>Year</th>
                                <th>Type</th>
                                <th>Fuel</th>
                                <th>Transmission</th>
                                <th>Seats</th>
                                <th>Price/Day</th>
                                <th>Location</th>
                                <th>Rating</th>
                                <th>Availability</th>
                                <th>Approval Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (mysqli_num_rows($result) > 0) { ?>
                                <?php while ($row = mysqli_fetch_assoc($result)) {
                                    $status = $row['availability'];
                                    if ($status == "Available") {
                                        $badgeClass = 'badge-success';
                                    } elseif ($status == "Booked") {
                                        $badgeClass = 'badge-danger';
                                    } else {
                                        $badgeClass = 'badge-warning';
                                    }

                                    $approvalStatus = $row['approval_status'];
                                    if ($approvalStatus == "Approved") {
                                        $approvalBadgeClass = 'badge-success';
                                    } elseif ($approvalStatus == "Rejected") {
                                        $approvalBadgeClass = 'badge-danger';
                                    } else {
                                        $approvalBadgeClass = 'badge-warning';
                                    }
                                ?>
                                    <tr>
                                        <td><?php echo $row['vehicle_id']; ?></td>
                                        <td><?php echo htmlspecialchars($row['vehicle_name']); ?></td>
                                        <td><?php echo htmlspecialchars($row['brand']); ?></td>
                                        <td><?php echo htmlspecialchars($row['model']); ?></td>
                                        <td><?php echo $row['year']; ?></td>
                                        <td><?php echo htmlspecialchars($row['vehicle_type']); ?></td>
                                        <td><?php echo htmlspecialchars($row['fuel_type']); ?></td>
                                        <td><?php echo htmlspecialchars($row['transmission']); ?></td>
                                        <td><?php echo $row['seat_capacity']; ?></td>
                                        <td>৳<?php echo number_format($row['price_per_day'], 2); ?></td>
                                        <td><?php echo htmlspecialchars($row['location']); ?></td>
                                        <td>
                                            <span style='color: #FD7E14;'><?php echo $row['avg_rating'] ? number_format($row['avg_rating'], 1) : '0.0'; ?> &#9733;</span>
                                            <small>(<?php echo $row['review_count'] ? $row['review_count'] : '0'; ?>)</small>
                                        </td>
                                        <td><span class='badge <?php echo $badgeClass; ?>'><?php echo $status; ?></span></td>
                                        <td><span class='badge <?php echo $approvalBadgeClass; ?>'><?php echo $approvalStatus; ?></span></td>
                                        <td>
                                            <a class="btn btn-success btn-sm" href="edit_vehicle.php?id=<?php echo $row['vehicle_id']; ?>"><i class="fas fa-edit"></i> Edit</a>
                                            <a class="btn btn-danger btn-sm" href="delete_vehicle.php?id=<?php echo $row['vehicle_id']; ?>" onclick="return confirm('Are you sure you want to delete this vehicle?');"><i class="fas fa-trash"></i> Delete</a>
                                        </td>
                                    </tr>
                                <?php } ?>
                            <?php } else { ?>
                                <tr>
                                    <td colspan="14">No vehicle found.</td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>