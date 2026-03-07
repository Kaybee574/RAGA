<?php
require_once 'session_helper.php';
require_once 'DatabaseConnection.php';

if (!isLoggedIn() || (getUserType() !== 'seller' && getUserType() !== 'both')) {
    header("Location: SignIn.html");
    exit();
}

$userId = $_SESSION['user_id'];
$error = '';
$success = '';

// Check if store already exists
$querySeller = "SELECT seller_id FROM sellers WHERE email = '$userId'";
$resultSeller = mysqli_query($conn, $querySeller);
$seller = mysqli_fetch_assoc($resultSeller);
$sellerId = $seller['seller_id'];

$queryCheck = "SELECT id FROM stores WHERE seller_id = '$sellerId'";
$resultCheck = mysqli_query($conn, $queryCheck);
if (mysqli_num_rows($resultCheck) > 0) {
    header("Location: SellerDashboard.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $storeName = mysqli_real_escape_string($conn, $_POST['store_name']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $location = mysqli_real_escape_string($conn, $_POST['location']);

    if (empty($storeName)) {
        $error = "Store name is required.";
    } else {
        $sql = "INSERT INTO stores (seller_id, store_name, description, location) VALUES ($sellerId, '$storeName', '$description', '$location')";
        if (mysqli_query($conn, $sql)) {
            $storeId = mysqli_insert_id($conn);
            mysqli_query($conn, "UPDATE sellers SET shop_id = $storeId WHERE seller_id = $sellerId");
            $success = "Store created successfully! Redirecting...";
            header("refresh:2;url=SellerDashboard.php");
        } else {
            $error = "Error creating store: " . mysqli_error($conn);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Your Store - Raga</title>
    <link rel="stylesheet" href="mainCss.css">
    <style>
        .form-container {
            max-width: 500px;
            margin: 50px auto;
            padding: 30px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }
        h2 { color: #7e3285; margin-bottom: 20px; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: bold; }
        .form-group input, .form-group textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 6px;
            box-sizing: border-box;
        }
        .btn {
            background: #ffaa50;
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 6px;
            cursor: pointer;
            font-weight: bold;
            width: 100%;
            font-size: 1rem;
        }
        .btn:hover { background: #ff9500; }
        .error { color: #d9534f; margin-bottom: 15px; }
        .success { color: #5cb85c; margin-bottom: 15px; }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>
    
    <div class="form-container">
        <h2>Create Your Store</h2>
        <?php if ($error): ?><div class="error"><?php echo $error; ?></div><?php endif; ?>
        <?php if ($success): ?><div class="success"><?php echo $success; ?></div><?php endif; ?>
        
        <form method="POST">
            <div class="form-group">
                <label for="store_name">Store Name</label>
                <input type="text" id="store_name" name="store_name" required placeholder="e.g. My Awesome Shop">
            </div>
            <div class="form-group">
                <label for="location">Location</label>
                <input type="text" id="location" name="location" placeholder="e.g. Grahamstown, Rhodes University">
            </div>
            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" rows="4" placeholder="Briefly describe what you sell..."></textarea>
            </div>
            <button type="submit" class="btn">Create Store</button>
        </form>
    </div>
</body>
</html>
