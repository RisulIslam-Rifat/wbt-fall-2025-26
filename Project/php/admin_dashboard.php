<?php
// admin_dashboard.php - Fetch pending users for admin dashboard
header('Content-Type: application/json');
require_once 'db_connect.php';

try {
    // Fetch pending donors
    $donors_query = "SELECT id, fullname, email, hospital, status FROM users WHERE role = 'donor'";
    $donors_result = mysqli_query($conn, $donors_query);
    $donors = [];
    while ($row = mysqli_fetch_assoc($donors_result)) {
        $donors[] = $row;
    }

    // Fetch pending finders
    $finders_query = "SELECT id, fullname, email, status FROM users WHERE role = 'finder'";
    $finders_result = mysqli_query($conn, $finders_query);
    $finders = [];
    while ($row = mysqli_fetch_assoc($finders_result)) {
        $finders[] = $row;
    }

    echo json_encode([
        'success' => true,
        'donors' => $donors,
        'finders' => $finders
    ]);

} catch (Exception $e) {
    error_log("Admin dashboard error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Failed to fetch data']);
}

mysqli_close($conn);
?>
