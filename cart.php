<?php
/**
 * Shopping Cart View - Minimal Editorial Style
 * Displays items selected by the buyer, calculates total costs, and provides checkout options.
 */
include 'includes/header.php';

// Check if user is logged in as a buyer
if (!$isLoggedIn || $userRole !== 'buyer') {
    header("Location: login.php");
    exit();
}

$buyer_id = $_SESSION['user_id'];
$message = "";

// Handle Cart Updates or Deletions
if (isset($_GET['action']) && isset($_GET['item_id'])) {
    $item_id = intval($_GET['item_id']);
    if ($_GET['action'] == 'remove') {
        mysqli_query($conn, "DELETE FROM cart_items WHERE id = $item_id AND buyer_student_number = '$buyer_id'");
        $message = "ITEM PURGED-";
    }
}

// Fetch Cart Items with Product Details including store_name from updated schema
$sql = "SELECT c.*, p.title, p.price, p.image_url, s.store_name, s.location 
        FROM cart_items c 
        JOIN products p ON c.product_id = p.id 
        JOIN stores s ON p.store_id = s.id 
        WHERE c.buyer_student_number = '$buyer_id' 
        ORDER BY c.added_at DESC";

$result = mysqli_query($conn, $sql);
$total = 0;
?>

<div style="padding: 4% 8%;">
    <div style="margin-bottom: 4rem; border-bottom: 1px solid black; padding-bottom: 2rem; display: flex; justify-content: space-between; align-items: flex-end;">
        <div>
            <h1 style="font-size: 3.5rem; letter-spacing: -3px;">YOUR POUCH-</h1>
            <p style="font-size: 0.7rem; letter-spacing: 2px; color: #888; font-weight: 700;">ITEMS SELECTED FOR ACQUISITION-</p>
        </div>
        <a href="products.php" class="btn" style="border-radius: 50px; font-size: 0.7rem;">CONTINUE-</a>
    </div>

    <?php if ($message): ?>
        <div style="margin-bottom: 2rem; border: 1px solid black; padding: 1rem; text-align: center; font-weight: 700;">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>

    <div class="cart-container">
        <?php if (mysqli_num_rows($result) > 0): ?>
            <div style="display: flex; flex-direction: column; gap: 2rem;">
                <?php while($item = mysqli_fetch_assoc($result)): 
                    $subtotal = $item['price'] * $item['quantity'];
                    $total += $subtotal;
                ?>
                    <div style="display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid #eee; padding-bottom: 2rem;">
                        <div style="display: flex; align-items: center; gap: 2.5rem;">
                            <div style="width: 120px; height: 120px; border: 1px solid black; overflow: hidden;">
                                <img src="<?php echo !empty($item['image_url']) ? $item['image_url'] : 'https://images.unsplash.com/photo-1516259762381-22954d7d3ad2?q=80&w=800'; ?>" 
                                     alt="<?php echo htmlspecialchars($item['title']); ?>" 
                                     style="width: 100%; height: 100%; object-fit: cover;"
                                     onerror="this.src='https://images.unsplash.com/photo-1516259762381-22954d7d3ad2?q=80&w=800';">
                            </div>
                            <div>
                                <h3 style="font-size: 1.5rem; letter-spacing: -1px;"><?php echo strtoupper($item['title']); ?>-</h3>
                                <p style="font-size: 0.7rem; font-weight: 700; color: #888; margin-top: 0.5rem;"><?php echo strtoupper($item['store_name']); ?> / <?php echo strtoupper($item['location']); ?></p>
                                <p style="font-size: 0.8rem; font-weight: 900; margin-top: 1rem; color: #333;">QTY: <?php echo $item['quantity']; ?></p>
                            </div>
                        </div>
                        
                        <div style="text-align: right;">
                            <p style="font-size: 1.5rem; font-weight: 300; margin-bottom: 1rem;">R<?php echo number_format($subtotal, 2); ?>-</p>
                            <a href="cart.php?action=remove&item_id=<?php echo $item['id']; ?>" 
                               style="color: red; font-size: 0.7rem; font-weight: 900; letter-spacing: 1px;" 
                               onclick="return confirm('PURGE ITEM?');">REMOVE-</a>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>

            <div style="margin-top: 6rem; padding: 4rem; border: 1px solid black; text-align: right;">
                <p style="font-size: 0.8rem; font-weight: 900; color: #888; margin-bottom: 1rem;">ESTIMATED TOTAL-</p>
                <h2 style="font-size: 4rem; margin-bottom: 3rem;">R<?php echo number_format($total, 2); ?>-</h2>
                <a href="checkout.php" class="btn btn-primary" style="padding: 1.5rem 4rem; font-size: 1rem; width: 100%; display: block;">PROCEED TO AUTHORIZATION-</a>
            </div>

        <?php else: ?>
            <div style="text-align: center; padding: 10rem 2rem; border: 1px dashed black;">
                <h3 style="font-weight: 300; font-size: 1.5rem;">THE POUCH IS UNSTOCKED-</h3>
                <p style="margin: 2rem 0; color: #888; font-size: 0.8rem; letter-spacing: 1px;">BROWSE THE CATALOG TO SELECT ITEMS-</p>
                <a href="products.php" class="btn btn-primary" style="padding: 1rem 3rem;">DISCOVER-</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
