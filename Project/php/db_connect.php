<?php
/**
 * Database Connection File
 * 
 * This file establishes a connection to the MySQL database using the 
 * procedural approach (mysqli_connect). This is a beginner-friendly 
 * way to handle database interactions in PHP.
 */

// 1. Declare database connection details
// For local development (e.g., using XAMPP or WAMP), the defaults are usually:
$servername = "localhost"; // The server where your database is hosted
$username   = "root";      // The default username for MySQL
$password   = "";          // The default password (usually empty)
$dbname     = "bloodlink_db"; // The name of your database

// 2. Create the connection
// We use mysqli_connect() which takes the four variables we defined above
$conn = mysqli_connect($servername, $username, $password, $dbname);

// 3. Check if the connection was successful
// If the connection fails, $conn will be false
if (!$conn) {
    // die() stops the execution of the script and shows an error message
    // mysqli_connect_error() tells us exactly what went wrong
    die("Connection failed: " . mysqli_connect_error());
}

// If we reach this point, the connection was successful!
// echo "Connected successfully to the database!"; 

// Note: In a real application, you would include this file in other scripts 
// using: include('db_connect.php');
?>
