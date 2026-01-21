<?php
// login.php - Handles user login with secure authentication
header('Content-Type: application/json');
session_start();

// Include database connection
require_once 'db_connect.php';

// Get POST data
$data = json_decode(file_get_contents('php://input'), true);

// Validate that data was received
if (!$data) {
    echo json_encode(['success' => false, 'message' => 'No data received']);
    exit;
}

// Validate required fields exist
if (!isset($data['email']) || !isset($data['password'])) {
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit;
}

// Extract and validate email
$email = trim($data['email']);
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Invalid email format']);
    exit;
}

// Extract password
$password = $data['password'];

try {
    // Use prepared statement to prevent SQL injection
    $stmt = mysqli_prepare($conn, "SELECT id, fullname, email, password, role, status FROM users WHERE email = ?");
    
    if (!$stmt) {
        throw new Exception('Database query preparation failed');
    }
    
    // Bind parameters (s = string)
    mysqli_stmt_bind_param($stmt, "s", $email);
    
    // Execute the prepared statement
    mysqli_stmt_execute($stmt);
    
    // Get result
    $result = mysqli_stmt_get_result($stmt);
    
    // Check if user exists
    if (mysqli_num_rows($result) === 0) {
        echo json_encode(['success' => false, 'message' => 'User not found']);
        exit;
    }
    
    // Fetch user data
    $user = mysqli_fetch_assoc($result);
    
    // Verify password
    if (password_verify($password, $user['password'])) {
        // Password correct, start session
        session_regenerate_id(true);
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['name'] = $user['fullname'];
        
        // Check strict status for all (pending users cannot login except admin)
        if ($user['status'] === 'pending' && $user['role'] !== 'admin') {
             echo json_encode(['success' => false, 'message' => 'Your account is pending approval.']);
             exit;
        }
        if ($user['status'] === 'declined') {
             echo json_encode(['success' => false, 'message' => 'Your account has been declined.']);
             exit;
        }

        // Determine redirect URL based on role
        $redirect = '../index.html'; // Default
        if ($user['role'] === 'admin') {
            $redirect = '../html/Admin.html';
        } elseif ($user['role'] === 'finder' || $user['role'] === 'donor') {
            $redirect = '../html/Finder.html';
        }

        echo json_encode([
            'success' => true, 
            'message' => 'Login successful',
            'user' => [
                'id' => $user['id'],
                'name' => $user['fullname'],
                'email' => $user['email'],
                'role' => $user['role']
            ],
            'redirect' => $redirect
        ]);
    } else {
        // Forward compatibility: Check if plain text password matches (migration strategy)
        if ($password === $user['password']) {
            // Upgrade to hash
            $new_hash = password_hash($password, PASSWORD_DEFAULT);
            $update_stmt = mysqli_prepare($conn, "UPDATE users SET password = ? WHERE id = ?");
            mysqli_stmt_bind_param($update_stmt, "si", $new_hash, $user['id']);
            mysqli_stmt_execute($update_stmt);
            
            // Login success after migration
            session_regenerate_id(true);
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['name'] = $user['fullname'];

            // Status check
             if ($user['status'] === 'pending' && $user['role'] !== 'admin') {
                 echo json_encode(['success' => false, 'message' => 'Your account is pending approval.']);
                 exit;
            }
            if ($user['status'] === 'declined') {
                 echo json_encode(['success' => false, 'message' => 'Your account has been declined.']);
                 exit;
            }

            // Determine redirect
            $redirect = '../index.html';
            if ($user['role'] === 'admin') {
                $redirect = '../html/Admin.html';
            } elseif ($user['role'] === 'finder') {
                $redirect = '../html/Finder.html';
            } elseif ($user['role'] === 'donor') {
                $redirect = '../index.html';
            }

            echo json_encode([
                'success' => true, 
                'message' => 'Login successful (Security Updated)',
                'user' => [
                    'id' => $user['id'],
                    'name' => $user['fullname'],
                    'email' => $user['email'],
                    'role' => $user['role']
                ],
                'redirect' => $redirect
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid password']);
        }
    }

} catch (Exception $e) {
    error_log("Login Error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'An internal error occurred']);
}

mysqli_close($conn);
?>
