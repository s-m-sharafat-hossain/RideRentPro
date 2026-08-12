<?php
session_start();
if (!isset($_SESSION['owner_id'])) {
    header("Location: ../../auth/login.php");
    exit();
}
require_once __DIR__ . '/../../config/database.php';

$owner_id = $_SESSION['owner_id'];

if (!isset($_GET['id'])) {
    header("Location: vehicle_list.php");
    exit();
}

$id = $_GET['id'];

$sql = "SELECT * FROM vehicle WHERE vehicle_id = ? AND owner_id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "ii", $id, $owner_id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) == 0) {
    echo "Vehicle not found or you don't have permission to view this vehicle!";
    exit();
}

$row = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vehicle Details - RideRent Pro</title>
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
                <li><a href="vehicle_list.php"><i class="fas fa-car"></i> My Vehicles</a></li>
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
            <h1><i class="fas fa-info-circle"></i> Vehicle Details</h1>
            <p>View vehicle information</p>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px;">
            <!-- Vehicle Image -->
            <div class="card">
                <div class="card-body" style="padding: 0;">
                    <?php if(!empty($row['image'])){ ?>
                        <img src="../../assets/uploads/<?php echo $row['image']; ?>" alt="<?php echo $row['vehicle_name']; ?>" style="width: 100%; height: 100%; object-fit: cover; border-radius: var(--radius-lg);">
                    <?php } else { ?>
                        <img src="https://via.placeholder.com/400x300?text=No+Image" alt="No Image" style="width: 100%; height: 100%; object-fit: cover; border-radius: var(--radius-lg);">
                    <?php } ?>
                </div>
            </div>

            <!-- Vehicle Details -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><?php echo $row['vehicle_name']; ?></h3>
                </div>
                <div class="card-body">
                    <table style="width: 100%;">
                        <tr><th style="padding: 10px; background: var(--off-white); font-weight: 600;">Brand</th><td style="padding: 10px;"><?php echo $row['brand']; ?></td></tr>
                        <tr><th style="padding: 10px; background: var(--off-white); font-weight: 600;">Model</th><td style="padding: 10px;"><?php echo $row['model']; ?></td></tr>
                        <tr><th style="padding: 10px; background: var(--off-white); font-weight: 600;">Year</th><td style="padding: 10px;"><?php echo $row['year']; ?></td></tr>
                        <tr><th style="padding: 10px; background: var(--off-white); font-weight: 600;">Vehicle Type</th><td style="padding: 10px;"><?php echo $row['vehicle_type']; ?></td></tr>
                        <tr><th style="padding: 10px; background: var(--off-white); font-weight: 600;">Fuel Type</th><td style="padding: 10px;"><?php echo $row['fuel_type']; ?></td></tr>
                        <tr><th style="padding: 10px; background: var(--off-white); font-weight: 600;">Transmission</th><td style="padding: 10px;"><?php echo $row['transmission']; ?></td></tr>
                        <tr><th style="padding: 10px; background: var(--off-white); font-weight: 600;">Seat Capacity</th><td style="padding: 10px;"><?php echo $row['seat_capacity']; ?></td></tr>
                        <tr><th style="padding: 10px; background: var(--off-white); font-weight: 600;">Location</th><td style="padding: 10px;"><?php echo $row['location']; ?></td></tr>
                        <tr><th style="padding: 10px; background: var(--off-white); font-weight: 600;">Price / Day</th><td style="padding: 10px;">৳<?php echo $row['price_per_day']; ?></td></tr>
                        <tr><th style="padding: 10px; background: var(--off-white); font-weight: 600;">Availability</th><td style="padding: 10px;">
                            <?php
                            if($row['availability']=="Available"){
                                echo "<span class='badge badge-success'>Available</span>";
                            }elseif($row['availability']=="Booked"){
                                echo "<span class='badge badge-danger'>Booked</span>";
                            }else{
                                echo "<span class='badge badge-warning'>Maintenance</span>";
                            }
                            ?>
                        </td></tr>
                    </table>
                    
                    <div style="margin-top: 20px;">
                        <strong>Description:</strong>
                        <p><?php echo nl2br($row['description']); ?></p>
                    </div>
                    
                    <div style="margin-top: 20px;">
                        <a href="edit_vehicle.php?id=<?php echo $row['vehicle_id']; ?>" class="btn btn-primary"><i class="fas fa-edit"></i> Edit Vehicle</a>
                        <a href="delete_vehicle.php?id=<?php echo $row['vehicle_id']; ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this vehicle?');"><i class="fas fa-trash"></i> Delete</a>
                        <a href="vehicle_list.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Back</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>