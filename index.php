<?php
/**
 * Main Home Page - Minimal Editorial Style (MANCLUB inspired)
 * Focused on high-impact typography and grid-based visual storytelling.
 */
include 'includes/header.php';

// Fetch current category if filter is applied
$cat_filter = isset($_GET['category']) ? sanitize($conn, $_GET['category']) : '';

// Fetch products based on category filter
$query = "SELECT p.*, s.store_name, s.location FROM products p JOIN stores s ON p.store_id = s.id";
if ($cat_filter) {
    // Basic mapping for our demo categories
    $query .= " WHERE p.category LIKE '%$cat_filter%'";
}
$query .= " ORDER BY p.created_at DESC LIMIT 10";
$result = mysqli_query($conn, $query);

// Demo Categories with Unsplash Images
$cat_items = [
    ['label' => 'MENS-TECH', 'img' => 'https://images.unsplash.com/photo-1519389950473-47ba0277781c?q=80&w=1200', 'link' => 'index.php?category=tech'],
    ['label' => 'MENS-ACCESSORIES', 'img' => 'https://images.unsplash.com/photo-1549439602-43ebca2327af?q=80&w=1200', 'link' => 'index.php?category=accessories'],
    ['label' => 'MENS-HOME', 'img' => 'https://images.unsplash.com/photo-1583847268964-b28dc2f51ac9?q=80&w=1200', 'link' => 'index.php?category=home'],
    ['label' => 'WOMENS-STYLE', 'img' => 'https://images.unsplash.com/photo-1551488831-00ddcb6c6bd3?q=80&w=1200', 'link' => 'index.php?category=style'],
];
?>

<!-- Hero Impact Headline Section -->
<div class="hero-editorial">
    <h1>WE CARRY A VARIETY OF CAMPUS PRODUCTS THAT ARE GOOD FOR BOTH YOU AND THE PLANET</h1>
    <a href="#shop" class="btn btn-primary" style="padding: 1rem 3rem; font-size: 0.8rem; border: 1px solid black; border-radius: 50px;">Shop Now-</a>
</div>

<!-- Category Exploration Grid (The large visual blocks) -->
<div class="grid-editorial">
    <?php foreach($cat_items as $cat): ?>
        <a href="<?php echo $cat['link']; ?>" class="grid-item">
            <img src="<?php echo $cat['img']; ?>" alt="<?php echo $cat['label']; ?>">
            <div class="grid-tag"><?php echo $cat['label']; ?></div>
        </a>
    <?php endforeach; ?>
</div>

<!-- Secondary Headline Section -->
<div class="hero-editorial" style="padding: 5rem 2rem; border-bottom: 1px solid black;">
    <p style="font-size: 1.5rem; max-width: 800px; margin: 0 auto; line-height: 1.5;">
        HOW <span style="font-weight: 900; border-bottom: 2px solid red;">RAGA-</span> FASHION SHOW ON STREET OF MAKHANDA
    </p>
</div>

<!-- Product Showcase (The Actual Listings) -->
<div id="shop" style="padding: 4% 0;">
    <h2 style="text-align: center; margin-bottom: 4rem; font-weight: 300; font-size: 2.5rem;">New Arrivals-</h2>
    
    <div class="grid-editorial" style="border-top: none;">
        <?php if (mysqli_num_rows($result) > 0): ?>
            <?php while($product = mysqli_fetch_assoc($result)): ?>
                <a href="product_details.php?id=<?php echo $product['id']; ?>" class="grid-item">
                    <img src="<?php echo !empty($product['image_url']) ? $product['image_url'] : 'https://images.unsplash.com/photo-1516259762381-22954d7d3ad2?q=80&w=800'; ?>" 
                        alt="<?php echo htmlspecialchars($product['title']); ?>" 
                        class="product-image"
                        onerror="this.src='https://images.unsplash.com/photo-1516259762381-22954d7d3ad2?q=80&w=800';">
                    <div class="grid-tag"><?php echo htmlspecialchars($product['title']); ?></div>
                    <div class="grid-price">R<?php echo number_format($product['price'], 2); ?>-</div>
                </a>
            <?php endwhile; ?>
        <?php else: ?>
            <div style="grid-column: 1 / -1; text-align: center; padding: 5rem;">
                <h3>The warehouse is empty.</h3>
                <a href="seller/add_product.php" class="btn btn-primary">Stock-</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
