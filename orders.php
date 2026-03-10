<?php
/**
 * Order History View - Minimal Editorial Style
 * Displays past and pending orders placed by the currently logged-in buyer.
 */
include 'includes/header.php';

// Ensure user is authorized
if (!$isLoggedIn || $userRole !== 'buyer') {
    header("Location: login.php");
    exit();
}

$buyer_id = $_SESSION['user_id'];

// Retrieve all customer orders sorted by newest/most recent
$sql = "SELECT * FROM orders WHERE buyer_student_number = '$buyer_id' ORDER BY created_at DESC";
$result = mysqli_query($conn, $sql);
?>

<div style="padding: 4% 8%;">
    <div style="margin-bottom: 4rem; border-bottom: 1px solid black; padding-bottom: 2rem;">
        <h1 style="font-size: 3.5rem; letter-spacing: -3px;">HISTORY-</h1>
        <p style="font-size: 0.7rem; letter-spacing: 2px; color: #888; font-weight: 700;">YOUR RAGA- TRANSACTION LOG-</p>
    </div>

    <?php if (mysqli_num_rows($result) > 0): ?>
        <div style="display: flex; flex-direction: column; gap: 4rem;">
            <?php while($order = mysqli_fetch_assoc($result)): 
                $order_id = $order['id'];
                // For each order, find its detailed items
                $item_sql = "SELECT oi.*, p.title FROM order_items oi JOIN products p ON oi.product_id = p.id WHERE oi.order_id = $order_id";
                $item_res = mysqli_query($conn, $item_sql);
            ?>
                <div style="border: 1px solid black; padding: 3rem; background: #fff;">
                    <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 2rem; border-bottom: 1px solid #eee; padding-bottom: 1rem;">
                        <div>
                            <p style="font-size: 0.75rem; font-weight: 900; letter-spacing: 1px;">ORDER #<?php echo $order_id; ?>-</p>
                            <p style="font-size: 0.75rem; color: #888;"><?php echo date("d.m.Y / H:i", strtotime($order['created_at'])); ?></p>
                        </div>
                        <div style="text-align: right;">
                            <span style="font-size: 0.6rem; font-weight: 900; background: #000; color: #fff; padding: 0.3rem 0.6rem; border-radius: 50px;"><?php echo strtoupper($order['status']); ?></span>
                            <p style="font-size: 1.5rem; font-weight: 300; margin-top: 0.5rem;">R<?php echo number_format($order['total_amount'], 2); ?>-</p>
                        </div>
                    </div>
                    
                    <div style="margin-bottom: 2rem;">
                        <h4 style="font-size: 0.7rem; font-weight: 900; color: #888; margin-bottom: 1rem; letter-spacing: 1px;">ITEMS CONFIGURED-</h4>
                        <div style="display: flex; flex-direction: column; gap: 1rem;">
                            <?php while($item = mysqli_fetch_assoc($item_res)): ?>
                                <div style="display: flex; justify-content: space-between; align-items: baseline;">
                                    <p style="font-weight: 700; font-size: 1rem;"><?php echo $item['quantity']; ?>X <?php echo strtoupper($item['title']); ?></p>
                                    <span style="font-size: 0.85rem; opacity: 0.5;">R<?php echo number_format($item['price_at_purchase'], 2); ?>- EA</span>
                                </div>
                            <?php endwhile; ?>
                        </div>
                    </div>
                    
                    <div style="padding-top: 1.5rem; border-top: 1px solid #eee;">
                        <p style="font-size: 0.7rem; font-weight: 900; color: #888; margin-bottom: 0.5rem; letter-spacing: 1px;">SHIPPING DIRECTIVE-</p>
                        <p style="font-size: 0.9rem; text-transform: none;"><?php echo htmlspecialchars($order['shipping_address']); ?></p>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <div style="text-align: center; padding: 10rem 2rem; border: 1px dashed #ccc;">
            <p style="font-size: 0.8rem; font-weight: 700; color: #888;">THE ARCHIVE IS EMPTY- NO ORDERS FOUND-</p>
            <p style="margin-top: 2rem;"><a href="products.php" class="btn btn-primary">GO SHOP-</a></p>
        </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
