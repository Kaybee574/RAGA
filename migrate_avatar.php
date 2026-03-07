<?php
require_once 'DatabaseConnection.php';

echo "Starting database migration...<br>";

// Add avatar_url to sellers
$sql = "ALTER TABLE sellers ADD COLUMN avatar_url VARCHAR(255) DEFAULT 'Explore images/default_avatar.png' AFTER address";
if (mysqli_query($conn, $sql)) {
    echo "Added avatar_url to sellers table.<br>";
} else {
    echo "Error updating sellers table: " . mysqli_error($conn) . "<br>";
}

// Add avatar_url to buyers
$sql = "ALTER TABLE buyers ADD COLUMN avatar_url VARCHAR(255) DEFAULT 'Explore images/default_avatar.png' AFTER address";
if (mysqli_query($conn, $sql)) {
    echo "Added avatar_url to buyers table.<br>";
} else {
    echo "Error updating buyers table: " . mysqli_error($conn) . "<br>";
}

echo "Migration completed.";
?>
