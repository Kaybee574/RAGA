<?php
// Database variables
$host = 'CS3-DEV.ICT.RU.AC.ZA';
$dbname = 'group8'; // Change this to your database name
$username = 'G23M3498'; // Change this to your database username
$password = 'JusMud23!'; // Change this to your database password

// Create connection
$conn = mysqli_connect($host, $dbname, $username, $password);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
// Optional: echo "Connected successfully";
echo("successful");
?>
