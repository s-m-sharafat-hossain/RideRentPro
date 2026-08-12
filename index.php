<?php
session_start();
require_once __DIR__ . '/config/database.php';

// Check if user is logged in
$logged_in = false;
$user_role = '';
$user_name = '';
$dashboard_url = '';

if (isset($_SESSION['admin'])) {
    $logged_in = true;
    $user_role = 'Admin';
    $user_name = 'Admin';
    $dashboard_url = 'admin/dashboard.php';
} elseif (isset($_SESSION['owner_id'])) {
    $logged_in = true;
    $user_role = 'Vehicle Owner';
    $user_name = $_SESSION['owner_name'];
    $dashboard_url = 'owner/dashboard.php';
} elseif (isset($_SESSION['driver_id'])) {
    $logged_in = true;
    $user_role = 'Driver';
    $user_name = $_SESSION['driver_name'];
    $dashboard_url = 'driver/dashboard.php';
} elseif (isset($_SESSION['customer_id'])) {
    $logged_in = true;
    $user_role = 'Customer';
    $user_name = $_SESSION['customer_name'];
    $dashboard_url = 'customer/dashboard.php';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RideRent Pro - Vehicle Rental System</title>
    <link rel="stylesheet" href="assets/css/new-theme.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
<script src="assets/js/theme.js"></script>

<!-- Welcome Banner for Logged In Users -->
<?php if ($logged_in): ?>
<div class="welcome-banner" style="background: var(--gradient-primary); color: white; padding: 20px 50px; text-align: center;">
    <h2 style="margin-bottom: 10px;">Welcome back, <?php echo $user_name; ?>!</h2>
    <p style="margin-bottom: 15px;">You are logged in as <strong><?php echo $user_role; ?></strong></p>
    <a href="<?php echo $dashboard_url; ?>" class="btn btn-primary" style="background: white; color: var(--maya-blue);">Go to Dashboard</a>
</div>
<?php endif; ?>

<!-- Navigation -->
<nav class="navbar">
    <div class="navbar-brand">
        <i class="fas fa-car-side"></i> RideRent Pro
    </div>
    <ul class="navbar-menu">
        <li><a href="index.php"><i class="fas fa-home"></i> Home</a></li>
        <?php if ($logged_in): ?>
            <li><a href="<?php echo $dashboard_url; ?>"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
            <li><a href="auth/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        <?php else: ?>
            <li><a href="auth/login.php"><i class="fas fa-sign-in-alt"></i> Login</a></li>
            <li><a href="auth/register.php"><i class="fas fa-user-plus"></i> Register</a></li>
        <?php endif; ?>
        <li>
            <button class="theme-toggle" onclick="toggleTheme()">
                <i class="fas fa-moon"></i>
                <span id="theme-text">Dark</span>
            </button>
        </li>
    </ul>
</nav>

<!-- Hero Section -->
<section class="hero">
    <div class="hero-content">
        <h1>Smart Vehicle Rental Platform</h1>
        <p>Rent Vehicles With or Without Professional Drivers</p>
        <div class="hero-buttons">
            <?php if ($logged_in): ?>
                <a href="<?php echo $dashboard_url; ?>" class="hero-btn hero-btn-primary">
                    <i class="fas fa-tachometer-alt"></i> Go to Dashboard
                </a>
            <?php else: ?>
                <a href="auth/login.php" class="hero-btn hero-btn-primary">
                    <i class="fas fa-sign-in-alt"></i> Get Started
                </a>
                <a href="auth/register.php" class="hero-btn hero-btn-secondary">
                    <i class="fas fa-user-plus"></i> Register Now
                </a>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Features Section -->
<section class="features-section">
    <h2 style="text-align: center; font-size: 36px; font-weight: 700; margin-bottom: 10px;">
        <span style="background: var(--gradient-primary); -webkit-background-clip: text; background-clip: text; -webkit-text-fill-color: transparent;">Our Features</span>
    </h2>
    <p style="text-align: center; color: var(--medium-gray); font-size: 18px;">Everything you need for a seamless rental experience</p>
    
    <div class="features-grid">
        <div class="feature-card">
            <div class="feature-icon">
                <i class="fas fa-car"></i>
            </div>
            <h3>Vehicle Rental</h3>
            <p>Rent cars, bikes and microbikes anytime with our extensive fleet of vehicles.</p>
        </div>
        
        <div class="feature-card">
            <div class="feature-icon">
                <i class="fas fa-id-card"></i>
            </div>
            <h3>Professional Drivers</h3>
            <p>Choose from our verified drivers with excellent ratings and experience.</p>
        </div>
        
        <div class="feature-card">
            <div class="feature-icon">
                <i class="fas fa-shield-alt"></i>
            </div>
            <h3>Secure Booking</h3>
            <p>Fast and secure booking management with real-time availability.</p>
        </div>
        
        <div class="feature-card">
            <div class="feature-icon">
                <i class="fas fa-wallet"></i>
            </div>
            <h3>Transparent Pricing</h3>
            <p>No hidden fees. Pay only for what you use with clear pricing.</p>
        </div>
        
        <div class="feature-card">
            <div class="feature-icon">
                <i class="fas fa-headset"></i>
            </div>
            <h3>24/7 Support</h3>
            <p>Our dedicated support team is available around the clock to help you.</p>
        </div>
        
        <div class="feature-card">
            <div class="feature-icon">
                <i class="fas fa-star"></i>
            </div>
            <h3>Rated Services</h3>
            <p>Rate and review drivers and vehicles to help others make informed choices.</p>
        </div>
    </div>
</section>

<!-- Vehicle Categories -->
<section style="padding: 80px 50px; background: var(--off-white);">
    <h2 style="text-align: center; font-size: 36px; font-weight: 700; margin-bottom: 10px;">
        <span style="background: var(--gradient-primary); -webkit-background-clip: text; background-clip: text; -webkit-text-fill-color: transparent;">Vehicle Categories</span>
    </h2>
    <p style="text-align: center; color: var(--medium-gray); font-size: 18px; margin-bottom: 50px;">Choose from our wide range of vehicles</p>
    
    <div class="vehicle-grid">
        <div class="vehicle-card">
            <img src="https://images.unsplash.com/photo-1553440569-bcc63803a83d?w=800" alt="Cars" class="vehicle-image">
            <div class="vehicle-info">
                <span class="vehicle-type">Cars</span>
                <h3 class="vehicle-title">Sedans & SUVs</h3>
                <p style="color: var(--medium-gray); margin-bottom: 15px;">Comfortable cars for city and highway travel</p>
                <div class="vehicle-features">
                    <span class="vehicle-feature"><i class="fas fa-user"></i> 4-5 Seats</span>
                    <span class="vehicle-feature"><i class="fas fa-gas-pump"></i> Petrol/Diesel</span>
                    <span class="vehicle-feature"><i class="fas fa-snowflake"></i> AC</span>
                </div>
            </div>
        </div>
        
        <div class="vehicle-card">
            <img src="https://images.unsplash.com/photo-1558981806-ec527fa84c39?w=800" alt="Motorcycles" class="vehicle-image">
            <div class="vehicle-info">
                <span class="vehicle-type">Motorcycles</span>
                <h3 class="vehicle-title">Bikes & Scooters</h3>
                <p style="color: var(--medium-gray); margin-bottom: 15px;">Perfect for quick trips and city navigation</p>
                <div class="vehicle-features">
                    <span class="vehicle-feature"><i class="fas fa-user"></i> 1-2 Seats</span>
                    <span class="vehicle-feature"><i class="fas fa-gas-pump"></i> Petrol/Electric</span>
                    <span class="vehicle-feature"><i class="fas fa-wind"></i> Air-cooled</span>
                </div>
            </div>
        </div>
        
        <div class="vehicle-card">
            <img src="https://images.unsplash.com/photo-1544620347-c4fd4a3d5957?w=800" alt="Microbus" class="vehicle-image">
            <div class="vehicle-info">
                <span class="vehicle-type">Microbus</span>
                <h3 class="vehicle-title">Vans & Microbuses</h3>
                <p style="color: var(--medium-gray); margin-bottom: 15px;">Spacious vehicles for group travel</p>
                <div class="vehicle-features">
                    <span class="vehicle-feature"><i class="fas fa-users"></i> 10-15 Seats</span>
                    <span class="vehicle-feature"><i class="fas fa-gas-pump"></i> Diesel</span>
                    <span class="vehicle-feature"><i class="fas fa-snowflake"></i> AC</span>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- About Section -->
<section style="padding: 80px 50px; background: var(--white); text-align: center;">
    <div style="max-width: 800px; margin: 0 auto;">
        <h2 style="font-size: 28px; font-weight: 600; margin-bottom: 15px;">
            <span style="background: var(--gradient-primary); -webkit-background-clip: text; background-clip: text; -webkit-text-fill-color: transparent;">About RideRent Pro</span>
        </h2>
        <p style="font-size: 18px; color: var(--medium-gray); line-height: 1.8;">
            RideRent Pro is a smart vehicle rental management system where customers can rent vehicles with or without professional drivers. Vehicle owners can manage vehicles and drivers, while administrators ensure platform security through verification and monitoring. Our platform provides a seamless experience for all users with modern features and secure transactions.
        </p>
    </div>
</section>

<!-- Footer -->
<footer class="footer">
    <h3><i class="fas fa-car-side"></i> RideRent Pro</h3>
    <p>Vehicle Rental Management System</p>
    <p style="margin-top: 10px; font-size: 12px;">CSE 327 Software Engineering Project</p>
</footer>

</body>
</html>