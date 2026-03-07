<?php
require_once 'DatabaseConnection.php';
require_once 'session_helper.php';

if (!isLoggedIn()) {
    header("Location: SignIn.html");
    exit();
}

$buyer_id = $_SESSION['student_number']; // Standardized to student_number

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullName = mysqli_real_escape_string($conn, $_POST['fullName']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    
    // In a real app we'd process payment here. For this project we just validate field presence.
    $cardNumber = $_POST['cardNumber'];
    $expiry = $_POST['expiry'];
    $cvv = $_POST['cvv'];

    // Start Transaction
    mysqli_begin_transaction($conn);

    try {
        // 1. Calculate Total from Cart
        $totalQuery = "
            SELECT SUM(p.price * ci.quantity) as total 
            FROM cart_items ci
            JOIN products p ON ci.product_id = p.id
            WHERE ci.buyer_student_number = '$buyer_id'
        ";
        $totalRes = mysqli_query($conn, $totalQuery);
        $totalRow = mysqli_fetch_assoc($totalRes);
        $totalAmount = $totalRow['total'] ?? 0;

        if ($totalAmount <= 0) {
            throw new Exception("Your cart is empty.");
        }

        // 2. Create Order
        $orderQuery = "INSERT INTO orders (buyer_student_number, shipping_address, total_amount, status) 
                       VALUES ('$buyer_id', '$address', '$totalAmount', 'paid')";
        if (!mysqli_query($conn, $orderQuery)) {
            throw new Exception("Error creating order: " . mysqli_error($conn));
        }
        $orderId = mysqli_insert_id($conn);

        // 3. Move Cart Items to Order Items
        $itemsQuery = "
            SELECT ci.product_id, ci.quantity, p.price 
            FROM cart_items ci
            JOIN products p ON ci.product_id = p.id
            WHERE ci.buyer_student_number = '$buyer_id'
        ";
        $itemsRes = mysqli_query($conn, $itemsQuery);
        
        while ($item = mysqli_fetch_assoc($itemsRes)) {
            $prodId = $item['product_id'];
            $qty = $item['quantity'];
            $price = $item['price'];
            
            $itemInsert = "INSERT INTO order_items (order_id, product_id, quantity, price_at_purchase) 
                           VALUES ('$orderId', '$prodId', '$qty', '$price')";
            if (!mysqli_query($conn, $itemInsert)) {
                throw new Exception("Error adding order items: " . mysqli_error($conn));
            }
        }

        // 4. Clear Cart
        $clearCart = "DELETE FROM cart_items WHERE buyer_student_number = '$buyer_id'";
        if (!mysqli_query($conn, $clearCart)) {
            throw new Exception("Error clearing cart: " . mysqli_error($conn));
        }

        mysqli_commit($conn);

        // Success View
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <title>Order Successful - Raga</title>
            <link rel="stylesheet" href="mainCss.css">
            <style>
                .success-container {
                    max-width: 600px;
                    margin: 80px auto;
                    text-align: center;
                    padding: 40px;
                    background: white;
                    border-radius: 12px;
                    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
                }
                .success-icon { font-size: 80px; color: #4CAF50; margin-bottom: 20px; }
                h1 { color: #7e3285; }
                .btn {
                    display: inline-block;
                    margin-top: 25px;
                    padding: 12px 25px;
                    background: #ffaa50;
                    color: white;
                    text-decoration: none;
                    border-radius: 6px;
                    font-weight: bold;
                }
            </style>
        </head>
        <body>
            <?php include 'navbar.php'; ?>
            <div class="success-container">
                <div class="success-icon">✓</div>
                <h1>Order Successful!</h1>
                <p>Thank you, <?php echo htmlspecialchars($fullName); ?>. Your payment has been processed.</p>
                <p>Order ID: #<?php echo $orderId; ?></p>
                <p>A confirmation has been sent to <?php echo htmlspecialchars($email); ?>.</p>
                <a href="Explore.php" class="btn">Continue Shopping</a>
            </div>
        </body>
        </html>
        <?php

    } catch (Exception $e) {
        mysqli_rollback($conn);
        echo "<h1>Order Failed</h1>";
        echo "<p>Error: " . $e->getMessage() . "</p>";
        echo "<p><a href='Checkout.php'>Go back and try again</a></p>";
    }
} else {
    header("Location: Checkout.php");
    exit();
}
?>
