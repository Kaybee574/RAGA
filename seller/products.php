<?php
/**
 * Seller Product Management - Minimal Editorial Style
 * View and delete products listed by the current seller.
 */
include '../includes/header.php';

// Authorization: Ensure user is a seller
if (!$isLoggedIn || $userRole !== 'seller') {
    header("Location: ../login.php");
    exit();
}

$seller_id = intval($_SESSION['user_id']); 

// Fetch store ID first
$store_res = mysqli_query($conn, "SELECT id, store_name FROM stores WHERE seller_id = $seller_id");
if (!$store = mysqli_fetch_assoc($store_res)) {
    header("Location: store_setup.php");
    exit();
}

$store_id = $store['id'];

// Handle Deletion logic
if (isset($_GET['delete_id'])) {
    $del_id = intval($_GET['delete_id']);
    // Ensure product belongs to this store before deleting
    mysqli_query($conn, "DELETE FROM products WHERE id = $del_id AND store_id = $store_id");
    header("Location: products.php");
    exit();
}

// Fetch all products for this seller
$products_sql = "SELECT * FROM products WHERE store_id = $store_id ORDER BY created_at DESC";
$products_res = mysqli_query($conn, $products_sql);
?>

<div style="padding: 4% 8%;">
    <!-- Sub-header -->
    <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 4rem; border-bottom: 1px solid black; padding-bottom: 2rem;">
        <div>
            <h1 style="font-size: 3.5rem; letter-spacing: -3px;"><?php echo strtoupper($store['store_name']); ?>-</h1>
            <p style="font-size: 0.7rem; letter-spacing: 2px; color: #888; font-weight: 700;">INVENTORY MANAGEMENT-</p>
        </div>
        <div style="display: flex; gap: 2rem; font-size: 0.7rem; font-weight: 700;">
            <a href="dashboard.php">OVERVIEW-</a>
            <a href="products.php" style="border-bottom: 1px solid black; padding-bottom: 4px;">INVENTORY-</a>
            <a href="add_product.php">LIST ITEM-</a>
        </div>
    </div>

    <!-- Product Grid UI -->
    <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 2rem;">
        <?php if (mysqli_num_rows($products_res) > 0): ?>
            <?php while($prod = mysqli_fetch_assoc($products_res)): ?>
                <div style="border: 1px solid black; overflow: hidden; height: auto;">
                    <div style="height: 400px; border-bottom: 1px solid black;">
                        <img src="<?php echo !empty($prod['image_url']) ? $prod['image_url'] : 'https://images.unsplash.com/photo-1516259762381-22954d7d3ad2?q=80&w=800'; ?>" 
                             alt="<?php echo htmlspecialchars($prod['title']); ?>" 
                             style="width: 100%; height: 100%; object-fit: cover;"
                             onerror="this.src='https://images.unsplash.com/photo-1516259762381-22954d7d3ad2?q=80&w=800';">
                    </div>
                    <div style="padding: 2.5rem; position: relative;">
                        <span style="font-size: 0.6rem; color: #888; font-weight: 700; display: block; margin-bottom: 0.5rem;"><?php echo strtoupper($prod['category']); ?> / <?php echo $prod['stock_quantity']; ?> IN STOCK-</span>
                        <h2 style="font-size: 1.8rem; margin-bottom: 1rem;"><?php echo strtoupper($prod['title']); ?>-</h2>
                        <p style="font-size: 1.5rem; font-weight: 300; margin-bottom: 2rem;">R<?php echo number_format($prod['price'], 2); ?>-</p>
                        
                        <div style="display: flex; gap: 2rem; padding-top: 1.5rem; border-top: 1px solid #eee;">
                            <a href="?delete_id=<?php echo $prod['id']; ?>" 
                               style="color: red; font-size: 0.7rem; font-weight: 900; letter-spacing: 1px;" 
                               onclick="return confirm('IDENTIFY- Confirm Deletion of Item?');">DELETE LISTING-</a>
                            <a href="../product_details.php?id=<?php echo $prod['id']; ?>" target="_blank" style="color: black; font-size: 0.7rem; font-weight: 900; letter-spacing: 1px;">VIEW LIVE-</a>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div style="grid-column: 1 / -1; padding: 6rem; text-align: center; border: 1px dashed black;">
                <p style="font-size: 0.8rem; font-weight: 700; color: #888;">NO ACTIVE LISTINGS IN ARCHIVE-</p>
                <a href="add_product.php" class="btn btn-primary" style="margin-top: 2rem;">CREATE FIRST LISTING-</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
