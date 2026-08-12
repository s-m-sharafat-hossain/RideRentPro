<?php
session_start();
if (!isset($_SESSION['owner_id'])) {
    header("Location: ../../auth/login.php");
    exit();
}
require_once __DIR__ . '/../../config/database.php';

$message = "";
$message_type = "";

// Get vehicle owner ID
$owner_id = $_SESSION['owner_id'];

if(isset($_POST['save_vehicle']))
{
    // ============================
    // Get Form Data
    // ============================
    $vehicle_name = trim($_POST['vehicle_name']);
    $brand = trim($_POST['brand']);
    $model = trim($_POST['model']);
    $year = $_POST['year'];
    $vehicle_type = $_POST['vehicle_type'];
    $fuel_type = $_POST['fuel_type'];
    $transmission = $_POST['transmission'];
    $seat_capacity = $_POST['seat_capacity'];
    $price_per_day = $_POST['price_per_day'];
    $location = trim($_POST['location']);
    $availability = $_POST['availability'];
    $description = trim($_POST['description']);

    // ============================
    // Image Upload
    // ============================
    $image_name = $_FILES['vehicle_image']['name'];
    $tmp_name = $_FILES['vehicle_image']['tmp_name'];
    $image_size = $_FILES['vehicle_image']['size'];

    $extension = strtolower(pathinfo($image_name, PATHINFO_EXTENSION));
    $allowed = array("jpg", "jpeg", "png");

    if(!in_array($extension, $allowed))
    {
        $message = "Only JPG, JPEG and PNG files are allowed.";
        $message_type = "danger";
    }
    elseif($image_size > 2097152)
    {
        $message = "Image size must be below 2MB.";
        $message_type = "danger";
    }
    else
    {
        $new_image = time()."_".$image_name;
        move_uploaded_file($tmp_name, "../../assets/uploads/".$new_image);

        // ============================
        // Insert Into Database
        // ============================
        $sql = "INSERT INTO vehicle (owner_id, vehicle_name, brand, model, year, vehicle_type, fuel_type, transmission, seat_capacity, price_per_day, location, availability, approval_status, image, description)
                VALUES ('$owner_id', '$vehicle_name', '$brand', '$model', '$year', '$vehicle_type', '$fuel_type', '$transmission', '$seat_capacity', '$price_per_day', '$location', '$availability', 'Pending', '$new_image', '$description')";

        if(mysqli_query($conn, $sql))
        {
            $message = "Vehicle Added Successfully!";
            $message_type = "success";
            header("Location: vehicle_list.php");
            exit();
        }
        else
        {
            $message = "Database Error: ".mysqli_error($conn);
            $message_type = "danger";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Vehicle - RideRent Pro</title>
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
                <li><a href="add_vehicle.php" class="active"><i class="fas fa-plus-circle"></i> Add Vehicle</a></li>
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
            <h1><i class="fas fa-plus-circle"></i> Add New Vehicle</h1>
            <p>Add a new vehicle to the system</p>
        </div>

        <div class="form-container" style="max-width: 800px; margin: 0 auto;">
            <?php if($message != "") { ?>
                <div class="alert alert-<?php echo $message_type; ?>">
                    <i class="fas fa-info-circle"></i>
                    <?php echo $message; ?>
                </div>
            <?php } ?>
            
            <form method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label class="form-label">Vehicle Name</label>
                    <input type="text" name="vehicle_name" class="form-control" required>
                </div>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div class="form-group">
                        <label class="form-label">Brand</label>
                        <input type="text" name="brand" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Model</label>
                        <input type="text" name="model" class="form-control" required>
                    </div>
                </div>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div class="form-group">
                        <label class="form-label">Year</label>
                        <input type="number" name="year" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Vehicle Type</label>
                        <select name="vehicle_type" class="form-select" required>
                            <option value="">Select Type</option>
                            <option>Sedan</option>
                            <option>SUV</option>
                            <option>Microbus</option>
                            <option>Van</option>
                            <option>Truck</option>
                            <option>Bike</option>
                        </select>
                    </div>
                </div>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div class="form-group">
                        <label class="form-label">Fuel Type</label>
                        <select name="fuel_type" class="form-select" required>
                            <option value="">Select Fuel</option>
                            <option>Petrol</option>
                            <option>Diesel</option>
                            <option>Hybrid</option>
                            <option>Electric</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Transmission</label>
                        <select name="transmission" class="form-select" required>
                            <option value="">Select</option>
                            <option>Automatic</option>
                            <option>Manual</option>
                        </select>
                    </div>
                </div>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div class="form-group">
                        <label class="form-label">Seat Capacity</label>
                        <input type="number" name="seat_capacity" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Price Per Day (৳)</label>
                        <input type="number" name="price_per_day" class="form-control" required>
                    </div>
                </div>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div class="form-group">
                        <label class="form-label">Location</label>
                        <input type="text" name="location" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Availability</label>
                        <select name="availability" class="form-select" required>
                            <option>Available</option>
                            <option>Booked</option>
                            <option>Maintenance</option>
                        </select>
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Vehicle Image</label>
                    <input type="file" name="vehicle_image" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control" rows="4"></textarea>
                </div>
                
                <div style="display: flex; gap: 15px; justify-content: center; margin-top: 30px;">
                    <button type="submit" name="save_vehicle" class="btn btn-primary"><i class="fas fa-save"></i> Save Vehicle</button>
                    <button type="reset" class="btn btn-secondary"><i class="fas fa-undo"></i> Reset</button>
                </div>
            </form>
        </div>
    </div>
</div>

</body>
</html>