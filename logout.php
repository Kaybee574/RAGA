<?php
/**
 * Logout Logic for Raga Marketplace
 * Clears and destroys the active session before redirecting.
 */
session_start();
session_unset();
session_destroy();

// Redirect user to the homepage after logging out successfully
header("Location: index.php");
exit();
?>
