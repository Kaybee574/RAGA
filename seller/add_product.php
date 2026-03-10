<?php
/**
 * Add New Product View
 * Allows sellers to list items for sale in the marketplace.
 */
include '../includes/header.php';

// Authorization: Ensure user is a seller
if (!$isLoggedIn || $userRole !== 'seller') {
    header("Location: ../login.php");
    exit();
}

$seller_id = $_SESSION['user_id'];
$error = "";
$success = "";

// Get user's store ID
$store_sql = "SELECT id FROM stores WHERE seller_student_number = '$seller_id'";
$store_res = mysqli_query($conn, $store_sql);
$store = mysqli_fetch_assoc($store_res);

if (!$store) {
    header("Location: store_setup.php");
    exit();
}

$store_id = $store['id'];

// Handle Product Creation Request
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = sanitize($conn, $_POST['title']);
    $description = sanitize($conn, $_POST['description']);
    $price = floatval($_POST['price']);
    $stock = intval($_POST['stock']);
    $category = sanitize($conn, $_POST['category']);
    $image_url = sanitize($conn, $_POST['image_url']);

    if (empty($title) || empty($description) || $price <= 0 || $stock < 1) {
        $error = "CRITICAL ERROR: Please provide complete product information with valid price/stock.";
    } else {
        // Insert product into 'products' table associated with this store
        $sql = "INSERT INTO products (store_id, title, description, price, stock_quantity, category, image_url) 
                VALUES ($store_id, '$title', '$description', $price, $stock, '$category', '$image_url')";
        
        if (mysqli_query($conn, $sql)) {
            $success = "Item successfully listed! <a href='products.php'>Manage your products</a>.";
        } else {
            $error = "SYSTEM ERROR: Product listing failed. " . mysqli_error($conn);
        }
    }
}
?>

<div class="dashboard-grid">
    <div class="sidebar">
        <ul class="sidebar-menu">
            <li><a href="dashboard.php">Overview</a></li>
            <li><a href="products.php">Manage Products</a></li>
            <li><a href="add_product.php" class="active">Add New Item</a></li>
            <li><a href="../index.php">View Marketplace</a></li>
            <li><a href="../logout.php" style="color: var(--error);">Logout</a></li>
        </ul>
    </div>

    <div style="background: white; padding: 2.5rem; border-radius: 8px;">
        <h1 style="margin-bottom: 2rem;">Post a New Item</h1>
        
        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>

        <form method="POST">
            <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem;">
                <div class="form-group">
                    <label for="title">Product Title</label>
                    <input type="text" name="title" id="title" placeholder="e.g. Vintage Denim Jacket" required>
                </div>
                <div class="form-group">
                    <label for="category">Category</label>
                    <select name="category" id="category" required>
                        <option value="Clothing">Clothing</option>
                        <option value="Electronics">Electronics</option>
                        <option value="Books">Books</option>
                        <option value="Accessories">Accessories</option>
                        <option value="Furniture">Furniture</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label for="description">Detailed Description</label>
                <textarea name="description" id="description" rows="5" placeholder="Specify condition, size, or any unique features." required></textarea>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                <div class="form-group">
                    <label for="price">Price (Rand)</label>
                    <input type="number" step="0.01" name="price" id="price" placeholder="R 0.00" required>
                </div>
                <div class="form-group">
                    <label for="stock">Quantity Available</label>
                    <input type="number" name="stock" id="stock" value="1" min="1" required>
                </div>
            </div>

            <div class="form-group">
                <label for="image_url">Image URL</label>
                <input type="url" name="image_url" id="image_url" placeholder="Paste an image link (e.g. from Unsplash)">
                <p style="font-size: 0.8rem; color: var(--text-secondary); margin-top: 5px;">Helpful Tip: Use high-quality images to sell faster!</p>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; padding: 1rem; font-size: 1.1rem; margin-top: 1rem;">List Product Now</button>
        </form>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
