<?php
require_once 'DatabaseConnection.php';
require_once 'session_helper.php';

if (!isLoggedIn()) {
    header("Location: SignIn.html");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cart_item_id'], $_POST['quantity'])) {
    $cartItemId = mysqli_real_escape_string($conn, $_POST['cart_item_id']);
    $quantity = (int)$_POST['quantity'];
    $buyerId = $_SESSION['user_id'];

    if ($quantity > 0) {
        $query = "UPDATE cart_items SET quantity = $quantity WHERE id = '$cartItemId' AND buyer_student_number = '$buyerId'";
        mysqli_query($conn, $query);
    }
}

header("Location: Cart.php");
exit();
?>
