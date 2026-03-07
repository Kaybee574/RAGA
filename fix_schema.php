<?php
require_once 'DatabaseConnection.php';

echo "<h1>Database Schema Fix</h1>";

$queries = [
    "ALTER TABLE sellers ADD COLUMN IF NOT EXISTS avatar_url VARCHAR(255) DEFAULT 'Explore images/default_avatar.png'",
    "ALTER TABLE buyers ADD COLUMN IF NOT EXISTS avatar_url VARCHAR(255) DEFAULT 'Explore images/default_avatar.png'"
];

foreach ($queries as $query) {
    if (mysqli_query($conn, $query)) {
        echo "<p style='color: green;'>Success: $query</p>";
    } else {
        // If IF NOT EXISTS is not supported by this version of MySQL/MariaDB
        if (strpos(mysqli_error($conn), "Duplicate column name") !== false) {
            echo "<p style='color: blue;'>Column already exists (skipped): $query</p>";
        } else {
            echo "<p style='color: red;'>Error: " . mysqli_error($conn) . " | Query: $query</p>";
        }
    }
}

echo "<p><a href='SellerDashboard.php'>Back to Dashboard</a></p>";
?>
