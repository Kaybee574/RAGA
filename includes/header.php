<?php
/**
 * Global Header - Minimal Editorial Style
 * Handles session initiation, user authentication status, and navigation menu.
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Use absolute path to prevent 'file not found' when included from subfolders
require_once __DIR__ . '/../config/db.php';

// Check if user is logged in and what their role is (Buyer or Seller)
$isLoggedIn = isset($_SESSION['user_id']);
$userRole = $_SESSION['user_role'] ?? null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Raga | Minimalist Campus Marketplace</title>
    <!-- Descriptive Title Tag for SEO -->
    <meta name="description" content="Raga - The best online marketplace replica for buying and selling items safely.">
    <!-- Main Style Link -->
    <link rel="stylesheet" href="assets/css/style.css">
    <!-- Custom Style Overrides for specific page layouts -->
    <style>
        .container-fluid {
            width: 100%;
            margin: 0;
            padding: 0;
        }
        .main-wrapper {
            margin: 0 auto;
        }
    </style>
</head>
<body>
    <header>
        <nav>
            <div class="logo" style="flex: 1;">
                <a href="index.php">Raga-</a>
            </div>
            
            <div class="nav-links" style="flex: 1; justify-content: center;">
                <a href="products.php">Products</a>
                <a href="products.php?category=tech">Tech</a>
                <a href="products.php?category=home">Home</a>
                <a href="about.php">About</a>
            </div>

            <div class="nav-links" style="flex: 1; justify-content: flex-end;">
                <?php if ($isLoggedIn): ?>
                    <?php if ($userRole === 'buyer'): ?>
                        <a href="cart.php">cart</a>
                        <a href="buyer/dashboard.php">User</a>
                    <?php
    elseif ($userRole === 'seller'): ?>
                        <a href="seller/dashboard.php">Store</a>
                    <?php
    endif; ?>
                    <a href="logout.php">Log Out</a>
                <?php
else: ?>
                    <a href="login.php">Log In</a>
                    <a href="register.php" class="btn btn-primary" style="padding: 0.5rem 1.5rem; font-size: 0.6rem;">Join-</a>
                <?php
endif; ?>
            </div>

        </nav>
    </header>
    <main class="main-wrapper">
