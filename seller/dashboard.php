<?php
/**
 * Seller Dashboard - Editorial Design
 * Core management for store owners on Raga.
 */
include '../includes/header.php';

// Authorization: Ensure user is a seller
if (!$isLoggedIn || $userRole !== 'seller') {
    header("Location: ../login.php");
    exit();
}

$seller_id = intval($_SESSION['user_id']); 

// Check for store presence
$store_res = mysqli_query($conn, "SELECT * FROM stores WHERE seller_id = $seller_id");
$store = mysqli_fetch_assoc($store_res);

if (!$store) {
    header("Location: store_setup.php");
    exit();
}

$store_id = $store['id'];

// Aggregate stats
$product_count_res = mysqli_query($conn, "SELECT COUNT(*) as total FROM products WHERE store_id = $store_id");
$product_count = mysqli_fetch_assoc($product_count_res)['total'];

// Fetch recent sales activity
$sales_sql = "SELECT oi.*, o.created_at, o.status, p.title 
              FROM order_items oi 
              JOIN orders o ON oi.order_id = o.id 
              JOIN products p ON oi.product_id = p.id 
              WHERE p.store_id = $store_id 
              ORDER BY o.created_at DESC LIMIT 5";
$sales_res = mysqli_query($conn, $sales_sql);
?>

<div style="padding: 4% 8%;">
    <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 4rem; border-bottom: 1px solid black; padding-bottom: 2rem;">
        <div>
            <h1 style="font-size: 3.5rem; letter-spacing: -3px;"><?php echo strtoupper($store['store_name']); ?>-</h1>
            <p style="font-size: 0.7rem; letter-spacing: 2px; color: #888; font-weight: 700;">LOCATION: <?php echo strtoupper($store['location']); ?></p>
        </div>
        <div style="display: flex; gap: 2rem; font-size: 0.7rem; font-weight: 700;">
            <a href="dashboard.php" style="border-bottom: 1px solid black; padding-bottom: 4px;">OVERVIEW-</a>
            <a href="products.php">INVENTORY-</a>
            <a href="add_product.php">LIST ITEM-</a>
        </div>
    </div>

    <!-- Stats -->
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; margin-bottom: 4rem;">
        <div style="border: 1px solid black; padding: 4rem; text-align: center;">
            <p style="font-size: 0.7rem; font-weight: 700; color: #888; margin-bottom: 1rem;">ACTIVE LISTINGS-</p>
            <h2 style="font-size: 4rem;"><?php echo $product_count; ?></h2>
        </div>
        <div style="border: 1px solid black; padding: 4rem; text-align: center;">
            <p style="font-size: 0.7rem; font-weight: 700; color: #888; margin-bottom: 1rem;">STORE STATUS-</p>
            <h2 style="font-size: 4rem;">LIVE</h2>
        </div>
    </div>

    <!-- Sales Table -->
    <div>
        <h3 style="font-size: 1.5rem; margin-bottom: 2rem; font-weight: 300;">SALES LOG-</h3>
        <?php if (mysqli_num_rows($sales_res) > 0): ?>
            <div style="border-top: 1px solid black;">
                <?php while($sale = mysqli_fetch_assoc($sales_res)): ?>
                    <div style="display: flex; justify-content: space-between; padding: 2rem 0; border-bottom: 1px solid black;">
                        <div style="max-width: 400px;">
                            <p style="font-weight: 700; font-size: 1.2rem;"><?php echo strtoupper($sale['title']); ?></p>
                            <p style="font-size: 0.7rem; font-weight: 700; color: #888; margin-top: 0.5rem;">ORDER #<?php echo $sale['id']; ?> / <?php echo date("d.m.Y", strtotime($sale['created_at'])); ?></p>
                        </div>
                        <div style="text-align: right;">
                            <p style="font-size: 1.2rem; font-weight: 700;">R<?php echo number_format($sale['price_at_purchase'] * $sale['quantity'], 2); ?>-</p>
                            <span style="font-size: 0.6rem; font-weight: 900; background: #000; color: #fff; padding: 0.2rem 0.5rem; border-radius: 50px;"><?php echo strtoupper($sale['status']); ?></span>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div style="padding: 6rem; text-align: center; border: 1px dashed #ccc;">
                <p style="font-size: 0.8rem; font-weight: 700; color: #888;">NO SALES TO LOG-</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
