<?php
require_once 'session_helper.php';
require_once 'DatabaseConnection.php';

if (!isLoggedIn()) {
    header("Location: SignIn.html");
    exit();
}

$email = $_SESSION['email'] ?? null;
$name = $_SESSION['name'];

// Fetch seller information
$querySeller = "SELECT * FROM sellers WHERE email = '$email'";
$resultSeller = mysqli_query($conn, $querySeller);
$seller = mysqli_fetch_assoc($resultSeller);

if (!$seller) {
    die("Seller account not found.");
}

$sellerId = $seller['seller_id'];
$avatarUrl = $seller['avatar_url'] ?? 'Explore images/default_avatar.png';

// Fetch store information
$queryStore = "SELECT * FROM stores WHERE seller_id = '$sellerId'";
$resultStore = mysqli_query($conn, $queryStore);
$store = mysqli_fetch_assoc($resultStore);

$totalSales = 0;
$avgRating = 0;
$pendingOrders = 0;
$products = [];

if ($store) {
    $storeId = $store['id'];
    
    // Fetch Inventory
    $queryProducts = "SELECT * FROM products WHERE store_id = '$storeId'";
    $resultProducts = mysqli_query($conn, $queryProducts);
    while ($row = mysqli_fetch_assoc($resultProducts)) {
        $products[] = $row;
    }

    // Calculate Total Sales
    $querySales = "SELECT SUM(oi.price_at_purchase * oi.quantity) as total_sales 
                   FROM order_items oi 
                   JOIN products p ON oi.product_id = p.id 
                   JOIN orders o ON oi.order_id = o.id 
                   WHERE p.store_id = '$storeId' AND o.status IN ('paid', 'shipped', 'delivered')";
    $resSales = mysqli_query($conn, $querySales);
    $salesData = mysqli_fetch_assoc($resSales);
    $totalSales = $salesData['total_sales'] ?? 0;

    // Calculate Avg Rating
    $queryReviews = "SELECT AVG(r.rating) as avg_rating 
                     FROM reviews r 
                     JOIN products p ON r.product_id = p.id 
                     WHERE p.store_id = '$storeId'";
    $resReviews = mysqli_query($conn, $queryReviews);
    $reviewData = mysqli_fetch_assoc($resReviews);
    $avgRating = number_format($reviewData['avg_rating'] ?? 0, 1);

    // Count Pending Orders
    $queryPending = "SELECT COUNT(DISTINCT o.id) as pending_count 
                     FROM orders o 
                     JOIN order_items oi ON o.id = oi.order_id 
                     JOIN products p ON oi.product_id = p.id 
                     WHERE p.store_id = '$storeId' AND o.status = 'pending'";
    $resPending = mysqli_query($conn, $queryPending);
    $pendingData = mysqli_fetch_assoc($resPending);
    $pendingOrders = $pendingData['pending_count'] ?? 0;

    // Fetch History
    $orders = [];
    $queryHistory = "SELECT o.id, o.created_at, o.total_amount, o.status 
                     FROM orders o 
                     JOIN order_items oi ON o.id = oi.order_id 
                     JOIN products p ON oi.product_id = p.id 
                     WHERE p.store_id = '$storeId'
                     GROUP BY o.id
                     ORDER BY o.created_at DESC";
    $resHistory = mysqli_query($conn, $queryHistory);
    if ($resHistory) {
        while($row = mysqli_fetch_assoc($resHistory)) {
            $orders[] = $row;
        }
    }

    // Fetch Reviews
    $reviewsList = [];
    $queryReviewsList = "SELECT r.*, b.full_name 
                         FROM reviews r 
                         JOIN products p ON r.product_id = p.id 
                         JOIN buyers b ON r.buyer_student_number = b.student_number
                         WHERE p.store_id = '$storeId'
                         ORDER BY r.created_at DESC";
    $resReviewsList = mysqli_query($conn, $queryReviewsList);
    if ($resReviewsList) {
        while($row = mysqli_fetch_assoc($resReviewsList)) {
            $reviewsList[] = $row;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seller Dashboard - Raga</title>
    <link rel="stylesheet" href="mainCss.css">
    <style>
        :root {
            --primary-color: #7e3285;
            --accent-color: #ffaa50;
            --bg-color: #ffffff;
            --card-bg: #f5f5f5;
        }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; margin: 0; background-color: #fff; }
        
        .top-banner {
            background-color: var(--accent-color);
            padding: 15px;
            text-align: center;
            font-size: 1.5rem;
            font-weight: bold;
            color: #333;
        }

        .dashboard-nav {
            background-color: var(--accent-color);
            padding: 10px 0;
            display: flex;
            justify-content: center;
            gap: 30px;
            margin-top: 10px;
        }
        .dashboard-nav a {
            text-decoration: none;
            color: #333;
            font-weight: 500;
            font-size: 1.1rem;
        }
        .dashboard-nav a:hover { color: var(--primary-color); }

        .dashboard-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .welcome-msg {
            text-align: center;
            margin: 30px 0;
            font-size: 1.2rem;
        }

        .profile-section {
            background-color: #e0e0e0;
            border-radius: 50px;
            padding: 30px;
            display: flex;
            align-items: center;
            gap: 40px;
            margin-bottom: 40px;
            position: relative;
        }
        
        .avatar-container {
            position: relative;
            width: 150px;
            height: 150px;
        }

        .avatar-img {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid white;
            cursor: pointer;
        }
        .avatar-overlay {
            position: absolute;
            bottom: 0;
            right: 0;
            background: var(--primary-color);
            color: white;
            padding: 5px;
            border-radius: 50%;
            cursor: pointer;
            font-size: 0.8rem;
        }

        .profile-info { flex: 1; }
        .profile-info p { margin: 5px 0; font-size: 1.1rem; }
        
        .edit-btn {
            background: white;
            border: none;
            padding: 10px 20px;
            border-radius: 20px;
            font-weight: bold;
            cursor: pointer;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 30px;
            margin-bottom: 40px;
        }
        .stat-card {
            background-color: #eeeeee;
            border-radius: 12px;
            padding: 30px;
            text-align: center;
            box-shadow: 2px 2px 10px rgba(0,0,0,0.05);
        }
        .stat-card h3 { margin-top: 0; font-weight: normal; }
        .stat-value { font-size: 2rem; font-weight: bold; margin-top: 20px; }

        .section-header { margin-top: 40px; color: #333; }
        
        .inventory-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background: white;
        }
        .inventory-table th, .inventory-table td {
            text-align: left;
            padding: 15px;
            border-bottom: 1px solid #ddd;
        }
        .inventory-table th { color: var(--primary-color); font-weight: bold; }

        #avatar-input { display: none; }
    </style>
</head>
<body>

<div class="top-banner">Raga</div>

<nav class="dashboard-nav">
    <a href="SellerDashboard.php">About My shop</a>
    <a href="#inventory">Inventory</a>
    <a href="#reviews">Rating And Reviews</a>
    <a href="#history">History</a>
    <a href="#stats">Stats</a>
    <a href="logout.php">Logout</a>
</nav>

<div class="dashboard-content">
    <div class="welcome-msg">Welcome to your dashboard</div>

    <section class="profile-section">
        <div class="avatar-container" onclick="document.getElementById('avatar-input').click()">
            <img src="<?php echo $avatarUrl; ?>" alt="Avatar" class="avatar-img" id="dashboard-avatar">
            <div class="avatar-overlay">📷</div>
            <input type="file" id="avatar-input" accept="image/*">
        </div>
        
        <div class="profile-info">
            <?php if ($store): ?>
                <p><strong>Store Name:</strong> <?php echo htmlspecialchars($store['store_name']); ?></p>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($seller['email']); ?></p>
                <p><strong>Location:</strong> <?php echo htmlspecialchars($store['location']); ?></p>
                <p><strong>Selling Since:</strong> <?php echo date('F Y', strtotime($store['created_at'])); ?></p>
            <?php else: ?>
                <p><strong>Name:</strong> <?php echo htmlspecialchars($seller['full_name']); ?></p>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($seller['email']); ?></p>
                <p><a href="create_store.php" class="edit-btn" style="text-decoration: none; display: inline-block; margin-top: 10px;">Setup Your Store</a></p>
            <?php endif; ?>
        </div>
        
        <?php if ($store): ?>
            <a href="edit_profile.php" class="edit-btn" style="text-decoration: none; display: inline-block;">edit details</a>
        <?php endif; ?>
    </section>

    <h3 class="section-header" id="stats">Quick Stats</h3>
    <div class="stats-grid">
        <div class="stat-card">
            <h3>Total Sales</h3>
            <div class="stat-value">R<?php echo number_format($totalSales, 2); ?></div>
        </div>
        <div class="stat-card">
            <h3>Reviews</h3>
            <div class="stat-value"><?php echo $avgRating; ?></div>
        </div>
        <div class="stat-card">
            <h3>Pending Orders</h3>
            <div class="stat-value"><?php echo $pendingOrders; ?> Pending Orders</div>
        </div>
    </div>

    <h3 class="section-header" id="reviews">Ratings & Reviews</h3>
    <div style="background: #eeeeee; padding: 20px; border-radius: 12px; margin-bottom: 40px;">
        <div style="text-align: center; margin-bottom: 20px;">
            <h1 style="color: var(--primary-color); font-size: 3rem; margin: 0;"><?php echo $avgRating; ?></h1>
            <p>Average Star Rating</p>
        </div>
        <?php if (empty($reviewsList)): ?>
            <p style="text-align: center;">No reviews yet.</p>
        <?php else: ?>
            <?php foreach ($reviewsList as $rev): ?>
                <div style="background: white; padding: 15px; border-radius: 8px; margin-bottom: 10px; border-left: 5px solid var(--accent-color);">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
                        <strong><?php echo htmlspecialchars($rev['full_name']); ?></strong>
                        <span style="color: #ffaa50;">★ <?php echo $rev['rating']; ?></span>
                    </div>
                    <p style="margin: 0; color: #555;"><?php echo htmlspecialchars($rev['comment']); ?></p>
                    <small style="color: #999;"><?php echo date('M d, Y', strtotime($rev['created_at'])); ?></small>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <h3 class="section-header" id="history">Manage Sales & Fulfillment</h3>
    <div style="background: #eeeeee; padding: 20px; border-radius: 12px; margin-bottom: 40px;">
        <table class="inventory-table">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Date</th>
                    <th>Total</th>
                    <th>Current Status</th>
                    <th>Update Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($orders)): ?>
                    <tr><td colspan="5">No sales history yet.</td></tr>
                <?php else: ?>
                    <?php foreach ($orders as $o): ?>
                    <tr>
                        <td>#<?php echo $o['id']; ?></td>
                        <td><?php echo date('M d, Y', strtotime($o['created_at'])); ?></td>
                        <td>R<?php echo number_format($o['total_amount'], 2); ?></td>
                        <td>
                            <span style="padding: 4px 10px; border-radius: 20px; font-size: 0.85rem; background: <?php 
                                echo $o['status'] === 'delivered' ? '#d4edda; color: #155724;' : 
                                    ($o['status'] === 'shipped' ? '#fff3cd; color: #856404;' : 
                                    ($o['status'] === 'cancelled' ? '#f8d7da; color: #721c24;' : '#e2e3e5; color: #383d41;'));
                            ?>">
                                <?php echo ucfirst($o['status']); ?>
                            </span>
                        </td>
                        <td>
                            <form action="update_order_status.php" method="POST" style="display: flex; gap: 5px;">
                                <input type="hidden" name="order_id" value="<?php echo $o['id']; ?>">
                                <select name="status" style="padding: 5px; border-radius: 4px; border: 1px solid #ccc; font-size: 0.8rem;">
                                    <option value="pending" <?php echo $o['status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                    <option value="shipped" <?php echo $o['status'] === 'shipped' ? 'selected' : ''; ?>>Shipped</option>
                                    <option value="delivered" <?php echo $o['status'] === 'delivered' ? 'selected' : ''; ?>>Delivered</option>
                                    <option value="cancelled" <?php echo $o['status'] === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                </select>
                                <button type="submit" class="edit-btn" style="padding: 5px 10px; font-size: 0.8rem; background: var(--accent-color); color: white;">Update</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php if ($store): ?>
    <h3 class="section-header" id="inventory">Your Inventory</h3>
    <div style="background: #eeeeee; padding: 20px; border-radius: 12px;">
        <table class="inventory-table">
            <thead>
                <tr>
                    <th>Image</th>
                    <th>Product</th>
                    <th>Price</th>
                    <th>Stock</th>
                    <th>Category</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($products)): ?>
                    <tr><td colspan="5">No products found.</td></tr>
                <?php else: ?>
                    <?php foreach ($products as $p): ?>
                    <tr>
                        <td><img src="<?php echo htmlspecialchars($p['image_url']); ?>" alt="" style="width: 50px; border-radius: 4px;"></td>
                        <td><?php echo htmlspecialchars($p['title']); ?></td>
                        <td>R<?php echo number_format($p['price'], 2); ?></td>
                        <td><?php echo $p['stock_quantity']; ?></td>
                        <td><?php echo htmlspecialchars($p['category']); ?></td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
        
        <div style="margin-top: 20px; text-align: right;">
            <a href="add_product.php" class="edit-btn" style="text-decoration: none; background: var(--accent-color); color: white;">+ Add Product</a>
        </div>
    </div>
    <?php endif; ?>
</div>

<script>
document.getElementById('avatar-input').onchange = function(e) {
    const file = e.target.files[0];
    if (!file) return;

    const formData = new FormData();
    formData.append('avatar', file);

    fetch('upload_avatar.php', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok: ' + response.statusText);
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            document.getElementById('dashboard-avatar').src = data.avatar_url;
            // Also update navbar avatar if present
            const navAvatar = document.getElementById('nav-avatar');
            if (navAvatar) navAvatar.src = data.avatar_url;
            alert('Avatar updated successfully!');
        } else {
            alert('Upload failed: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred during upload: ' + error.message);
    });
};
</script>

</body>
</html>
