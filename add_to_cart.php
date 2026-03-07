<?php
require_once 'DatabaseConnection.php';
require_once 'session_helper.php';

if (!isLoggedIn()) {
    header("Location: SignIn.html?error=Please login to add items to cart");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'])) {
    $productId = mysqli_real_escape_string($conn, $_POST['product_id']);
    
    // For now, we assume user_id in session is the student number if they are buyer/both
    // If they are only a seller, they can't buy yet (no student number mapped)
    if (getUserType() === 'seller') {
        die("<h1>Access Denied</h1><p>Only buyers can add items to cart. Please register a student account.</p><p><a href='Explore.php'>Go back</a></p>");
    }

    // Fix: We use student_number specifically to avoid FK errors (especially for dual-role users)
    $buyerStudentNumber = $_SESSION['student_number'];
    $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;

    // Check if item already in cart
    $checkQuery = "SELECT * FROM cart_items WHERE buyer_student_number = '$buyerStudentNumber' AND product_id = '$productId'";
    $checkResult = mysqli_query($conn, $checkQuery);

    if (mysqli_num_rows($checkResult) > 0) {
        // Update quantity
        $updateQuery = "UPDATE cart_items SET quantity = quantity + $quantity WHERE buyer_student_number = '$buyerStudentNumber' AND product_id = '$productId'";
        mysqli_query($conn, $updateQuery);
    } else {
        // Insert new item
        $insertQuery = "INSERT INTO cart_items (buyer_student_number, product_id, quantity) VALUES ('$buyerStudentNumber', '$productId', $quantity)";
        mysqli_query($conn, $insertQuery);
    }

    header("Location: Cart.php");
    exit();
} else {
    header("Location: Explore.php");
    exit();
}
?>
