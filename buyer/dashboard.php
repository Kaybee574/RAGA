<?php
/**
 * Buyer Dashboard - Minimal Editorial Style
 * Provides users with an overview of their account, recent orders, and cart status.
 */
include '../includes/header.php';

// Authorization: Ensure user is a buyer
if (!$isLoggedIn || $userRole !== 'buyer') {
    header("Location: ../login.php");
    exit();
}

$buyer_id = $_SESSION['user_id'];

// Get aggregate stats
$cart_count_res = mysqli_query($conn, "SELECT SUM(quantity) as total FROM cart_items WHERE buyer_student_number = '$buyer_id'");
$cart_count = mysqli_fetch_assoc($cart_count_res)['total'] ?? 0;

$order_count_res = mysqli_query($conn, "SELECT COUNT(*) as total FROM orders WHERE buyer_student_number = '$buyer_id'");
$order_count = mysqli_fetch_assoc($order_count_res)['total'] ?? 0;

// Fetch recent orders
$orders_sql = "SELECT * FROM orders WHERE buyer_student_number = '$buyer_id' ORDER BY created_at DESC LIMIT 3";
$orders_res = mysqli_query($conn, $orders_sql);
?>

<div style="padding: 4% 8%;">
    <div style="margin-bottom: 4rem;">
        <h1 style="font-size: 3.5rem; letter-spacing: -2px;">USER- OVERVIEW</h1>
        <p style="font-size: 0.8rem; letter-spacing: 2px; color: #888;">WELCOME BACK, <?php echo strtoupper($_SESSION['user_name']); ?></p>
    </div>

    <!-- Stats Grid -->
    <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 2rem; margin-bottom: 4rem;">
        <div style="border: 1px solid black; padding: 3rem; text-align: center;">
            <p style="font-size: 0.7rem; font-weight: 700; margin-bottom: 1rem;">POUCH ITEMS-</p>
            <h2 style="font-size: 3rem;"><?php echo $cart_count; ?></h2>
            <a href="../cart.php" class="btn" style="margin-top: 2rem; border-radius: 50px;">VIEW POUCH-</a>
        </div>
        <div style="border: 1px solid black; padding: 3rem; text-align: center;">
            <p style="font-size: 0.7rem; font-weight: 700; margin-bottom: 1rem;">TOTAL ORDERS-</p>
            <h2 style="font-size: 3rem;"><?php echo $order_count; ?></h2>
            <a href="../orders.php" class="btn" style="margin-top: 2rem; border-radius: 50px;">HISTORY-</a>
        </div>
    </div>

    <!-- Recent Activity -->
    <div>
        <h3 style="font-size: 1.5rem; margin-bottom: 2rem; font-weight: 300;">RECENT ORDERS-</h3>
        <?php if (mysqli_num_rows($orders_res) > 0): ?>
            <div style="border-top: 1px solid black;">
                <?php while($order = mysqli_fetch_assoc($orders_res)): ?>
                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 2rem 0; border-bottom: 1px solid black;">
                        <div>
                            <p style="font-weight: 700;">#<?php echo $order['id']; ?></p>
                            <p style="font-size: 0.8rem; color: #888;"><?php echo date("d.m.Y", strtotime($order['created_at'])); ?></p>
                        </div>
                        <div style="text-align: right;">
                            <p style="font-weight: 700;">R<?php echo number_format($order['total_amount'], 2); ?></p>
                            <p style="font-size: 0.7rem; font-weight: 900;"><?php echo strtoupper($order['status']); ?></p>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
            <div style="margin-top: 2rem; text-align: right;">
                <a href="../orders.php" style="color: black; font-weight: 700; font-size: 0.8rem; text-decoration: none; border-bottom: 1px solid black;">VIEW ALL HISTORY-</a>
            </div>
        <?php else: ?>
            <p style="padding: 4rem; text-align: center; border: 1px dashed #ccc;">NO RECENT ACTIVITY FOUND-</p>
        <?php endif; ?>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
