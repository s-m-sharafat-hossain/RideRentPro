<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: ../auth/login.php");
    exit();
}
require_once __DIR__ . '/../config/database.php';

// Handle approval/rejection/deletion
if(isset($_GET['action']) && isset($_GET['id'])) {
    $vehicle_id = intval($_GET['id']);
    $action = $_GET['action'];
    
    if($action == 'approve') {
        $update = "UPDATE vehicle SET approval_status = 'Approved' WHERE vehicle_id = '$vehicle_id'";
        mysqli_query($conn, $update);
        header("Location: vehicle_approvals.php?msg=approved");
        exit();
    } elseif($action == 'reject') {
        $update = "UPDATE vehicle SET approval_status = 'Rejected' WHERE vehicle_id = '$vehicle_id'";
        mysqli_query($conn, $update);
        header("Location: vehicle_approvals.php?msg=rejected");
        exit();
    } elseif($action == 'delete') {
        // Delete vehicle (this will automatically remove it from owner's view)
        $delete = "DELETE FROM vehicle WHERE vehicle_id = '$vehicle_id'";
        if(mysqli_query($conn, $delete)) {
            header("Location: vehicle_approvals.php?msg=deleted");
        } else {
            header("Location: vehicle_approvals.php?msg=delete_failed");
        }
        exit();
    }
}

$active_page = 'Vehicle Approvals';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vehicle Approvals - RideRent Pro</title>
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
            <h1><i class="fas fa-check-circle"></i> Vehicle Approvals</h1>
            <p>Review and approve vehicle owner requests</p>
        </div>

        <?php if(isset($_GET['msg'])): ?>
            <div class="alert alert-success">
                <?php 
                if($_GET['msg'] == 'approved') echo "Vehicle approved successfully!";
                if($_GET['msg'] == 'rejected') echo "Vehicle rejected successfully!";
                if($_GET['msg'] == 'deleted') echo "Vehicle deleted successfully!";
                ?>
            </div>
        <?php endif; ?>
        
        <?php if(isset($_GET['msg']) && $_GET['msg'] == 'delete_failed'): ?>
            <div class="alert alert-danger">
                Failed to delete vehicle. Please try again.
            </div>
        <?php endif; ?>

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Pending Approvals</h3>
            </div>
            <div class="card-body">
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Vehicle Name</th>
                                <th>Brand</th>
                                <th>Model</th>
                                <th>Owner</th>
                                <th>Type</th>
                                <th>Price/Day</th>
                                <th>Location</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $sql = "SELECT v.*, o.full_name as owner_name FROM vehicle v 
                                    LEFT JOIN vehicle_owner o ON v.owner_id = o.owner_id 
                                    WHERE v.approval_status = 'Pending' 
                                    ORDER BY v.created_at DESC";
                            $result = mysqli_query($conn, $sql);
                            
                            if ($result && mysqli_num_rows($result) > 0) {
                                while ($row = mysqli_fetch_assoc($result)) {
                            ?>
                                    <tr>
                                        <td><?php echo $row['vehicle_id']; ?></td>
                                        <td><?php echo htmlspecialchars($row['vehicle_name']); ?></td>
                                        <td><?php echo htmlspecialchars($row['brand']); ?></td>
                                        <td><?php echo htmlspecialchars($row['model']); ?></td>
                                        <td><?php echo htmlspecialchars($row['owner_name']); ?></td>
                                        <td><?php echo htmlspecialchars($row['vehicle_type']); ?></td>
                                        <td>৳<?php echo number_format($row['price_per_day'], 2); ?></td>
                                        <td><?php echo htmlspecialchars($row['location']); ?></td>
                                        <td><span class='badge badge-warning'>Pending</span></td>
                                        <td>
                                            <a href="vehicle_approvals.php?action=approve&id=<?php echo $row['vehicle_id']; ?>" class="btn btn-success btn-sm" onclick="return confirm('Approve this vehicle?')"><i class="fas fa-check"></i> Approve</a>
                                            <a href="vehicle_approvals.php?action=reject&id=<?php echo $row['vehicle_id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Reject this vehicle?')"><i class="fas fa-times"></i> Reject</a>
                                            <a href="vehicle_approvals.php?action=delete&id=<?php echo $row['vehicle_id']; ?>" class="btn btn-secondary btn-sm" onclick="return confirm('Delete this vehicle? This will remove it from the owner\'s account.')"><i class="fas fa-trash"></i> Delete</a>
                                        </td>
                                    </tr>
                            <?php
                                }
                            } else {
                                echo "<tr><td colspan='11' style='text-align: center; padding: 30px;'>No pending vehicle approvals.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">All Vehicles</h3>
            </div>
            <div class="card-body">
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Vehicle Name</th>
                                <th>Brand</th>
                                <th>Owner</th>
                                <th>Type</th>
                                <th>Price/Day</th>
                                <th>Approval Status</th>
                                <th>Availability</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $sql = "SELECT v.*, o.full_name as owner_name FROM vehicle v 
                                    LEFT JOIN vehicle_owner o ON v.owner_id = o.owner_id 
                                    ORDER BY v.created_at DESC";
                            $result = mysqli_query($conn, $sql);
                            
                            if ($result && mysqli_num_rows($result) > 0) {
                                while ($row = mysqli_fetch_assoc($result)) {
                                    $approvalBadge = '';
                                    if($row['approval_status'] == 'Approved') $approvalBadge = 'badge-success';
                                    elseif($row['approval_status'] == 'Rejected') $approvalBadge = 'badge-danger';
                                    else $approvalBadge = 'badge-warning';
                                    
                                    $availabilityBadge = '';
                                    if($row['availability'] == 'Available') $availabilityBadge = 'badge-success';
                                    elseif($row['availability'] == 'Booked') $availabilityBadge = 'badge-danger';
                                    else $availabilityBadge = 'badge-warning';
                            ?>
                                    <tr>
                                        <td><?php echo $row['vehicle_id']; ?></td>
                                        <td><?php echo htmlspecialchars($row['vehicle_name']); ?></td>
                                        <td><?php echo htmlspecialchars($row['brand']); ?></td>
                                        <td><?php echo htmlspecialchars($row['owner_name']); ?></td>
                                        <td><?php echo htmlspecialchars($row['vehicle_type']); ?></td>
                                        <td>৳<?php echo number_format($row['price_per_day'], 2); ?></td>
                                        <td><span class='badge <?php echo $approvalBadge; ?>'><?php echo $row['approval_status']; ?></span></td>
                                        <td><span class='badge <?php echo $availabilityBadge; ?>'><?php echo $row['availability']; ?></span></td>
                                        <td>
                                            <a href="vehicle_approvals.php?action=delete&id=<?php echo $row['vehicle_id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Delete this vehicle? This will permanently remove it from the owner\'s account.')"><i class="fas fa-trash"></i> Delete</a>
                                        </td>
                                    </tr>
                            <?php
                                }
                            } else {
                                echo "<tr><td colspan='9' style='text-align: center; padding: 30px;'>No vehicles found.</td></tr>";
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