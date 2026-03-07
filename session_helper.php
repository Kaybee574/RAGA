<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Returns the type: 'buyer', 'seller', or 'both'
function getUserType() {
    return $_SESSION['user_type'] ?? null;
}

// Returns the display name
function getUserName() {
    return $_SESSION['name'] ?? 'Guest';
}
