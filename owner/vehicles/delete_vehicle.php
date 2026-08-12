<?php
session_start();
if (!isset($_SESSION['owner_id'])) {
    header("Location: ../../auth/login.php");
    exit();
}
require_once __DIR__ . '/../../config/database.php';

$owner_id = $_SESSION['owner_id'];

// Check ID
if(!isset($_GET['id'])){
    header("Location: vehicle_list.php");
    exit();
}

$id = $_GET['id'];

// Get Vehicle Image (only owner's vehicles)
$sql = "SELECT image FROM vehicle WHERE vehicle_id='$id' AND owner_id='$owner_id'";
$result = mysqli_query($conn,$sql);

if(mysqli_num_rows($result)==0){
    die("Vehicle not found or you don't have permission to delete this vehicle.");
}

$row = mysqli_fetch_assoc($result);

// Delete image from uploads folder
if(!empty($row['image'])){

    $imagePath = "../../assets/uploads/".$row['image'];

    if(file_exists($imagePath)){
        unlink($imagePath);
    }

}

// Delete Vehicle
$delete = "DELETE FROM vehicle WHERE vehicle_id='$id'";

if(mysqli_query($conn,$delete)){

    echo "<script>
            alert('Vehicle Deleted Successfully');
            window.location='vehicle_list.php';
          </script>";

}else{

    echo "<script>
            alert('Delete Failed');
            history.back();
          </script>";

}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Vehicle - RideRent Pro</title>
    <link rel="stylesheet" href="../../assets/css/new-theme.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

<!-- Dashboard Container -->
<div class="dashboard-container">
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-header">
            <h2><i class="fas fa-car"></i> RideRent Pro</h2>
        </div>
        <nav class="sidebar-nav">
            <ul>
                <li><a href="../dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                <li><a href="vehicle_list.php"><i class="fas fa-car"></i> My Vehicles</a></li>
                <li><a href="add_vehicle.php"><i class="fas fa-plus-circle"></i> Add Vehicle</a></li>
                <li><a href="../bookings.php"><i class="fas fa-calendar-check"></i> Bookings</a></li>
                <li><a href="../drivers.php"><i class="fas fa-id-card"></i> Drivers</a></li>
                <li><a href="../../auth/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </nav>
        <div class="sidebar-footer">
            <a href="../../auth/logout.php" class="btn btn-danger"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="page-header">
            <h1><i class="fas fa-trash"></i> Delete Vehicle</h1>
            <p>Vehicle deletion in progress...</p>
        </div>
    </div>
</div>

</body>
</html>
