<?php
/**
 * Checkout and Payment Simulation - Minimal Editorial Style
 * Handles the final step of the purchasing process for buyers.
 */
include 'includes/header.php';

// Check if user is logged in as a buyer
if (!$isLoggedIn || $userRole !== 'buyer') {
    header("Location: login.php");
    exit();
}

$buyer_id = $_SESSION['user_id'];
$error = "";
$success = "";

// Get user's current address for the default shipping field
$user_sql = mysqli_query($conn, "SELECT address FROM buyers WHERE student_number = '$buyer_id'");
$user_data = mysqli_fetch_assoc($user_sql);
$default_address = $user_data['address'] ?? "";

// Calculate Total from the cart
$cart_sql = "SELECT c.*, p.title, p.price, p.stock_quantity FROM cart_items c JOIN products p ON c.product_id = p.id WHERE c.buyer_student_number = '$buyer_id'";
$cart_res = mysqli_query($conn, $cart_sql);
$grand_total = 0;
$items = [];

while($row = mysqli_fetch_assoc($cart_res)) {
    $grand_total += ($row['price'] * $row['quantity']);
    $items[] = $row;
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['complete_purchase'])) {
    $shipping_address = sanitize($conn, $_POST['shipping_address']);
    $card_number = sanitize($conn, $_POST['card_number']); // Simulation only
    
    if (empty($shipping_address) || empty($card_number)) {
        $error = "MISSING INFO-";
    } else if (count($items) == 0) {
        $error = "POUCH EMPTY-";
    } else {
        // Start Order Processing Transaction
        mysqli_begin_transaction($conn);
        
        try {
            // 1. Create the Order Record
            $order_sql = "INSERT INTO orders (buyer_student_number, total_amount, status, shipping_address) 
                          VALUES ('$buyer_id', $grand_total, 'Pending', '$shipping_address')";
            mysqli_query($conn, $order_sql);
            $order_id = mysqli_insert_id($conn);
            
            // 2. Move items to order_items & Update stock
            foreach($items as $item) {
                $p_id = $item['product_id'];
                $qty = $item['quantity'];
                $price = $item['price'];
                
                $oi_sql = "INSERT INTO order_items (order_id, product_id, quantity, price_at_purchase) 
                           VALUES ($order_id, $p_id, $qty, $price)";
                mysqli_query($conn, $oi_sql);
                
                $update_stock = "UPDATE products SET stock_quantity = stock_quantity - $qty WHERE id = $p_id";
                mysqli_query($conn, $update_stock);
            }
            
            // 3. Clear cart
            mysqli_query($conn, "DELETE FROM cart_items WHERE buyer_student_number = '$buyer_id'");
            
            mysqli_commit($conn);
            $success = "ORDER COMMITTED- ID #$order_id";
            $items = []; 
            $grand_total = 0;
            
        } catch (Exception $e) {
            mysqli_rollback($conn);
            $error = "SYSTEM FAIL-";
        }
    }
}
?>

<div style="padding: 4% 8%;">
    <div style="margin-bottom: 4rem; border-bottom: 1px solid black; padding-bottom: 2rem;">
        <h1 style="font-size: 3.5rem; letter-spacing: -3px;">CHECKOUT-</h1>
        <p style="font-size: 0.7rem; letter-spacing: 2px; color: #888; font-weight: 700;">SECURE PAYMENT & DELIVERY-</p>
    </div>

    <?php if ($error): ?>
        <div style="margin-bottom: 2rem; border: 1px solid black; padding: 1rem; text-align: center; font-weight: 700; color: red;">
            <?php echo $error; ?>
        </div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div style="margin-bottom: 2rem; border: 1px solid black; padding: 3rem; text-align: center;">
            <h2 style="font-size: 2rem; margin-bottom: 1.5rem;"><?php echo $success; ?></h2>
            <p style="margin-bottom: 2rem;">YOUR ORDER HAS BEEN RECEIVED BY OUR CURATORS-</p>
            <a href="orders.php" class="btn" style="border-radius: 50px;">VIEW HISTORY-</a>
        </div>
    <?php else: ?>
        <div style="display: grid; grid-template-columns: 1.5fr 1fr; gap: 4rem;">
            <div>
                <form method="POST">
                    <div style="margin-bottom: 3rem;">
                        <h3 style="font-size: 0.8rem; font-weight: 900; margin-bottom: 1.5rem; letter-spacing: 1px;">DELIVERY ADDRESS-</h3>
                        <div class="form-group">
                            <textarea name="shipping_address" rows="3" placeholder="WHERE SHOULD WE SEND THIS?-" required style="width: 100%; border: none; border-bottom: 1px solid black; padding: 1rem 0; font-size: 1rem; outline: none; background: transparent;"><?php echo htmlspecialchars($default_address); ?></textarea>
                        </div>
                    </div>

                    <div style="margin-bottom: 3rem;">
                        <h3 style="font-size: 0.8rem; font-weight: 900; margin-bottom: 1.5rem; letter-spacing: 1px;">PAYMENT DETAILS-</h3>
                        <div class="form-group">
                            <input type="text" placeholder="NAME ON CARD-" required style="width: 100%; border: none; border-bottom: 1px solid black; padding: 1rem 0; font-size: 1rem; outline: none;">
                        </div>
                        <div class="form-group">
                            <input type="text" name="card_number" placeholder="CARD NUMBER (16 DIGITS)-" maxlength="16" required style="width: 100%; border: none; border-bottom: 1px solid black; padding: 1rem 0; font-size: 1rem; outline: none;">
                        </div>
                        <div style="display: flex; gap: 2rem;">
                            <div class="form-group" style="flex: 1;">
                                <input type="text" placeholder="EXP (MM/YY)-" maxlength="5" required style="width: 100%; border: none; border-bottom: 1px solid black; padding: 1rem 0; font-size: 1rem; outline: none;">
                            </div>
                            <div class="form-group" style="flex: 1;">
                                <input type="password" placeholder="CVV-" maxlength="3" required style="width: 100%; border: none; border-bottom: 1px solid black; padding: 1rem 0; font-size: 1rem; outline: none;">
                            </div>
                        </div>
                    </div>

                    <button type="submit" name="complete_purchase" class="btn btn-primary" style="width: 100%; padding: 1.5rem; font-size: 1.1rem; font-weight: 900; background: black; color: white;">AUTHORIZE PAYMENT (R<?php echo number_format($grand_total, 2); ?>)-</button>
                </form>
            </div>

            <div style="border-left: 1px solid black; padding-left: 4rem;">
                <h3 style="font-size: 0.8rem; font-weight: 900; margin-bottom: 2rem; letter-spacing: 1px;">ORDER SUMMARY-</h3>
                <div style="display: flex; flex-direction: column; gap: 1.5rem;">
                    <?php foreach($items as $item): ?>
                        <div style="display: flex; justify-content: space-between; align-items: baseline;">
                            <p style="font-size: 0.9rem; font-weight: 700;"><?php echo $item['quantity']; ?>X <?php echo strtoupper($item['title']); ?></p>
                            <span style="font-size: 0.9rem; font-weight: 300;">R<?php echo number_format($item['price'] * $item['quantity'], 2); ?>-</span>
                        </div>
                    <?php endforeach; ?>
                    
                    <div style="border-top: 1px solid black; margin-top: 2rem; padding-top: 2rem; display: flex; justify-content: space-between; align-items: baseline;">
                        <p style="font-size: 1.2rem; font-weight: 900;">TOTAL-</p>
                        <span style="font-size: 1.5rem; font-weight: 300;">R<?php echo number_format($grand_total, 2); ?>-</span>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
