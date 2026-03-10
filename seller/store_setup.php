<?php
/**
 * Initial Store Creation for Sellers
 * Required setup before a seller can list products.
 */
include '../includes/header.php';

// Authorization: Ensure user is a seller
if (!$isLoggedIn || $userRole !== 'seller') {
    header("Location: ../login.php");
    exit();
}

$seller_id = intval($_SESSION['user_id']); // This is now the numerical seller_id from the DB
$error = "";
$success = "";

// Check if seller already has a store (only one store per seller allowed)
$check_sql = "SELECT id FROM stores WHERE seller_id = $seller_id";
if (mysqli_num_rows(mysqli_query($conn, $check_sql)) > 0) {
    header("Location: dashboard.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $store_name = sanitize($conn, $_POST['store_name']);
    $store_desc = sanitize($conn, $_POST['store_desc']);
    $location = sanitize($conn, $_POST['location']);

    if (empty($store_name) || empty($store_desc) || empty($location)) {
        $error = "CRITICAL ERROR: Please provide a name, description, and location for your store.";
    } else {
        // Insert new store into the 'stores' table matching the exact schema from images
        $sql = "INSERT INTO stores (seller_id, store_name, description, location) VALUES ($seller_id, '$store_name', '$store_desc', '$location')";
        if (mysqli_query($conn, $sql)) {
            $store_id = mysqli_insert_id($conn);
            // Update the sellers table with the newly created shop_id
            mysqli_query($conn, "UPDATE sellers SET shop_id = $store_id WHERE seller_id = $seller_id");
            
            $success = "Store created! Redirecting to your dashboard...";
            header("Refresh: 2; URL=dashboard.php");
        } else {
            $error = "SYSTEM ERROR: Failed to create store. " . mysqli_error($conn);
        }
    }
}
?>

<div class="auth-container">
    <div style="text-align: center; margin-bottom: 2rem;">
        <h1>Setup Your Store</h1>
        <p style="color: var(--text-secondary);">One last step before you start selling on Raga.</p>
    </div>

    <?php if ($error): ?>
        <div class="alert alert-error"><?php echo $error; ?></div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="form-group">
            <label for="store_name">Store Name</label>
            <input type="text" name="store_name" id="store_name" placeholder="e.g. Campus Tech" required>
        </div>

        <div class="form-group">
            <label for="location">Physical Location</label>
            <input type="text" name="location" id="location" placeholder="e.g. Student Center, Rhodes University" required>
        </div>

        <div class="form-group">
            <label for="store_desc">About Your Store</label>
            <textarea name="store_desc" id="store_desc" rows="4" placeholder="Briefly describe what you sell." required></textarea>
        </div>

        <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 1rem;">Create My Store</button>
    </form>
</div>

<?php include '../includes/footer.php'; ?>
