<?php
// Database configuration
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root'); // Default XAMPP username
define('DB_PASSWORD', ''); // Default XAMPP password
define('DB_NAME', 'inventory_db2');

// Attempt to connect to the MySQL database
$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Check the connection
if ($conn->connect_error) {
    die("ERROR: Could not connect. " . $conn->connect_error);
}
?>
