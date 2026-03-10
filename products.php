<?php
/**
 * Dedicated Products Exploration Page - Minimal Editorial Style
 * Full catalog of marketplace items with advanced filtering.
 */
include 'includes/header.php';

// Fetch current category if filter is applied
$cat_filter = isset($_GET['category']) ? sanitize($conn, $_GET['category']) : '';
$sort = isset($_GET['sort']) ? sanitize($conn, $_GET['sort']) : 'newest';

// Base Query
$query = "SELECT p.*, s.store_name, s.location FROM products p JOIN stores s ON p.store_id = s.id WHERE 1=1";


// Filtering
if ($cat_filter) {
    if ($cat_filter === 'tech') $query .= " AND p.category = 'Electronics'";
    elseif ($cat_filter === 'home') $query .= " AND (p.category = 'Furniture' OR p.category = 'Home')";
    elseif ($cat_filter === 'style') $query .= " AND (p.category = 'Clothing' OR p.category = 'Accessories')";
    else $query .= " AND p.category LIKE '%$cat_filter%'";
}

// Sorting logic
if ($sort === 'price_low') $query .= " ORDER BY p.price ASC";
elseif ($sort === 'price_high') $query .= " ORDER BY p.price DESC";
else $query .= " ORDER BY p.created_at DESC";

$result = mysqli_query($conn, $query);
?>

<div style="padding: 4% 0;">
    <!-- Editorial Sidebar/Header Hybrid -->
    <div style="padding: 0 4%; margin-bottom: 4rem; display: flex; justify-content: space-between; align-items: flex-end; border-bottom: 1px solid black; padding-bottom: 2rem;">
        <div>
            <h1 style="font-size: 4rem; letter-spacing: -3px; line-height: 1;">CATALOG-</h1>
            <p style="font-size: 0.7rem; letter-spacing: 2px; color: #888; font-weight: 700; margin-top: 1rem;">
                BROWSE ALL <?php echo strtoupper($cat_filter ?: 'AVAILABLE'); ?> ITEMS-
            </p>
        </div>
        
        <div style="display: flex; gap: 2rem; font-size: 0.7rem; font-weight: 700;">
            <a href="products.php?sort=newest<?php echo $cat_filter ? "&category=$cat_filter" : ""; ?>" style="color: <?php echo $sort == 'newest' ? 'black' : '#888'; ?>; text-decoration: none;">NEWEST-</a>
            <a href="products.php?sort=price_low<?php echo $cat_filter ? "&category=$cat_filter" : ""; ?>" style="color: <?php echo $sort == 'price_low' ? 'black' : '#888'; ?>; text-decoration: none;">LOW PRICE-</a>
            <a href="products.php?sort=price_high<?php echo $cat_filter ? "&category=$cat_filter" : ""; ?>" style="color: <?php echo $sort == 'price_high' ? 'black' : '#888'; ?>; text-decoration: none;">HIGH PRICE-</a>
        </div>
    </div>

    <!-- Clean Editorial Grid -->
    <div class="grid-editorial" style="border-top: none;">
        <?php if (mysqli_num_rows($result) > 0): ?>
            <?php while($product = mysqli_fetch_assoc($result)): ?>
                <a href="product_details.php?id=<?php echo $product['id']; ?>" class="grid-item" style="height: 60vh;">
                    <img src="<?php echo !empty($product['image_url']) ? $product['image_url'] : 'https://images.unsplash.com/photo-1516259762381-22954d7d3ad2?q=80&w=800'; ?>" 
                         alt="<?php echo htmlspecialchars($product['title']); ?>"
                         onerror="this.src='https://images.unsplash.com/photo-1516259762381-22954d7d3ad2?q=80&w=800';">
                    <div class="grid-tag">
                        <?php echo htmlspecialchars($product['title']); ?>
                        <br>
                        <span style="font-size: 0.7rem; opacity: 0.5; font-weight: 400;"><?php echo htmlspecialchars($product['store_name']); ?></span>
                    </div>
                    <div class="grid-price">R<?php echo number_format($product['price'], 2); ?>-</div>
                </a>
            <?php endwhile; ?>
        <?php else: ?>
            <div style="grid-column: 1 / -1; text-align: center; padding: 10rem;">
                <h2 style="font-weight: 300;">THE ARCHIVE IS EMPTY-</h2>
                <p style="margin-top: 2rem;"><a href="index.php" class="btn">GO HOME-</a></p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
