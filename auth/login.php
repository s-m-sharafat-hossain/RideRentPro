<?php
session_start();
require_once __DIR__ . '/../config/database.php';

$error = "";

// Check which owner table exists
$owner_table = "owner"; // default
$check_owner = mysqli_query($conn, "SELECT 1 FROM owner LIMIT 1");
if(!$check_owner) {
    $check_vehicle_owner = mysqli_query($conn, "SELECT 1 FROM vehicle_owner LIMIT 1");
    if($check_vehicle_owner) {
        $owner_table = "vehicle_owner";
    }
}

if(isset($_POST['email']) && isset($_POST['password']) && isset($_POST['role'])){
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $role = mysqli_real_escape_string($conn, $_POST['role']);

    // Admin Login
    if($role == 'Admin'){
        $sql = "SELECT * FROM admin WHERE email='$email' AND password='$password'";
        $result = mysqli_query($conn, $sql);

        if($result && mysqli_num_rows($result) > 0){
            $_SESSION['admin'] = $email;
            $_SESSION['role'] = 'admin';
            header("Location: ../admin/dashboard.php");
            exit();
        } else {
            if(!$result) {
                $error = "Database error: " . mysqli_error($conn);
            } else {
                $error = "Invalid Email or Password for Admin!";
            }
        }
    }
    // Vehicle Owner Login
    elseif($role == 'Vehicle Owner'){
        $sql = "SELECT * FROM $owner_table WHERE email='$email' AND password='$password'";
        $result = mysqli_query($conn, $sql);

        if($result && mysqli_num_rows($result) > 0){
            $row = mysqli_fetch_assoc($result);
            $_SESSION['owner_id'] = $row['owner_id'];
            $_SESSION['owner_name'] = $row['full_name'];
            $_SESSION['role'] = 'owner';
            header("Location: ../owner/dashboard.php");
            exit();
        } else {
            if(!$result) {
                $error = "Database error: " . mysqli_error($conn);
            } else {
                $error = "Invalid Email or Password for Vehicle Owner!";
            }
        }
    }
    // Driver Login
    elseif($role == 'Driver'){
        $sql = "SELECT * FROM driver WHERE email='$email' AND password='$password'";
        $result = mysqli_query($conn, $sql);

        if($result && mysqli_num_rows($result) > 0){
            $row = mysqli_fetch_assoc($result);
            $_SESSION['driver_id'] = $row['driver_id'];
            $_SESSION['driver_name'] = $row['full_name'];
            $_SESSION['role'] = 'driver';
            header("Location: ../driver/dashboard.php");
            exit();
        } else {
            if(!$result) {
                $error = "Database error: " . mysqli_error($conn);
            } else {
                $error = "Invalid Email or Password for Driver!";
            }
        }
    }
    // Customer Login
    elseif($role == 'Customer'){
        $sql = "SELECT * FROM customer WHERE email='$email' AND password='$password'";
        $result = mysqli_query($conn, $sql);

        if($result && mysqli_num_rows($result) > 0){
            $row = mysqli_fetch_assoc($result);
            $_SESSION['customer_id'] = $row['customer_id'];
            $_SESSION['customer_name'] = $row['full_name'];
            $_SESSION['role'] = 'customer';
            header("Location: ../customer/dashboard.php");
            exit();
        } else {
            if(!$result) {
                $error = "Database error: " . mysqli_error($conn);
            } else {
                $error = "Invalid Email or Password for Customer!";
            }
        }
    }
    else {
        $error = "Please select a valid role!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - RideRent Pro</title>
    <link rel="stylesheet" href="../assets/css/new-theme.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body style="background: var(--gradient-primary); min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 20px;">
<script src="../assets/js/theme.js"></script>

<div class="form-container" style="max-width: 500px;">
    <div style="text-align: center; margin-bottom: 30px;">
        <h1 style="font-size: 32px; margin-bottom: 10px;">
            <i class="fas fa-car-side" style="background: var(--gradient-primary); -webkit-background-clip: text; background-clip: text; -webkit-text-fill-color: transparent;"></i>
        </h1>
        <h2 class="form-title">Welcome Back</h2>
        <p class="form-subtitle">Sign in to your RideRent Pro account</p>
        <button class="theme-toggle" onclick="toggleTheme()" style="margin-top: 20px;">
            <i class="fas fa-moon"></i>
            <span>Dark Mode</span>
        </button>
    </div>

    <?php if($error): ?>
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="">
        <div class="form-group">
            <label class="form-label"><i class="fas fa-envelope"></i> Email Address</label>
            <input type="email" name="email" class="form-control" placeholder="Enter your email" required>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div class="form-group">
                <label class="form-label"><i class="fas fa-lock"></i> Password</label>
                <input type="password" name="password" class="form-control" placeholder="Enter your password" required>
            </div>

            <div class="form-group">
                <label class="form-label"><i class="fas fa-user-tie"></i> Login As</label>
                <select name="role" class="form-select" required>
                    <option value="">Select Role</option>
                    <option value="Admin">Admin</option>
                    <option value="Vehicle Owner">Vehicle Owner</option>
                    <option value="Driver">Driver</option>
                    <option value="Customer">Customer</option>
                </select>
            </div>
        </div>

        <button type="submit" class="btn btn-primary btn-block">
            <i class="fas fa-sign-in-alt"></i> Login
        </button>
    </form>

    <div style="text-align: center; margin-top: 25px;">
        <p style="color: var(--medium-gray); margin-bottom: 10px;">
            <a href="forgot_password.php" style="color: var(--primary); text-decoration: none;">Forgot Password?</a>
        </p>
        <p style="color: var(--medium-gray);">
            Don't have an account? 
            <a href="register.php" style="color: var(--maya-blue); text-decoration: none; font-weight: 600;">Register</a>
        </p>
    </div>
</div>

</body>
</html>