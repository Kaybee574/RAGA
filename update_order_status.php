<?php
require_once 'session_helper.php';
require_once 'DatabaseConnection.php';

if (!isLoggedIn() || (getUserType() !== 'seller' && getUserType() !== 'both')) {
    header("Location: SignIn.html");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $order_id = $_POST['order_id'];
    $new_status = $_POST['status'];

    // Update order status
    $query = "UPDATE orders SET status = '" . mysqli_real_escape_string($conn, $new_status) . "' WHERE id = " . intval($order_id);
    
    if (mysqli_query($conn, $query)) {
        header("Location: SellerDashboard.php#history");
    } else {
        echo "Error updating order: " . mysqli_error($conn);
    }
} else {
    header("Location: SellerDashboard.php");
}
?>
