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

// Get drivers data
$result = mysqli_query($conn, "SELECT * FROM driver WHERE status = 'Active' ORDER BY rating DESC");
if(!$result) {
    $result = mysqli_query($conn, "SELECT * FROM driver WHERE status = 'Active'");
}

// Set active page for sidebar
$active_page = 'Drivers';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Available Drivers - RideRent Pro</title>
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
            <h1><i class="fas fa-id-card"></i> Available Drivers</h1>
            <p>View available drivers for your vehicles</p>
        </div>

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Driver List</h3>
            </div>
            <div class="card-body">
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>License</th>
                                <th>Experience</th>
                                <th>Rating</th>
                                <th>Availability</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if($result && mysqli_num_rows($result) > 0) {
                            while($row = mysqli_fetch_assoc($result)) {
                                $availClass = $row['availability'] == 'Available' ? 'badge-success' : 'badge-warning';
                                echo "<tr>
                                    <td>{$row['driver_id']}</td>
                                    <td>{$row['full_name']}</td>
                                    <td>{$row['email']}</td>
                                    <td>{$row['phone']}</td>
                                    <td>{$row['license_number']}</td>
                                    <td>{$row['experience_years']} years</td>
                                    <td><span style='color: #FD7E14;'>" . (isset($row['rating']) ? $row['rating'] : '0.0') . " ⭐</span></td>
                                    <td><span class='badge {$availClass}'>{$row['availability']}</span></td>
                                    <td><span class='badge badge-info'>{$row['status']}</span></td>
                                </tr>";
                            }
                            } else {
                                echo "<tr><td colspan='9' style='text-align: center; padding: 30px;'>No drivers found.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>