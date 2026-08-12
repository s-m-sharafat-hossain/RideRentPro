<?php
session_start();
if (!isset($_SESSION['owner_id'])) {
    header("Location: ../auth/login.php");
    exit();
}
// Redirect to new vehicles folder
header("Location: vehicles/vehicle_list.php");
exit();
?>