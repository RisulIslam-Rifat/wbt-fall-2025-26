<?php
// update_user_status.php - Update user status (approve/decline)
header('Content-Type: application/json');
require_once 'db_connect.php';

$data = json_decode(file_get_contents('php://input'), true);

if (!$data || !isset($data['user_id']) || !isset($data['status'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid data']);
    exit;
}

$user_id = $data['user_id'];
$status = $data['status'];
$allowed_statuses = ['approved', 'declined'];

if (!in_array($status, $allowed_statuses)) {
    echo json_encode(['success' => false, 'message' => 'Invalid status']);
    exit;
}

try {
    $stmt = mysqli_prepare($conn, "UPDATE users SET status = ? WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "si", $status, $user_id);
    
    if (mysqli_stmt_execute($stmt)) {
        echo json_encode(['success' => true, 'message' => "User status updated to $status"]);
    } else {
        throw new Exception("Database update failed");
    }
    
    mysqli_stmt_close($stmt);

} catch (Exception $e) {
    error_log("Update status error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Failed to update status']);
}

mysqli_close($conn);
?>
