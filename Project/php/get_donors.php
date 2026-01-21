<?php
// get_donors.php - Fetch list of available donors
header('Content-Type: application/json');
require_once 'db_connect.php';
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

try {
    // Basic query to fetch donors
    // We only select necessary fields for privacy/display
    $sql = "SELECT id, fullname, blood_group, hospital, phone, email, status FROM users WHERE role = 'donor' AND status = 'approved'";
    
    // Optional: Filter by blood group if provided in GET request
    if (isset($_GET['blood_group']) && !empty($_GET['blood_group'])) {
        $bg = mysqli_real_escape_string($conn, $_GET['blood_group']);
        $sql .= " AND blood_group = '$bg'";
    }

    $result = mysqli_query($conn, $sql);
    
    if (!$result) {
        throw new Exception(mysqli_error($conn));
    }

    $donors = [];
    while ($row = mysqli_fetch_assoc($result)) {
        // If blood_group is missing (legacy users), maybe label as "Unknown" or hide? 
        // We'll return them but frontend can handle "Unknown".
        if (!$row['blood_group']) {
            $row['blood_group'] = 'Unknown';
        }
        $donors[] = $row;
    }

    echo json_encode(['success' => true, 'donors' => $donors]);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error fetching donors: ' . $e->getMessage()]);
}

mysqli_close($conn);
?>
