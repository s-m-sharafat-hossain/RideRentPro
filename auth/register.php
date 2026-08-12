<?php
session_start();
require_once __DIR__ . '/../config/database.php';

$message = "";
$message_type = "";

// Check which owner table exists at the start
$owner_table = "owner"; // default
$check_owner = mysqli_query($conn, "SELECT 1 FROM owner LIMIT 1");
if(!$check_owner) {
    $check_vehicle_owner = mysqli_query($conn, "SELECT 1 FROM vehicle_owner LIMIT 1");
    if($check_vehicle_owner) {
        $owner_table = "vehicle_owner";
    }
}

if(isset($_POST['register'])) {
    $role = $_POST['role'];
    $full_name = mysqli_real_escape_string($conn, $_POST['full_name']);
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    
    // Check if email or username already exists
    $check_sql = "";
    $check_table = "";
    
    switch($role) {
        case 'customer':
            $check_table = "customer";
            break;
        case 'driver':
            $check_table = "driver";
            break;
        case 'owner':
            $check_table = $owner_table;
            break;
        default:
            $message = "Invalid role selected!";
            $message_type = "danger";
    }
    
    if ($check_table) {
        $check_sql = "SELECT * FROM $check_table WHERE email='$email' OR username='$username'";
        $check_result = mysqli_query($conn, $check_sql);

        if(!$check_result) {
            // If table doesn't exist, try alternative name for owner
            if($check_table == 'owner') {
                $check_table = 'vehicle_owner';
                $check_sql = "SELECT * FROM $check_table WHERE email='$email' OR username='$username'";
                $check_result = mysqli_query($conn, $check_sql);
            }
            if(!$check_result) {
                $message = "Database error: " . mysqli_error($conn);
                $message_type = "danger";
            }
        }

        if($check_result && mysqli_num_rows($check_result) > 0) {
            $message = "Email or username already exists!";
            $message_type = "danger";
        } elseif($check_result) {
            // Insert new user
            $insert_sql = "";
            $redirect = "";
            
            switch($role) {
                case 'customer':
                    $insert_sql = "INSERT INTO customer (full_name, username, email, password, phone_1) VALUES ('$full_name', '$username', '$email', '$password', '$phone')";
                    $redirect = "../customer/dashboard.php";
                    break;
                case 'driver':
                    $insert_sql = "INSERT INTO driver (full_name, username, email, password, phone) VALUES ('$full_name', '$username', '$email', '$password', '$phone')";
                    $redirect = "../driver/dashboard.php";
                    break;
                case 'owner':
                    $insert_sql = "INSERT INTO $owner_table (full_name, username, email, password, phone) VALUES ('$full_name', '$username', '$email', '$password', '$phone')";
                    $redirect = "../owner/dashboard.php";
                    break;
                case 'admin':
                    $insert_sql = "INSERT INTO admin (full_name, username, email, password, phone) VALUES ('$full_name', '$username', '$email', '$password', '$phone')";
                    $redirect = "../admin/dashboard.php";
                    break;
            }

            // Execute query for non-owner roles
            if(!isset($query_success)) {
                $query_success = mysqli_query($conn, $insert_sql);
            }

            if($query_success) {
                // Set session based on role
                if($role == 'customer') {
                    $user_id = mysqli_insert_id($conn);
                    $_SESSION['customer_id'] = $user_id;
                    $_SESSION['customer_name'] = $full_name;
                    $_SESSION['role'] = 'customer';
                } elseif($role == 'driver') {
                    $user_id = mysqli_insert_id($conn);
                    $_SESSION['driver_id'] = $user_id;
                    $_SESSION['driver_name'] = $full_name;
                    $_SESSION['role'] = 'driver';
                } elseif($role == 'owner') {
                    $user_id = mysqli_insert_id($conn);
                    $_SESSION['owner_id'] = $user_id;
                    $_SESSION['owner_name'] = $full_name;
                    $_SESSION['role'] = 'owner';
                } elseif($role == 'admin') {
                    $user_id = mysqli_insert_id($conn);
                    $_SESSION['admin'] = true;
                    $_SESSION['admin_name'] = $full_name;
                    $_SESSION['role'] = 'admin';
                }
                
                header("Location: $redirect");
                exit();
            } else {
                $message = "Registration failed: " . mysqli_error($conn);
                $message_type = "danger";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - RideRent Pro</title>
    <link rel="stylesheet" href="../assets/css/new-theme.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body style="background: var(--gradient-primary); min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 20px;">
<script src="../assets/js/theme.js"></script>

<div class="form-container" style="max-width: 600px;">
    <div style="text-align: center; margin-bottom: 30px;">
        <h1 style="font-size: 32px; margin-bottom: 10px;">
            <i class="fas fa-car-side" style="background: var(--gradient-primary); -webkit-background-clip: text; background-clip: text; -webkit-text-fill-color: transparent;"></i>
        </h1>
        <h2 class="form-title">Create Account</h2>
        <p class="form-subtitle">Join RideRent Pro today</p>
        <button class="theme-toggle" onclick="toggleTheme()" style="margin-top: 20px;">
            <i class="fas fa-moon"></i>
            <span>Dark Mode</span>
        </button>
    </div>

    <?php if($message): ?>
        <div class="alert alert-<?php echo $message_type; ?>">
            <i class="fas fa-<?php echo $message_type == 'success' ? 'check-circle' : 'exclamation-circle'; ?>"></i> 
            <?php echo $message; ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div class="form-group">
                <label class="form-label"><i class="fas fa-user"></i> Full Name</label>
                <input type="text" name="full_name" class="form-control" placeholder="Enter your full name" required>
            </div>

            <div class="form-group">
                <label class="form-label"><i class="fas fa-at"></i> Username</label>
                <input type="text" name="username" class="form-control" placeholder="Choose a username" required>
            </div>
        </div>

        <div class="form-group">
            <label class="form-label"><i class="fas fa-envelope"></i> Email Address</label>
            <input type="email" name="email" class="form-control" placeholder="Enter your email" required>
        </div>

        <div class="form-group">
            <label class="form-label"><i class="fas fa-phone"></i> Phone Number</label>
            <input type="tel" name="phone" class="form-control" placeholder="Enter your phone number" required>
        </div>

        <div class="form-group">
            <label class="form-label"><i class="fas fa-lock"></i> Password</label>
            <input type="password" name="password" class="form-control" placeholder="Create a password" required>
        </div>

        <div class="form-group">
            <label class="form-label"><i class="fas fa-user-tie"></i> Register As</label>
            <select name="role" class="form-select" required>
                <option value="">Select Role</option>
                <option value="customer">Customer</option>
                <option value="driver">Driver</option>
                <option value="owner">Vehicle Owner</option>
                <option value="admin">Admin</option>
            </select>
        </div>

        <button type="submit" name="register" class="btn btn-primary btn-block">
            <i class="fas fa-user-plus"></i> Register
        </button>
    </form>

    <div style="text-align: center; margin-top: 25px;">
        <p style="color: var(--medium-gray);">
            Already have an account? 
            <a href="login.php" style="color: var(--maya-blue); text-decoration: none; font-weight: 600;">Login here</a>
        </p>
    </div>
</div>

</body>
</html>