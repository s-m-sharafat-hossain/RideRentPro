<?php
session_start();
if (!isset($_SESSION['owner_id'])) {
    header("Location: ../../auth/login.php");
    exit();
}
require_once __DIR__ . '/../../config/database.php';

$owner_id = $_SESSION['owner_id'];

// Check Vehicle ID
if (!isset($_GET['id'])) {
    header("Location: vehicle_list.php");
    exit();
}

$id = $_GET['id'];

// Get Vehicle Data (only owner's vehicles)
$query = "SELECT * FROM vehicle WHERE vehicle_id='$id' AND owner_id='$owner_id'";
$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) == 0) {
    die("Vehicle not found or you don't have permission to edit this vehicle!");
}

$row = mysqli_fetch_assoc($result);

// Update Vehicle
if (isset($_POST['update'])) {

    $vehicle_name = mysqli_real_escape_string($conn, $_POST['vehicle_name']);
    $brand = mysqli_real_escape_string($conn, $_POST['brand']);
    $model = mysqli_real_escape_string($conn, $_POST['model']);
    $year = mysqli_real_escape_string($conn, $_POST['year']);
    $vehicle_type = mysqli_real_escape_string($conn, $_POST['vehicle_type']);
    $fuel_type = mysqli_real_escape_string($conn, $_POST['fuel_type']);
    $transmission = mysqli_real_escape_string($conn, $_POST['transmission']);
    $seat_capacity = mysqli_real_escape_string($conn, $_POST['seat_capacity']);
    $price = mysqli_real_escape_string($conn, $_POST['price_per_day']);
    $location = mysqli_real_escape_string($conn, $_POST['location']);
    $availability = mysqli_real_escape_string($conn, $_POST['availability']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);

    // Image Upload
    if ($_FILES['image']['name'] != "") {

        $image = time() . "_" . $_FILES['image']['name'];
        $tmp = $_FILES['image']['tmp_name'];

        move_uploaded_file($tmp, "../../assets/uploads/" . $image);

    } else {

        $image = $row['image'];
    }

    $update = "UPDATE vehicle SET

        vehicle_name='$vehicle_name',
        brand='$brand',
        model='$model',
        year='$year',
        vehicle_type='$vehicle_type',
        fuel_type='$fuel_type',
        transmission='$transmission',
        seat_capacity='$seat_capacity',
        price_per_day='$price',
        location='$location',
        availability='$availability',
        description='$description',
        image='$image'

        WHERE vehicle_id='$id'";

    if (mysqli_query($conn, $update)) {

        echo "<script>
        alert('Vehicle Updated Successfully!');
        window.location='vehicle_details.php?id=$id';
        </script>";

    } else {

        echo "<script>alert('Update Failed');</script>";

    }

}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Vehicle - RideRent Pro</title>
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
            <h1><i class="fas fa-edit"></i> Edit Vehicle</h1>
            <p>Edit vehicle details</p>
        </div>

        <div class="form-container" style="max-width: 800px; margin: 0;">
            <form method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label class="form-label">Vehicle Name</label>
                    <input type="text" name="vehicle_name" class="form-control" value="<?php echo $row['vehicle_name']; ?>" required>
                </div>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div class="form-group">
                        <label class="form-label">Brand</label>
                        <input type="text" name="brand" class="form-control" value="<?php echo $row['brand']; ?>" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Model</label>
                        <input type="text" name="model" class="form-control" value="<?php echo $row['model']; ?>" required>
                    </div>
                </div>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div class="form-group">
                        <label class="form-label">Year</label>
                        <input type="number" name="year" class="form-control" value="<?php echo $row['year']; ?>" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Vehicle Type</label>
                        <select name="vehicle_type" class="form-select" required>
                            <option value="">Select Type</option>
                            <option <?php echo $row['vehicle_type'] == 'Sedan' ? 'selected' : ''; ?>>Sedan</option>
                            <option <?php echo $row['vehicle_type'] == 'SUV' ? 'selected' : ''; ?>>SUV</option>
                            <option <?php echo $row['vehicle_type'] == 'Microbus' ? 'selected' : ''; ?>>Microbus</option>
                            <option <?php echo $row['vehicle_type'] == 'Van' ? 'selected' : ''; ?>>Van</option>
                            <option <?php echo $row['vehicle_type'] == 'Truck' ? 'selected' : ''; ?>>Truck</option>
                            <option <?php echo $row['vehicle_type'] == 'Bike' ? 'selected' : ''; ?>>Bike</option>
                        </select>
                    </div>
                </div>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div class="form-group">
                        <label class="form-label">Fuel Type</label>
                        <select name="fuel_type" class="form-select" required>
                            <option value="">Select Fuel</option>
                            <option <?php echo $row['fuel_type'] == 'Petrol' ? 'selected' : ''; ?>>Petrol</option>
                            <option <?php echo $row['fuel_type'] == 'Diesel' ? 'selected' : ''; ?>>Diesel</option>
                            <option <?php echo $row['fuel_type'] == 'Hybrid' ? 'selected' : ''; ?>>Hybrid</option>
                            <option <?php echo $row['fuel_type'] == 'Electric' ? 'selected' : ''; ?>>Electric</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Transmission</label>
                        <select name="transmission" class="form-select" required>
                            <option value="">Select</option>
                            <option <?php echo $row['transmission'] == 'Automatic' ? 'selected' : ''; ?>>Automatic</option>
                            <option <?php echo $row['transmission'] == 'Manual' ? 'selected' : ''; ?>>Manual</option>
                        </select>
                    </div>
                </div>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div class="form-group">
                        <label class="form-label">Seat Capacity</label>
                        <input type="number" name="seat_capacity" class="form-control" value="<?php echo $row['seat_capacity']; ?>" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Price Per Day (৳)</label>
                        <input type="number" name="price_per_day" class="form-control" value="<?php echo $row['price_per_day']; ?>" required>
                    </div>
                </div>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div class="form-group">
                        <label class="form-label">Location</label>
                        <input type="text" name="location" class="form-control" value="<?php echo $row['location']; ?>" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Availability</label>
                        <select name="availability" class="form-select" required>
                            <option <?php echo $row['availability'] == 'Available' ? 'selected' : ''; ?>>Available</option>
                            <option <?php echo $row['availability'] == 'Booked' ? 'selected' : ''; ?>>Booked</option>
                            <option <?php echo $row['availability'] == 'Maintenance' ? 'selected' : ''; ?>>Maintenance</option>
                        </select>
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Vehicle Image</label>
                    <input type="file" name="image" class="form-control">
                    <?php if(!empty($row['image'])) { ?>
                        <img src="../../assets/uploads/<?php echo $row['image']; ?>" alt="Current Image" style="max-width: 200px; margin-top: 10px; border-radius: var(--radius-md);">
                    <?php } ?>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control" rows="5"><?php echo $row['description']; ?></textarea>
                </div>
                
                <button type="submit" name="update" class="btn btn-primary"><i class="fas fa-save"></i> Update Vehicle</button>
                <a href="vehicle_list.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Back to List</a>
            </form>
        </div>
    </div>
</div>

</body>
</html>lass="text-center">

<button type="submit"
name="update"
class="btn btn-success">

Update Vehicle

</button>

<a href="vehicle_details.php?id=<?php echo $id; ?>"
class="btn btn-secondary">

Cancel

</a>

</div>

</div>

</form>

</div>

</div>

</div>
</div>

</body>

</html>
