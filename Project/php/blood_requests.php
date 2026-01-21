<?php
// blood_requests.php - Handle blood request operations
// Disable error displaying to user (logs to server instead) to prevent breaking JSON
ini_set('display_errors', 0);
error_reporting(E_ALL);

// Handle Fatal Errors
register_shutdown_function(function() {
    $error = error_get_last();
    if ($error !== NULL && $error['type'] === E_ERROR) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Fatal Error: ' . $error['message'] . ' on line ' . $error['line']]);
        exit;
    }
});

header('Content-Type: application/json');
require_once 'db_connect.php';
session_start();

$method = $_SERVER['REQUEST_METHOD'];

// Helper to get user ID from session
if (!isset($_SESSION['user_id'])) {
    // Return explicit JSON error if session lost
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit;
}

switch ($method) {
    case 'GET':
        // Fetch all requests
        $query = "SELECT br.*, u.fullname as db_requester_name, u.email as db_requester_email 
                  FROM blood_requests br 
                  JOIN users u ON br.user_id = u.id 
                  ORDER BY br.created_at DESC";
        $result = mysqli_query($conn, $query);
        
        $requests = [];
        while ($row = mysqli_fetch_assoc($result)) {
            // Format for frontend
            $requests[] = [
                'id' => $row['id'],
                'user_id' => $row['user_id'], // To check ownership
                'fullname' => $row['db_requester_name'], // Requester name
                'email' => $row['db_requester_email'], // Requester email
                'bloodGroup' => $row['blood_group'],
                'admittedHospital' => $row['hospital'],
                'patientLocation' => $row['location'],
                'contacts' => [$row['contact1'], $row['contact2']],
                'timestamp' => $row['created_at']
            ];
        }
        echo json_encode(['success' => true, 'requests' => $requests]);
        break;

    case 'POST':
        // Create new request
        $json_input = file_get_contents('php://input');
        $data = json_decode($json_input, true);
        
        if ($data === null) {
            echo json_encode(['success' => false, 'message' => 'Invalid JSON received']);
            exit;
        }
        
        // Basic validation
        if (!isset($_SESSION['user_id'])) {
             echo json_encode(['success' => false, 'message' => 'Please login required']);
             exit;
        }

        $user_id = $_SESSION['user_id'];
        $blood_group = $data['bloodGroup'];
        $hospital = $data['admittedHospital'];
        $location = $data['patientLocation'];
        $contact1 = $data['contacts'][0];
        $contact2 = isset($data['contacts'][1]) ? $data['contacts'][1] : '';

        $stmt = mysqli_prepare($conn, "INSERT INTO blood_requests (user_id, blood_group, hospital, location, contact1, contact2) VALUES (?, ?, ?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "isssss", $user_id, $blood_group, $hospital, $location, $contact1, $contact2);

        if (mysqli_stmt_execute($stmt)) {
            echo json_encode(['success' => true, 'message' => 'Request posted successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to post request']);
        }
        mysqli_stmt_close($stmt);
        break;

    case 'DELETE':
        // Delete request
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }

        $request_id = $data['id']; // We need the ID, not timestamp
        $user_id = $_SESSION['user_id'];

        // Verify ownership
        $check_stmt = mysqli_prepare($conn, "SELECT id FROM blood_requests WHERE id = ? AND user_id = ?");
        mysqli_stmt_bind_param($check_stmt, "ii", $request_id, $user_id);
        mysqli_stmt_execute($check_stmt);
        $check_result = mysqli_stmt_get_result($check_stmt);

        if (mysqli_num_rows($check_result) === 0) {
            echo json_encode(['success' => false, 'message' => 'Not authorized to delete this request']);
            exit;
        }
        mysqli_stmt_close($check_stmt);

        // Delete
        $stmt = mysqli_prepare($conn, "DELETE FROM blood_requests WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "i", $request_id);

        if (mysqli_stmt_execute($stmt)) {
            echo json_encode(['success' => true, 'message' => 'Request deleted']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to delete']);
        }
        mysqli_stmt_close($stmt);
        break;
}

mysqli_close($conn);
?>
