<?php
require_once 'session_helper.php';
require_once 'DatabaseConnection.php';

if (!isLoggedIn()) {
    header("Location: SignIn.html");
    exit();
}

$studentNumber = $_SESSION['student_number'] ?? null;
$name = $_SESSION['name'];
$userType = $_SESSION['user_type'];

// Fetch buyer details
$query = "SELECT * FROM buyers WHERE student_number = '$studentNumber'";
$result = mysqli_query($conn, $query);
$buyer = mysqli_fetch_assoc($result);

// Fetch buyer's orders
$order_query = "SELECT * FROM orders WHERE buyer_student_number = '$studentNumber' ORDER BY created_at DESC";
$order_result = mysqli_query($conn, $order_query);
$orders = [];
if ($order_result) {
    while ($row = mysqli_fetch_assoc($order_result)) {
        $orders[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buyer Dashboard - Raga</title>
    <link rel="stylesheet" href="mainCss.css">
    <style>
        .dashboard-container {
            max-width: 1000px;
            margin: 40px auto;
            padding: 20px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .profile-card {
            background: #fdfdfd;
            padding: 20px;
            border-radius: 8px;
            border: 1px solid #eee;
            margin-bottom: 30px;
        }
        .dashboard-header {
            color: #7e3285;
            margin-bottom: 20px;
        }
        .info-row {
            margin-bottom: 15px;
            display: flex;
            gap: 10px;
        }
        .label {
            font-weight: bold;
            color: #555;
            min-width: 150px;
        }
        .value {
            color: #333;
        }
        .actions {
            margin-top: 20px;
            display: flex;
            gap: 15px;
        }
        .btn {
            padding: 10px 20px;
            border-radius: 6px;
            text-decoration: none;
            color: white;
            background: #ffaa50;
            font-weight: bold;
        }
        .btn:hover {
            background: #ff9500;
        }
    </style>
</head>
<body>

<?php include 'navbar.php'; ?>

<main class="dashboard-container">
    <h1 class="dashboard-header">Buyer Dashboard</h1>
    
    <div class="profile-card" style="text-align: center;">
        <div style="position: relative; width: 150px; height: 150px; margin: 0 auto 20px; cursor: pointer;" onclick="document.getElementById('avatar-input').click()">
            <img id="dashboard-avatar" src="<?php echo $buyer['avatar_url'] ?? 'Explore images/default_avatar.png'; ?>" alt="Avatar" style="width: 150px; height: 150px; border-radius: 50%; object-fit: cover; border: 4px solid #fdfdfd; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
            <div style="position: absolute; bottom: 5px; right: 5px; background: #7e3285; color: white; padding: 8px; border-radius: 50%; font-size: 0.8rem;">📷</div>
            <input type="file" id="avatar-input" style="display: none;" accept="image/*">
        </div>
        <h2>Account Information</h2>
        <div class="info-row">
            <div class="label">Full Name:</div>
            <div class="value"><?php echo htmlspecialchars($name); ?></div>
        </div>
        <div class="info-row">
            <div class="label">Student Number:</div>
            <div class="value"><?php echo htmlspecialchars($studentNumber); ?></div>
        </div>
        <div class="info-row">
            <div class="label">Address:</div>
            <div class="value"><?php echo htmlspecialchars($buyer['address'] ?? 'N/A'); ?></div>
        </div>
        
        <div class="actions">
            <a href="Explore.php" class="btn">Shop Now</a>
            <a href="Cart.php" class="btn">View Cart</a>
            <a href="edit_profile.php" class="btn" style="background: #7e3285;">Edit Profile</a>
        </div>
    </div>
    
    <div class="profile-card">
        <h2>Your Orders</h2>
        <?php if (empty($orders)): ?>
            <p>No orders found yet. Start shopping!</p>
        <?php else: ?>
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; margin-top: 15px;">
                    <thead>
                        <tr style="border-bottom: 2px solid #eee; text-align: left;">
                            <th style="padding: 10px;">Order ID</th>
                            <th style="padding: 10px;">Date</th>
                            <th style="padding: 10px;">Total</th>
                            <th style="padding: 10px;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $order): ?>
                            <tr style="border-bottom: 1px solid #eee;">
                                <td style="padding: 10px;">#<?php echo $order['id']; ?></td>
                                <td style="padding: 10px;"><?php echo date('M d, Y', strtotime($order['created_at'])); ?></td>
                                <td style="padding: 10px;">R<?php echo number_format($order['total_amount'], 2); ?></td>
                                <td style="padding: 10px;">
                                    <span style="padding: 4px 10px; border-radius: 20px; font-size: 0.85rem; background: <?php 
                                        echo $order['status'] === 'delivered' ? '#d4edda; color: #155724;' : 
                                            ($order['status'] === 'shipped' ? '#fff3cd; color: #856404;' : 
                                            ($order['status'] === 'cancelled' ? '#f8d7da; color: #721c24;' : '#e2e3e5; color: #383d41;'));
                                    ?>">
                                        <?php echo ucfirst($order['status']); ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</main>

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
