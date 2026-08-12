<?php
// Sanitize input
function sanitize($data) {
    global $conn;
    return mysqli_real_escape_string($conn, $data);
}

// Redirect function
function redirect($url) {
    header("Location: $url");
    exit();
}

// Check if user is logged in
function is_logged_in() {
    return isset($_SESSION['user_id']) || isset($_SESSION['admin_id']) || 
           isset($_SESSION['owner_id']) || isset($_SESSION['driver_id']) || 
           isset($_SESSION['customer_id']);
}

// Get current user role
function get_user_role() {
    if (isset($_SESSION['admin']) || isset($_SESSION['admin_id'])) return 'admin';
    if (isset($_SESSION['owner_id'])) return 'owner';
    if (isset($_SESSION['driver_id'])) return 'driver';
    if (isset($_SESSION['customer_id'])) return 'customer';
    return null;
}

// Format date
function format_date($date) {
    return date('F j, Y', strtotime($date));
}

// Format currency
function format_currency($amount) {
    return '৳' . number_format($amount, 2);
}

// Check file upload
function upload_file($file, $target_dir, $allowed_types = ['jpg', 'jpeg', 'png', 'gif'], $max_size = 2097152) {
    if (empty($file['name'])) {
        return ['success' => false, 'message' => 'No file selected'];
    }
    
    $file_name = $file['name'];
    $file_size = $file['size'];
    $file_tmp = $file['tmp_name'];
    $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
    
    // Check file type
    if (!in_array($file_ext, $allowed_types)) {
        return ['success' => false, 'message' => 'Invalid file type'];
    }
    
    // Check file size
    if ($file_size > $max_size) {
        return ['success' => false, 'message' => 'File size too large'];
    }
    
    // Generate unique filename
    $new_file_name = time() . '_' . $file_name;
    $target_path = $target_dir . '/' . $new_file_name;
    
    // Move file
    if (move_uploaded_file($file_tmp, $target_path)) {
        return ['success' => true, 'filename' => $new_file_name];
    } else {
        return ['success' => false, 'message' => 'File upload failed'];
    }
}
?>