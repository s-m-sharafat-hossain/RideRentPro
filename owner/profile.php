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

// Handle profile update
if(isset($_POST['update_profile'])) {
    $full_name = mysqli_real_escape_string($conn, $_POST['full_name']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    
    // Handle image upload
    if(!empty($_FILES['profile_image']['name'])) {
        $image_name = $_FILES['profile_image']['name'];
        $tmp_name = $_FILES['profile_image']['tmp_name'];
        $extension = strtolower(pathinfo($image_name, PATHINFO_EXTENSION));
        $allowed = array("jpg", "jpeg", "png");
        
        if(in_array($extension, $allowed)) {
            $new_image = time()."_".$image_name;
            move_uploaded_file($tmp_name, "../assets/uploads/".$new_image);
            mysqli_query($conn, "UPDATE $owner_table SET profile_image = '$new_image' WHERE owner_id = '$owner_id'");
        }
    }
    
    mysqli_query($conn, "UPDATE $owner_table SET full_name = '$full_name', phone = '$phone', address = '$address' WHERE owner_id = '$owner_id'");
    header("Location: profile.php");
    exit();
}

$ownerInfo = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM $owner_table WHERE owner_id = '$owner_id'"));

// Get owner statistics
$total_vehicles = 0;
$total_bookings = 0;
$total_earnings = 0;
$avg_vehicle_rating = 0;

$vehicles_query = mysqli_query($conn, "SELECT * FROM vehicle WHERE owner_id = '$owner_id'");
if($vehicles_query) {
    $total_vehicles = mysqli_num_rows($vehicles_query);
    $rating_sum = 0;
    $rating_count = 0;
    
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
        
        if($vehicle['rating'] > 0) {
            $rating_sum += $vehicle['rating'];
            $rating_count++;
        }
    }
    
    if($rating_count > 0) {
        $avg_vehicle_rating = $rating_sum / $rating_count;
    }
}

// Set active page for sidebar
$active_page = 'My Profile';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - RideRent Pro</title>
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
            <h1><i class="fas fa-user"></i> My Profile</h1>
            <p>View and update your profile information</p>
        </div>

        <div class="card">
            <div class="card-body" style="display: grid; grid-template-columns: 1fr 2fr; gap: 40px;">
                <div style="text-align: center;">
                    <?php if(!empty($ownerInfo['profile_image'])) { ?>
                        <img src="../assets/uploads/<?php echo $ownerInfo['profile_image']; ?>" style="width: 150px; height: 150px; border-radius: 50%; object-fit: cover; margin-bottom: 20px;">
                    <?php } else { ?>
                        <img src="https://via.placeholder.com/150x150?text=Owner" style="width: 150px; height: 150px; border-radius: 50%; object-fit: cover; margin-bottom: 20px;">
                    <?php } ?>
                    <h3><?php echo $ownerInfo['full_name']; ?></h3>
                    <p style="color: var(--medium-gray);"><?php echo $ownerInfo['email']; ?></p>
                    <p><span class="badge badge-success"><?php echo $ownerInfo['status']; ?></span></p>
                    
                    <div style="margin-top: 20px; padding: 15px; background: var(--off-white); border-radius: var(--radius-md);">
                        <h4 style="margin-bottom: 15px;">Your Statistics</h4>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                            <div style="text-align: center; padding: 10px; background: white; border-radius: var(--radius-sm);">
                                <i class="fas fa-car" style="color: #2F80ED; font-size: 20px;"></i>
                                <p style="margin: 5px 0 0 0; font-size: 14px;">Total Vehicles</p>
                                <strong style="font-size: 18px;"><?php echo $total_vehicles; ?></strong>
                            </div>
                            <div style="text-align: center; padding: 10px; background: white; border-radius: var(--radius-sm);">
                                <i class="fas fa-calendar-check" style="color: #00C9A7; font-size: 20px;"></i>
                                <p style="margin: 5px 0 0 0; font-size: 14px;">Total Bookings</p>
                                <strong style="font-size: 18px;"><?php echo $total_bookings; ?></strong>
                            </div>
                            <div style="text-align: center; padding: 10px; background: white; border-radius: var(--radius-sm);">
                                <i class="fas fa-dollar-sign" style="color: #6C5CE7; font-size: 20px;"></i>
                                <p style="margin: 5px 0 0 0; font-size: 14px;">Total Earnings</p>
                                <strong style="font-size: 18px;">৳<?php echo number_format($total_earnings); ?></strong>
                            </div>
                            <div style="text-align: center; padding: 10px; background: white; border-radius: var(--radius-sm);">
                                <i class="fas fa-star" style="color: #FD7E14; font-size: 20px;"></i>
                                <p style="margin: 5px 0 0 0; font-size: 14px;">Avg Vehicle Rating</p>
                                <strong style="font-size: 18px;"><?php echo number_format($avg_vehicle_rating, 1); ?> ⭐</strong>
                            </div>
                        </div>
                    </div>
                </div>
                <div>
                    <table style="width: 100%; margin-top: 20px;">
                        <tr>
                            <th style="width: 30%; padding: 12px; background: var(--off-white); color: var(--dark-gray); font-weight: 600;">Owner ID</th>
                            <td style="padding: 12px; color: var(--dark-gray);"><?php echo $ownerInfo['owner_id']; ?></td>
                        </tr>
                        <tr>
                            <th style="padding: 12px; background: var(--off-white); color: var(--dark-gray); font-weight: 600;">Username</th>
                            <td style="padding: 12px; color: var(--dark-gray);"><?php echo $ownerInfo['username']; ?></td>
                        </tr>
                        <tr>
                            <th style="padding: 12px; background: var(--off-white); color: var(--dark-gray); font-weight: 600;">Email</th>
                            <td style="padding: 12px; color: var(--dark-gray);"><?php echo $ownerInfo['email']; ?></td>
                        </tr>
                        <tr>
                            <th style="padding: 12px; background: var(--off-white); color: var(--dark-gray); font-weight: 600;">Phone</th>
                            <td style="padding: 12px; color: var(--dark-gray);"><?php echo $ownerInfo['phone']; ?></td>
                        </tr>
                        <tr>
                            <th style="padding: 12px; background: var(--off-white); color: var(--dark-gray); font-weight: 600;">Address</th>
                            <td style="padding: 12px; color: var(--dark-gray);"><?php echo $ownerInfo['address'] ? $ownerInfo['address'] : 'N/A'; ?></td>
                        </tr>
                        <tr>
                            <th style="padding: 12px; background: var(--off-white); color: var(--dark-gray); font-weight: 600;">Member Since</th>
                            <td style="padding: 12px; color: var(--dark-gray);"><?php echo date('F j, Y', strtotime($ownerInfo['created_at'])); ?></td>
                        </tr>
                    </table>
                    
                    <hr style="margin: 30px 0;">
                    
                    <h4>Update Profile</h4>
                    <form method="POST" enctype="multipart/form-data" style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                        <div class="form-group">
                            <label class="form-label">Full Name</label>
                            <input type="text" name="full_name" class="form-control" value="<?php echo $ownerInfo['full_name']; ?>" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Phone</label>
                            <input type="text" name="phone" class="form-control" value="<?php echo $ownerInfo['phone']; ?>" required>
                        </div>
                        <div class="form-group" style="grid-column: span 2;">
                            <label class="form-label">Address</label>
                            <input type="text" name="address" class="form-control" value="<?php echo $ownerInfo['address']; ?>">
                        </div>
                        <div class="form-group" style="grid-column: span 2;">
                            <label class="form-label">Profile Image</label>
                            <input type="file" name="profile_image" class="form-control" accept="image/*">
                        </div>
                        <div style="grid-column: span 2;">
                            <button type="submit" name="update_profile" class="btn btn-primary"><i class="fas fa-save"></i> Update Profile</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>