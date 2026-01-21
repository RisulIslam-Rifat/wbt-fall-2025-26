<?php
// hash.php - Password hashing utility
// This script can be used to hash passwords for testing or database migration

header('Content-Type: application/json');
require_once 'db_connect.php';

// Check if this is a direct browser access (for manual hashing)
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['password'])) {
    $password = $_GET['password'];
    $hashed = password_hash($password, PASSWORD_DEFAULT);
    
    echo json_encode([
        'success' => true,
        'original' => $password,
        'hashed' => $hashed,
        'algorithm' => 'bcrypt (PASSWORD_DEFAULT)'
    ]);
    exit;
}

// POST request to hash and update passwords in database
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    // Hash all plain text passwords in the database
    if (isset($data['action']) && $data['action'] === 'hash_all') {
        try {
            // Get all users
            $query = "SELECT id, email, password FROM users";
            $result = mysqli_query($conn, $query);
            
            if (!$result) {
                throw new Exception('Failed to fetch users');
            }
            
            $updated_count = 0;
            $already_hashed = 0;
            
            while ($row = mysqli_fetch_assoc($result)) {
                $current_password = $row['password'];
                
                // Check if password is already hashed (bcrypt hashes start with $2y$)
                if (password_get_info($current_password)['algo'] !== null) {
                    $already_hashed++;
                    continue;
                }
                
                // Hash the plain text password
                $hashed_password = password_hash($current_password, PASSWORD_DEFAULT);
                
                // Update in database
                $update_stmt = mysqli_prepare($conn, "UPDATE users SET password = ? WHERE id = ?");
                mysqli_stmt_bind_param($update_stmt, "si", $hashed_password, $row['id']);
                
                if (mysqli_stmt_execute($update_stmt)) {
                    $updated_count++;
                }
                
                mysqli_stmt_close($update_stmt);
            }
            
            echo json_encode([
                'success' => true,
                'message' => 'Password hashing completed',
                'updated' => $updated_count,
                'already_hashed' => $already_hashed,
                'total_processed' => $updated_count + $already_hashed
            ]);
            
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
        
        mysqli_close($conn);
        exit;
    }
    
    // Hash a single password for a specific user
    if (isset($data['email']) && isset($data['new_password'])) {
        $email = $data['email'];
        $new_password = $data['new_password'];
        
        try {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            
            $stmt = mysqli_prepare($conn, "UPDATE users SET password = ? WHERE email = ?");
            mysqli_stmt_bind_param($stmt, "ss", $hashed_password, $email);
            
            if (mysqli_stmt_execute($stmt)) {
                echo json_encode([
                    'success' => true,
                    'message' => "Password updated for $email"
                ]);
            } else {
                throw new Exception('Failed to update password');
            }
            
            mysqli_stmt_close($stmt);
            
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
        
        mysqli_close($conn);
        exit;
    }
    
    // Hash a single password (no database update)
    if (isset($data['password'])) {
        $password = $data['password'];
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        
        echo json_encode([
            'success' => true,
            'hashed' => $hashed,
            'algorithm' => 'bcrypt (PASSWORD_DEFAULT)'
        ]);
        exit;
    }
}

// Show usage information
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Hash Utility - BloodLink</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #137fec;
            margin-bottom: 10px;
        }
        .endpoint {
            background: #f8f9fa;
            padding: 15px;
            margin: 15px 0;
            border-left: 4px solid #137fec;
            border-radius: 4px;
        }
        code {
            background: #e9ecef;
            padding: 2px 6px;
            border-radius: 3px;
            font-family: 'Courier New', monospace;
        }
        .method {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-weight: bold;
            font-size: 12px;
            margin-right: 10px;
        }
        .get { background: #28a745; color: white; }
        .post { background: #007bff; color: white; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔐 Password Hash Utility</h1>
        <p>This utility helps you hash passwords for the BloodLink system.</p>
        
        <h2>Available Endpoints:</h2>
        
        <div class="endpoint">
            <span class="method get">GET</span>
            <strong>Hash a single password (browser)</strong>
            <p><code>hash.php?password=yourpassword</code></p>
            <p>Example: <a href="hash.php?password=test123">hash.php?password=test123</a></p>
        </div>
        
        <div class="endpoint">
            <span class="method post">POST</span>
            <strong>Hash a password (API)</strong>
            <pre>{ "password": "yourpassword" }</pre>
        </div>
        
        <div class="endpoint">
            <span class="method post">POST</span>
            <strong>Update password for a user</strong>
            <pre>{ "email": "user@example.com", "new_password": "newpass123" }</pre>
        </div>
        
        <div class="endpoint">
            <span class="method post">POST</span>
            <strong>Hash all plain text passwords in database</strong>
            <pre>{ "action": "hash_all" }</pre>
            <p><em>⚠️ Use with caution! This will update all unhashed passwords.</em></p>
        </div>
        
        <h2>Security Notes:</h2>
        <ul>
            <li>Uses <code>PASSWORD_DEFAULT</code> (bcrypt algorithm)</li>
            <li>Automatically detects already hashed passwords</li>
            <li>Safe to run multiple times (won't double-hash)</li>
            <li><strong>DELETE THIS FILE in production environments</strong></li>
        </ul>
    </div>
</body>
</html>
