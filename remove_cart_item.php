<?php
require_once 'DatabaseConnection.php';
require_once 'session_helper.php';

if (!isLoggedIn()) {
    header("Location: SignIn.html");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cart_item_id'])) {
    $cartItemId = mysqli_real_escape_string($conn, $_POST['cart_item_id']);
    $buyerId = $_SESSION['user_id'];

    $query = "DELETE FROM cart_items WHERE id = '$cartItemId' AND buyer_student_number = '$buyerId'";
    mysqli_query($conn, $query);
}

header("Location: Cart.php");
exit();
?>
