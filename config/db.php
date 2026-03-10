<?php
// Database connection configuration for Raga Marketplace
// Host: CS3-DEV server at Rhodes University
$host = 'CS3-DEV.ICT.RU.AC.ZA';
$dbname = 'group8';
$username = 'G23M3498';
$password = 'JusMud23!';

/**
 * Establishment of connection to the MySQL database
 * Note: Order of parameters is crucial - (host, user, password, database)
 */
$conn = mysqli_connect($host, $username, $password, $dbname);

// Error handling for database connection failures
if (!$conn) {
    die("CRITICAL ERROR: Connection to database failed. " . mysqli_connect_error());
}

// Ensure the connection uses UTF-8 encoding for specialized characters
mysqli_set_charset($conn, "utf8mb4");

// Function to safely handle input and prevent SQL injection/XSS issues
function sanitize($conn, $data) {
    return mysqli_real_escape_string($conn, htmlspecialchars(strip_tags(trim($data))));
}
?>
