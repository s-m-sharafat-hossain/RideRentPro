<?php
session_start();
require_once __DIR__ . '/../config/database.php';

$message = "";
$message_type = "";

if(isset($_POST['reset_password'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    
    // Check if email exists in any user table
    $check_admin = mysqli_query($conn, "SELECT * FROM admin WHERE email = '$email'");
    $check_customer = mysqli_query($conn, "SELECT * FROM customer WHERE email = '$email'");
    $check_driver = mysqli_query($conn, "SELECT * FROM driver WHERE email = '$email'");
    $check_owner = mysqli_query($conn, "SELECT * FROM vehicle_owner WHERE email = '$email'");
    
    $user_found = false;
    $user_type = '';
    $reset_link = '';
    
    if(mysqli_num_rows($check_admin) > 0) {
        $user_found = true;
        $user_type = 'admin';
    } elseif(mysqli_num_rows($check_customer) > 0) {
        $user_found = true;
        $user_type = 'customer';
    } elseif(mysqli_num_rows($check_driver) > 0  ) {
        $user_found = true;
        $user_type = 'driver';
    } elseif(mysqli_num_rows($check_owner) > 0) {
        $user_found = true;
        $user_type = 'owner';
    }
    
    if($user_found) {
        // Generate random password
        $new_password = substr(str_shuffle("abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%"), 0, 10);
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        
        // Update password in the appropriate table
        switch($user_type) {
            case 'admin':
                mysqli_query($conn, "UPDATE admin SET password = '$hashed_password' WHERE email = '$email'");
                break;
            case 'customer':
                mysqli_query($conn, "UPDATE customer SET password = '$hashed_password' WHERE email = '$email'");
                break;
            case 'driver':
                mysqli_query($conn, "UPDATE driver SET password = '$hashed_password' WHERE email = '$email'");
                break;
            case 'owner':
                mysqli_query($conn, "UPDATE vehicle_owner SET password = '$hashed_password' WHERE email = '$email'");
                break;
        }
        
        $message = "Password reset successful! Your new password is: <strong>$new_password</strong>";
        $message_type = "success";
    } else {
        $message = "Email not found in our system!";
        $message_type = "danger";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - RideRent Pro</title>
    <link rel="stylesheet" href="../assets/css/new-theme.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body style="background: var(--gradient-primary); min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 20px;">
<script src="../assets/js/theme.js"></script>

<div class="form-container" style="max-width: 500px;">
    <div style="text-align: center; margin-bottom: 30px;">
        <h1 style="font-size: 32px; margin-bottom: 10px;">
            <i class="fas fa-key" style="background: var(--gradient-primary); -webkit-background-clip: text; background-clip: text; -webkit-text-fill-color: transparent;"></i>
        </h1>
        <h2 class="form-title">Forgot Password</h2>
        <p class="form-subtitle">Reset your password</p>
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
        <div class="form-group">
            <label class="form-label"><i class="fas fa-envelope"></i> Email Address</label>
            <input type="email" name="email" class="form-control" placeholder="Enter your email" required>
        </div>

        <button type="submit" name="reset_password" class="btn btn-primary btn-block">
            <i class="fas fa-key"></i> Reset Password
        </button>
    </form>

    <div style="text-align: center; margin-top: 25px;">
        <p style="color: var(--medium-gray);">
            Remember your password? 
            <a href="login.php" style="color: var(--primary); text-decoration: none; font-weight:600;">Login here</a>
        </p>
    </div>
</div>

</body>
</html>