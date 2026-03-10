<?php
/**
 * Product Details View - Minimal Editorial Style
 * Displays a single product's information and 'Add to Cart' functionality for buyers.
 */
include 'includes/header.php';

// Get Product ID from the URL
$product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Fetch full details of the product
$sql = "SELECT p.*, s.store_name, s.location, sl.full_name as seller_name 
        FROM products p 
        JOIN stores s ON p.store_id = s.id 
        JOIN sellers sl ON s.seller_id = sl.seller_id 
        WHERE p.id = $product_id";

$result = mysqli_query($conn, $sql);
$product = mysqli_fetch_assoc($result);

// Handle 'Add to Cart' Request
$message = "";
if (isset($_POST['add_to_cart'])) {
    if (!$isLoggedIn || $userRole !== 'buyer') {
        $message = "Please ID as Buyer-";
    }
    else {
        $buyer_id = $_SESSION['user_id'];
        $qty = 1;

        $check_cart = mysqli_query($conn, "SELECT id, quantity FROM cart_items WHERE buyer_student_number = '$buyer_id' AND product_id = $product_id");

        if (mysqli_num_rows($check_cart) > 0) {
            $cart_row = mysqli_fetch_assoc($check_cart);
            $new_qty = $cart_row['quantity'] + 1;
            mysqli_query($conn, "UPDATE cart_items SET quantity = $new_qty WHERE id = " . $cart_row['id']);
        }
        else {
            mysqli_query($conn, "INSERT INTO cart_items (buyer_student_number, product_id, quantity) VALUES ('$buyer_id', $product_id, $qty)");
        }
        $message = "In Cart-";
    }
}

if (!$product) {
    echo "<div style='text-align: center; padding: 10rem;'><h1>Not Found-</h1><a href='index.php' class='btn'>Raga-</a></div>";
    include 'includes/footer.php';
    exit();
}
?>

<div class="product-details-container">
    <div class="product-image-panel">
        <img src="<?php echo !empty($product['image_url']) ? $product['image_url'] : 'https://images.unsplash.com/photo-1516259762381-22954d7d3ad2?q=80&w=800'; ?>" 
             alt="<?php echo htmlspecialchars($product['title']); ?>" 
             style="width: 100%; height: 100%; object-fit: cover;"
             onerror="this.src='https://images.unsplash.com/photo-1516259762381-22954d7d3ad2?q=80&w=800';">
    </div>
    
    <div class="product-info-panel">
        <?php if ($message): ?>
            <div style="font-weight: 700; margin-bottom: 2rem; border: 1px solid black; padding: 1rem; border-radius: 50px; text-align: center;">
                <?php echo $message; ?>
            </div>
        <?php
endif; ?>

        <div style="margin-bottom: 4rem;">
            <p style="font-size: 0.8rem; color: #888; font-weight: 700; letter-spacing: 2px;">
                <?php echo strtoupper($product['category']); ?> / <?php echo strtoupper($product['location']); ?>
            </p>
            <h1 style="font-size: 3.5rem; letter-spacing: -2px; line-height: 0.9; margin-top: 1rem;"><?php echo htmlspecialchars($product['title']); ?></h1>
            <p style="font-size: 2rem; margin-top: 2rem; font-weight: 300;">R<?php echo number_format($product['price'], 2); ?>-</p>
        </div>
        
        <div style="margin-bottom: 4rem; padding-top: 2rem; border-top: 1px solid #000;">
            <p style="line-height: 1.8; text-transform: none; color: #333;">
                <?php echo nl2br(htmlspecialchars($product['description'])); ?>
            </p>
        </div>

        <div style="margin-bottom: 4rem;">
            <p style="font-size: 0.7rem; font-weight: 700; letter-spacing: 1px; color: #888;">SELLER-</p>
            <p style="font-weight: 700;"><?php echo htmlspecialchars($product['store_name']); ?> / <?php echo htmlspecialchars($product['seller_name']); ?></p>
        </div>

        <form method="POST">
            <?php if ($product['stock_quantity'] > 0): ?>
                <button type="submit" name="add_to_cart" class="btn btn-primary" style="width: 100%; padding: 1.5rem; font-size: 1rem; font-weight: 900; background: black; color: white;">Add to Pouch-</button>
            <?php else: ?>
                <button type="button" class="btn" disabled style="width: 100%; padding: 1.5rem; font-size: 1rem; font-weight: 900; opacity: 0.5; cursor: not-allowed; border: 1px dashed black;">Sold Out-</button>
            <?php endif; ?>
        </form>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
