<?php
// File: includes/db.php
$servername = "localhost";
$username = "root";
$password = ""; // Default for XAMPP
$database = "trading_card_library";

// Create connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
