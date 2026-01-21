<?php
require_once 'db_connect.php';

// 1. Drop the incorrect table
$sql_drop = "DROP TABLE IF EXISTS blood_requests";
if (mysqli_query($conn, $sql_drop)) {
    echo "Old table dropped.<br>";
} else {
    echo "Error dropping table: " . mysqli_error($conn) . "<br>";
}

// 2. Create the table correctly
$sql_create = "CREATE TABLE blood_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    blood_group VARCHAR(10) NOT NULL,
    hospital VARCHAR(255) NOT NULL,
    location TEXT NOT NULL,
    contact1 VARCHAR(20) NOT NULL,
    contact2 VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
)";

if (mysqli_query($conn, $sql_create)) {
    echo "Table 'blood_requests' created successfully with 'user_id' column.";
} else {
    echo "Error creating table: " . mysqli_error($conn);
}

mysqli_close($conn);
?>
