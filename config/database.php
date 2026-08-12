<?php
// Database Configuration
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "riderent_prodb";

// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname);

// Check connection
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Set charset
mysqli_set_charset($conn, "utf8mb4");
?>