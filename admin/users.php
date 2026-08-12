<?php
session_start();
if (!isset($_SESSION['admin'])) {
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

// Handle Delete
if(isset($_GET['delete']) && isset($_GET['type'])) {
    $id = $_GET['delete'];
    $type = $_GET['type'];
    
    $table = "";
    $id_field = "";
    
    switch($type) {
        case 'admin':
            $table = "admin";
            $id_field = "admin_id";
            break;
        case 'customer':
            $table = "customer";
            $id_field = "customer_id";
            break;
        case 'driver':
            $table = "driver";
            $id_field = "driver_id";
            break;
        case 'owner':
            $table = $owner_table;
            $id_field = "owner_id";
            break;
    }
    
    if($table && $id_field) {
        $sql = "DELETE FROM $table WHERE $id_field = '$id'";
        mysqli_query($conn, $sql);
    }
    
    header("Location: users.php");
    exit();
}

// Handle Status Update
if(isset($_GET['status']) && isset($_GET['type']) && isset($_GET['id'])) {
    $id = $_GET['id'];
    $type = $_GET['type'];
    $status = $_GET['status'];
    
    $table = "";
    $id_field = "";
    
    switch($type) {
        case 'admin':
            $table = "admin";
            $id_field = "admin_id";
            break;
        case 'customer':
            $table = "customer";
            $id_field = "customer_id";
            break;
        case 'driver':
            $table = "driver";
            $id_field = "driver_id";
            break;
        case 'owner':
            $table = $owner_table;
            $id_field = "owner_id";
            break;
    }
    
    if($table && $id_field) {
        $sql = "UPDATE $table SET status = '$status' WHERE $id_field = '$id'";
        mysqli_query($conn, $sql);
    }
    
    header("Location: users.php");
    exit();
}

// Get filter
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';

$active_page = 'Users';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users Management - RideRent Pro</title>
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
            <h1><i class="fas fa-users"></i> Users Management</h1>
            <p>Manage all system users</p>
        </div>

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Filter Users</h3>
            </div>
            <div class="card-body">
                <div class="filter-tabs">
                    <a href="users.php?filter=all" class="btn <?php echo $filter == 'all' ? 'btn-primary' : 'btn-secondary'; ?>">All Users</a>
                    <a href="users.php?filter=admin" class="btn <?php echo $filter == 'admin' ? 'btn-primary' : 'btn-secondary'; ?>">Admins</a>
                    <a href="users.php?filter=customer" class="btn <?php echo $filter == 'customer' ? 'btn-primary' : 'btn-secondary'; ?>">Customers</a>
                    <a href="users.php?filter=driver" class="btn <?php echo $filter == 'driver' ? 'btn-primary' : 'btn-secondary'; ?>">Drivers</a>
                    <a href="users.php?filter=owner" class="btn <?php echo $filter == 'owner' ? 'btn-primary' : 'btn-secondary'; ?>">Vehicle Owners</a>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Users List</h3>
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
                                <th>Type</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if($filter == 'all' || $filter == 'admin') {
                                $result = mysqli_query($conn, "SELECT * FROM admin");
                                if ($result) {
                                    while($row = mysqli_fetch_assoc($result)) {
                                    $statusClass = $row['status'] == 'Active' ? 'badge-success' : 'badge-danger';
                                    echo "<tr>
                                        <td>{$row['admin_id']}</td>
                                        <td>{$row['full_name']}</td>
                                        <td>{$row['email']}</td>
                                        <td>{$row['phone']}</td>
                                        <td><span class='badge badge-primary'>Admin</span></td>
                                        <td><span class='badge {$statusClass}'>{$row['status']}</span></td>
                                        <td>
                                            <a href='users.php?id={$row['admin_id']}&type=admin&status=Active' class='btn btn-success btn-sm'>Activate</a>
                                            <a href='users.php?id={$row['admin_id']}&type=admin&status=Inactive' class='btn btn-warning btn-sm'>Deactivate</a>
                                            <a href='users.php?delete={$row['admin_id']}&type=admin' class='btn btn-danger btn-sm' onclick='return confirm(\"Are you sure?\")'>Delete</a>
                                        </td>
                                    </tr>";
                                }
                                }
                            }
                            
                            if($filter == 'all' || $filter == 'customer') {
                                $result = mysqli_query($conn, "SELECT * FROM customer");
                                if ($result) {
                                    while($row = mysqli_fetch_assoc($result)) {
                                    $statusClass = $row['status'] == 'Active' ? 'badge-success' : 'badge-danger';
                                    echo "<tr>
                                        <td>{$row['customer_id']}</td>
                                        <td>{$row['full_name']}</td>
                                        <td>{$row['email']}</td>
                                        <td>{$row['phone_1']}</td>
                                        <td><span class='badge badge-info'>Customer</span></td>
                                        <td><span class='badge {$statusClass}'>{$row['status']}</span></td>
                                        <td>
                                            <a href='users.php?id={$row['customer_id']}&type=customer&status=Active' class='btn btn-success btn-sm'>Activate</a>
                                            <a href='users.php?id={$row['customer_id']}&type=customer&status=Inactive' class='btn btn-warning btn-sm'>Deactivate</a>
                                            <a href='users.php?delete={$row['customer_id']}&type=customer' class='btn btn-danger btn-sm' onclick='return confirm(\"Are you sure?\")'>Delete</a>
                                        </td>
                                    </tr>";
                                }
                                }
                            }
                            
                            if($filter == 'all' || $filter == 'driver') {
                                $result = mysqli_query($conn, "SELECT * FROM driver");
                                if ($result) {
                                    while($row = mysqli_fetch_assoc($result)) {
                                    $statusClass = $row['status'] == 'Active' ? 'badge-success' : 'badge-danger';
                                    echo "<tr>
                                        <td>{$row['driver_id']}</td>
                                        <td>{$row['full_name']}</td>
                                        <td>{$row['email']}</td>
                                        <td>{$row['phone']}</td>
                                        <td><span class='badge badge-secondary'>Driver</span></td>
                                        <td><span class='badge {$statusClass}'>{$row['status']}</span></td>
                                        <td>
                                            <a href='users.php?id={$row['driver_id']}&type=driver&status=Active' class='btn btn-success btn-sm'>Activate</a>
                                            <a href='users.php?id={$row['driver_id']}&type=driver&status=Inactive' class='btn btn-warning btn-sm'>Deactivate</a>
                                            <a href='users.php?delete={$row['driver_id']}&type=driver' class='btn btn-danger btn-sm' onclick='return confirm(\"Are you sure?\")'>Delete</a>
                                        </td>
                                    </tr>";
                                }
                                }
                            }
                            
                            if($filter == 'all' || $filter == 'owner') {
                                $result = mysqli_query($conn, "SELECT * FROM vehicle_owner");
                                if ($result) {
                                    while($row = mysqli_fetch_assoc($result)) {
                                    $statusClass = $row['status'] == 'Active' ? 'badge-success' : 'badge-danger';
                                    echo "<tr>
                                        <td>{$row['owner_id']}</td>
                                        <td>{$row['full_name']}</td>
                                        <td>{$row['email']}</td>
                                        <td>{$row['phone']}</td>
                                        <td><span class='badge badge-warning'>Owner</span></td>
                                        <td><span class='badge {$statusClass}'>{$row['status']}</span></td>
                                        <td>
                                            <a href='users.php?id={$row['owner_id']}&type=owner&status=Active' class='btn btn-success btn-sm'>Activate</a>
                                            <a href='users.php?id={$row['owner_id']}&type=owner&status=Inactive' class='btn btn-warning btn-sm'>Deactivate</a>
                                            <a href='users.php?delete={$row['owner_id']}&type=owner' class='btn btn-danger btn-sm' onclick='return confirm(\"Are you sure?\")'>Delete</a>
                                        </td>
                                    </tr>";
                                }
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