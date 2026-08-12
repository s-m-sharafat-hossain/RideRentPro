<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/functions.php';

$role = get_user_role();
$sidebar_items = [];

switch($role) {
    case 'admin':
        $sidebar_items = [
            ['icon' => 'fas fa-tachometer-alt', 'text' => 'Dashboard', 'link' => 'dashboard.php'],
            ['icon' => 'fas fa-users', 'text' => 'Users', 'link' => 'users.php'],
            ['icon' => 'fas fa-check-circle', 'text' => 'Vehicle Approvals', 'link' => 'vehicle_approvals.php'],
            ['icon' => 'fas fa-user-plus', 'text' => 'Driver Assignment', 'link' => 'driver_assignment.php'],
            ['icon' => 'fas fa-id-card', 'text' => 'Drivers', 'link' => 'drivers.php'],
            ['icon' => 'fas fa-calendar-check', 'text' => 'Bookings', 'link' => 'bookings.php'],
            ['icon' => 'fas fa-star', 'text' => 'Reviews', 'link' => 'reviews.php'],
            ['icon' => 'fas fa-star-half-alt', 'text' => 'Ratings', 'link' => 'ratings.php'],
            ['icon' => 'fas fa-chart-bar', 'text' => 'Reports', 'link' => 'reports.php'],
        ];
        break;
    case 'owner':
        $sidebar_items = [
            ['icon' => 'fas fa-tachometer-alt', 'text' => 'Dashboard', 'link' => 'dashboard.php'],
            ['icon' => 'fas fa-car', 'text' => 'My Vehicles', 'link' => 'vehicles/vehicle_list.php'],
            ['icon' => 'fas fa-plus-circle', 'text' => 'Add Vehicle', 'link' => 'vehicles/add_vehicle.php'],
            ['icon' => 'fas fa-calendar-check', 'text' => 'Bookings', 'link' => 'bookings.php'],
            ['icon' => 'fas fa-chart-line', 'text' => 'Vehicle Performance', 'link' => 'vehicle_performance.php'],
            ['icon' => 'fas fa-id-card', 'text' => 'Drivers', 'link' => 'drivers.php'],
            ['icon' => 'fas fa-user', 'text' => 'My Profile', 'link' => 'profile.php'],
        ];
        break;
    case 'driver':
        $sidebar_items = [
            ['icon' => 'fas fa-tachometer-alt', 'text' => 'Dashboard', 'link' => 'dashboard.php'],
            ['icon' => 'fas fa-calendar-check', 'text' => 'My Bookings', 'link' => 'bookings.php'],
            ['icon' => 'fas fa-toggle-on', 'text' => 'Availability', 'link' => 'availability.php'],
            ['icon' => 'fas fa-chart-line', 'text' => 'Performance', 'link' => 'performance.php'],
            ['icon' => 'fas fa-dollar-sign', 'text' => 'My Earnings', 'link' => 'earnings.php'],
            ['icon' => 'fas fa-user', 'text' => 'My Profile', 'link' => 'profile.php'],
        ];
        break;
    case 'customer':
        $sidebar_items = [
            ['icon' => 'fas fa-tachometer-alt', 'text' => 'Dashboard', 'link' => 'dashboard.php'],
            ['icon' => 'fas fa-car', 'text' => 'Browse Vehicles', 'link' => 'vehicles.php'],
            ['icon' => 'fas fa-calendar-check', 'text' => 'My Bookings', 'link' => 'bookings.php'],
            ['icon' => 'fas fa-balance-scale', 'text' => 'Compare', 'link' => 'compare.php'],
            ['icon' => 'fas fa-history', 'text' => 'Booking History', 'link' => 'booking_history.php'],
            ['icon' => 'fas fa-user', 'text' => 'My Profile', 'link' => 'profile.php'],
        ];
        break;
}
?>

<div class="sidebar">
    <div class="sidebar-header">
        <h2><i class="fas fa-car-side"></i> RideRent Pro</h2>
    </div>
    <div class="sidebar-nav">
        <ul>
            <?php foreach ($sidebar_items as $index => $item): ?>
                <li>
                    <a href="<?php echo $item['link']; ?>" class="<?php echo (isset($active_page) && $active_page === $item['text']) ? 'active' : ''; ?>">
                        <i class="<?php echo $item['icon']; ?>"></i> <?php echo $item['text']; ?>
                    </a>
                </li>
            <?php endforeach; ?>
            <li>
                <a href="../auth/logout.php">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </li>
        </ul>
    </div>
    <div class="sidebar-footer">
        <button class="theme-toggle" onclick="toggleTheme()" style="width: 100%; justify-content: center;">
            <i class="fas fa-moon"></i>
            <span>Dark Mode</span>
        </button>
    </div>
</div>