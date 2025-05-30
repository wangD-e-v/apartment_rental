<?php
session_start();
require 'db/db.php';

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Verify user is logged in
if (!isset($_SESSION['admin_name'])) {
    header("HTTP/1.1 401 Unauthorized");
    exit("Unauthorized access");
}

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("HTTP/1.1 405 Method Not Allowed");
    exit("Method not allowed");
}

// Get current admin data
$current_username = $_SESSION['admin_name'];
$query = "SELECT * FROM admin_reg WHERE username = ?";
$stmt = mysqli_prepare($con, $query);
mysqli_stmt_bind_param($stmt, "s", $current_username);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$admin_data = mysqli_fetch_assoc($result);

if (!$admin_data) {
    $_SESSION['error'] = "User not found";
    header("Location: profile.php");
    exit();
}

// Process form data
$username = trim($_POST['username'] ?? '');
$email = trim($_POST['email'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$errors = [];

// Validate inputs
if (empty($username)) {
    $errors[] = "Username is required";
} elseif (!preg_match('/^[a-zA-Z0-9_]{3,20}$/', $username)) {
    $errors[] = "Username must be 3-20 characters (letters, numbers, underscores)";
}

if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Invalid email format";
}

if (!empty($phone) && !preg_match('/^[\d\s\-+]{7,20}$/', $phone)) {
    $errors[] = "Invalid phone number format";
}

// Handle file upload
$profile_pic = $admin_data['profile_pic'] ?? null; // Keep current if no new upload

if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] === UPLOAD_ERR_OK) {
    $upload_dir = __DIR__ . '/uploads/';
    
    // Verify upload directory exists
    if (!is_dir($upload_dir)) {
        if (!mkdir($upload_dir, 0755, true)) {
            $errors[] = "Failed to create upload directory";
        }
    }

    if (empty($errors)) {
        $file = $_FILES['profile_pic'];
        $max_size = 2 * 1024 * 1024; // 2MB
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        
        // Verify file
        $file_info = finfo_open(FILEINFO_MIME_TYPE);
        $mime_type = finfo_file($file_info, $file['tmp_name']);
        
        if ($file['size'] > $max_size) {
            $errors[] = "File too large (max 2MB)";
        } elseif (!in_array($mime_type, $allowed_types)) {
            $errors[] = "Only JPG, PNG, and GIF images are allowed";
        } else {
            // Generate safe filename
            $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
            $new_filename = uniqid('profile_') . '.' . strtolower($ext);
            $destination = $upload_dir . $new_filename;
            
            if (move_uploaded_file($file['tmp_name'], $destination)) {
                // Delete old profile picture if it exists
                if (!empty($admin_data['profile_pic']) && file_exists($upload_dir . $admin_data['profile_pic'])) {
                    unlink($upload_dir . $admin_data['profile_pic']);
                }
                $profile_pic = $new_filename;
            } else {
                $errors[] = "Failed to save uploaded file";
            }
        }
    }
}

// Update database if no errors
if (empty($errors)) {
    $query = "UPDATE admin_reg SET 
              username = ?, 
              email = " . ($email ? "?" : "NULL") . ", 
              phone = " . ($phone ? "?" : "NULL") . "
              WHERE admin_id = ?";
    
    $stmt = mysqli_prepare($con, $query);
    
    // Dynamic parameter binding
    $params = [$username];
    $types = "s";
    
    if ($email) {
        $params[] = $email;
        $types .= "s";
    }
    
    if ($phone) {
        $params[] = $phone;
        $types .= "s";
    }
    

    
    $params[] = $admin_data['admin_id'];
    $types .= "i";
    
    mysqli_stmt_bind_param($stmt, $types, ...$params);
    
    if (mysqli_stmt_execute($stmt)) {
        // Update session if username changed
        if ($username !== $current_username) {
            $_SESSION['admin_name'] = $username;
        }
        
        $_SESSION['success'] = "Profile updated successfully!";
    } else {
        $_SESSION['error'] = "Database error: " . mysqli_error($con);
    }
    
    mysqli_stmt_close($stmt);
} else {
    $_SESSION['error'] = implode("<br>", $errors);
}

header("Location: profile.php");
exit();
?>