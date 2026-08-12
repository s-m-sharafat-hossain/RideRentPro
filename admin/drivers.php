<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: ../auth/login.php");
    exit();
}
require_once __DIR__ . '/../config/database.php';

// Handle Delete
if(isset($_GET['delete'])) {
    $id = $_GET['delete'];
    mysqli_query($conn, "DELETE FROM driver WHERE driver_id = '$id'");
    header("Location: drivers.php");
    exit();
}

// Handle Status Update
if(isset($_GET['status']) && isset($_GET['id'])) {
    $id = $_GET['id'];
    $status = $_GET['status'];
    mysqli_query($conn, "UPDATE driver SET status = '$status' WHERE driver_id = '$id'");
    header("Location: drivers.php");
    exit();
}

// Handle Availability Update
if(isset($_GET['availability']) && isset($_GET['id'])) {
    $id = $_GET['id'];
    $availability = $_GET['availability'];
    mysqli_query($conn, "UPDATE driver SET availability = '$availability' WHERE driver_id = '$id'");
    header("Location: drivers.php");
    exit();
}

$active_page = 'Drivers';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Drivers Management - RideRent Pro</title>
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
            <h1><i class="fas fa-id-card"></i> Drivers Management</h1>
            <p>Manage all system drivers</p>
        </div>

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Drivers List</h3>
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
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $result = mysqli_query($conn, "SELECT * FROM driver");
                            if (!$result) {
                                echo "<tr><td colspan='7' style='text-align: center; padding: 30px;'>Error loading drivers: " . mysqli_error($conn) . "</td></tr>";
                            } else {
                                while($row = mysqli_fetch_assoc($result)) {
                                $statusClass = $row['status'] == 'Active' ? 'badge-success' : 'badge-danger';
                                $availClass = $row['availability'] == 'Available' ? 'badge-success' : 'badge-warning';
                                echo "<tr>
                                    <td>{$row['driver_id']}</td>
                                    <td>{$row['full_name']}</td>
                                    <td>{$row['email']}</td>
                                    <td>{$row['phone']}</td>
                                    <td>{$row['license_number']}</td>
                                    <td>{$row['experience_years']} years</td>
                                    <td>{$row['rating']} ⭐</td>
                                    <td><span class='badge {$availClass}'>{$row['availability']}</span></td>
                                    <td><span class='badge {$statusClass}'>{$row['status']}</span></td>
                                    <td>
                                        <a href='drivers.php?id={$row['driver_id']}&availability=Available' class='btn btn-success btn-sm'>Available</a>
                                        <a href='drivers.php?id={$row['driver_id']}&availability=Unavailable' class='btn btn-warning btn-sm'>Unavailable</a>
                                        <a href='drivers.php?id={$row['driver_id']}&status=Active' class='btn btn-info btn-sm'>Activate</a>
                                        <a href='drivers.php?id={$row['driver_id']}&status=Inactive' class='btn btn-secondary btn-sm'>Deactivate</a>
                                        <a href='drivers.php?delete={$row['driver_id']}' class='btn btn-danger btn-sm' onclick='return confirm(\"Are you sure?\")'>Delete</a>
                                    </td>
                                </tr>";
                            }
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