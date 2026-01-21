<?php
require_once 'db_connect.php';

// Add blood_group column to users table
$sql = "ALTER TABLE users ADD COLUMN blood_group VARCHAR(10) NULL AFTER phone";

if (mysqli_query($conn, $sql)) {
    echo "Column 'blood_group' added successfully.";
} else {
    // Ignore if already exists (error 1060)
    if (mysqli_errno($conn) == 1060) {
        echo "Column 'blood_group' already exists.";
    } else {
        echo "Error adding column: " . mysqli_error($conn);
    }
}

mysqli_close($conn);
?>
