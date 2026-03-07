<?php
require_once 'DatabaseConnection.php';
require_once 'session_helper.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $loginId = mysqli_real_escape_string($conn, $_POST['login_id']);
    $password = $_POST['password'];

    // 1. Check if it's a buyer
    $queryBuyer = "SELECT * FROM buyers WHERE student_number = '$loginId'";
    $resultBuyer = mysqli_query($conn, $queryBuyer);
    $buyer = mysqli_fetch_assoc($resultBuyer);

    if ($buyer && password_verify($password, $buyer['password'])) {
        // Logged in as Buyer
        $_SESSION['user_id'] = $buyer['student_number'];
        $_SESSION['student_number'] = $buyer['student_number'];
        $_SESSION['user_type'] = 'buyer'; // Default to buyer
        $_SESSION['name'] = $buyer['full_name'];
        $_SESSION['avatar_url'] = $buyer['avatar_url'];

        // Dual Role Management: Check if the buyer also exists as a seller
        $res = mysqli_query($conn, "SELECT email FROM sellers WHERE full_name = '" . mysqli_real_escape_string($conn, $buyer['full_name']) . "'");
        if ($seller_row = mysqli_fetch_assoc($res)) {
            $_SESSION['user_type'] = 'both'; // Update user type to 'both' if they are also a seller
            $_SESSION['email'] = $seller_row['email']; // Store seller's email for dual role
        }

        header("Location: BuyerDashboard.php");
        exit();
    }

    // 2. Check if it's a seller
    $querySeller = "SELECT * FROM sellers WHERE email = '$loginId'";
    $resultSeller = mysqli_query($conn, $querySeller);
    $seller = mysqli_fetch_assoc($resultSeller);

    if ($seller && password_verify($password, $seller['password'])) {
        // Logged in as Seller
        $_SESSION['user_id'] = $seller['email'];
        $_SESSION['email'] = $seller['email'];
        $_SESSION['user_type'] = 'seller';
        $_SESSION['name'] = $seller['full_name'];
        $_SESSION['avatar_url'] = $seller['avatar_url'];

        // Check if they also exist as a buyer
        $res = mysqli_query($conn, "SELECT student_number FROM buyers WHERE full_name = '" . mysqli_real_escape_string($conn, $seller['full_name']) . "'");
        if ($buyer_row = mysqli_fetch_assoc($res)) {
            $_SESSION['user_type'] = 'both';
            $_SESSION['student_number'] = $buyer_row['student_number'];
        }
        
        // Redirect logic
        if ($_SESSION['user_type'] === 'both') {
            header("Location: BuyerDashboard.php"); // Or SellerDashboard.php, but consistent landing
        } else {
            header("Location: SellerDashboard.php");
        }
        exit();
    }

    // If we get here, login failed
    echo "<h1>Login Failed</h1>";
    echo "<p>Invalid email, student number, or password. <a href='SignIn.html'>Try again</a></p>";
} else {
    header("Location: SignIn.html");
    exit();
}
