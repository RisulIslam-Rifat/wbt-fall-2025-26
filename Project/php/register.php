<?php
// register.php - Handles user registration with secure password hashing
header('Content-Type: application/json');

// Include database connection
require_once 'db_connect.php';

// Get POST data
$data = json_decode(file_get_contents('php://input'), true);

// Validate that data was received
if (!$data) {
    echo json_encode(['success' => false, 'message' => 'No data received']);
    exit;
}

// Validate required fields
$required_fields = ['fullname', 'email', 'phone', 'password', 'role'];
foreach ($required_fields as $field) {
    if (!isset($data[$field]) || empty(trim($data[$field]))) {
        echo json_encode(['success' => false, 'message' => "Field '$field' is required"]);
        exit;
    }
}

// Extract and sanitize data
$fullname = trim($data['fullname']);
$email = trim($data['email']);
$phone = trim($data['phone']);
$password = $data['password'];
$role = trim($data['role']);

// Validate email format
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Invalid email format']);
    exit;
}

// Validate role
$allowed_roles = ['admin', 'donor', 'finder'];
if (!in_array($role, $allowed_roles)) {
    echo json_encode(['success' => false, 'message' => 'Invalid role specified']);
    exit;
}

// Validate password strength (minimum 6 characters)
if (strlen($password) < 6) {
    echo json_encode(['success' => false, 'message' => 'Password must be at least 6 characters long']);
    exit;
}

// Hash the password using bcrypt
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

try {
    // Check if email already exists using prepared statement
    $check_stmt = mysqli_prepare($conn, "SELECT id FROM users WHERE email = ?");
    
    if (!$check_stmt) {
        throw new Exception('Database query preparation failed');
    }
    
    mysqli_stmt_bind_param($check_stmt, "s", $email);
    mysqli_stmt_execute($check_stmt);
    $check_result = mysqli_stmt_get_result($check_stmt);
    
    if (mysqli_num_rows($check_result) > 0) {
        echo json_encode(['success' => false, 'message' => 'Email already registered']);
        mysqli_stmt_close($check_stmt);
        exit;
    }
    mysqli_stmt_close($check_stmt);
    
    // Prepare base insert query
    $hospital = null;
    $smoking = null;
    $chronic_illness = null;
    $illness_details = null;
    $tattoo = null;
    $travel = null;
    
    // Get hospital if role is admin or donor
    if ($role === 'admin' || $role === 'donor') {
        if (!isset($data['hospital']) || empty(trim($data['hospital']))) {
            echo json_encode(['success' => false, 'message' => 'Hospital affiliation is required for ' . $role . 's']);
            exit;
        }
        $hospital = trim($data['hospital']);
    }
    
    // Get Blood Group for Donors
    $blood_group = null;
    if ($role === 'donor') {
        if (!isset($data['blood_group']) || empty(trim($data['blood_group']))) {
            echo json_encode(['success' => false, 'message' => 'Blood Group is required for donors']);
            exit;
        }
        $blood_group = trim($data['blood_group']);
    }
    
    // Get donor-specific health information
    if ($role === 'donor') {
        $smoking = isset($data['smoking']) ? $data['smoking'] : null;
        $chronic_illness = isset($data['chronic_illness']) ? $data['chronic_illness'] : null;
        $illness_details = isset($data['illness_details']) ? trim($data['illness_details']) : null;
        $tattoo = isset($data['tattoo']) ? $data['tattoo'] : null;
        $travel = isset($data['travel']) ? $data['travel'] : null;
    }
    
    // Determine status (all roles start as pending for admin approval)
    $status = 'pending';
    
    // Insert user into database using prepared statement
    $stmt = mysqli_prepare($conn, 
        "INSERT INTO users (fullname, email, phone, password, role, hospital, status, smoking, chronic_illness, illness_details, tattoo, travel, blood_group) 
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
    );
    
    if (!$stmt) {
        throw new Exception('Database query preparation failed: ' . mysqli_error($conn));
    }
    
    mysqli_stmt_bind_param($stmt, "sssssssssssss", 
        $fullname, 
        $email, 
        $phone, 
        $hashed_password, 
        $role, 
        $hospital,
        $status,
        $smoking,
        $chronic_illness,
        $illness_details,
        $tattoo,
        $travel,
        $blood_group
    );
    
    if (mysqli_stmt_execute($stmt)) {
        $user_id = mysqli_insert_id($conn);
        
        $message = 'Registration successful! Your account is pending admin approval.';
        
        echo json_encode([
            'success' => true,
            'message' => $message,
            'user_id' => $user_id
        ]);
    } else {
        throw new Exception('Failed to insert user into database');
    }
    
    mysqli_stmt_close($stmt);
    
} catch (Exception $e) {
    // Log error for debugging (in production, log to file)
    error_log("Registration error: " . $e->getMessage());
    
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred during registration. Please try again.'
    ]);
}

mysqli_close($conn);
?>

