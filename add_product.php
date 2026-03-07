<?php
require_once 'session_helper.php';
require_once 'DatabaseConnection.php';

if (!isLoggedIn() || (getUserType() !== 'seller' && getUserType() !== 'both')) {
    header("Location: SignIn.html");
    exit();
}

$email = $_SESSION['email'] ?? $_SESSION['user_id'];
$queryStore = "SELECT * FROM stores s JOIN sellers sel ON s.seller_id = sel.seller_id WHERE sel.email = '$email'";
$resStore = mysqli_query($conn, $queryStore);
$store = mysqli_fetch_assoc($resStore);

if (!$store) {
    die("<h1>Store Required</h1><p>You need to set up a store before adding products.</p><p><a href='SellerDashboard.php'>Go back</a></p>");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Product - Raga</title>
    <link rel="stylesheet" href="mainCss.css">
    <style>
        .form-container {
            max-width: 600px;
            margin: 50px auto;
            padding: 30px;
            background: #fdfdfd;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        h1 { color: #7e3285; text-align: center; }
        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 8px; font-weight: bold; color: #333; }
        input, textarea, select {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-sizing: border-box;
        }
        .btn-submit {
            width: 100%;
            padding: 15px;
            background: #ffaa50;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1.1rem;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.3s;
        }
        .btn-submit:hover { background: #e68f3c; }
        .back-link { display: block; text-align: center; margin-top: 20px; color: #7e3285; text-decoration: none; }
    </style>
</head>
<body>

<?php include 'navbar.php'; ?>

<main class="form-container">
    <h1>List New Product</h1>
    <form action="add_product_backend.php" method="POST">
        <input type="hidden" name="store_id" value="<?php echo $store['id']; ?>">
        
        <div class="form-group">
            <label for="title">Product Title</label>
            <input type="text" id="title" name="title" required placeholder="e.g. Vintage Denim Jacket">
        </div>
        
        <div class="form-group">
            <label for="price">Price (R)</label>
            <input type="number" id="price" name="price" step="0.01" required placeholder="0.00">
        </div>
        
        <div class="form-group">
            <label for="category">Category</label>
            <select id="category" name="category" required>
                <option value="Electronics">Electronics</option>
                <option value="Fashion" selected>Fashion</option>
                <option value="Books">Books</option>
                <option value="Home">Home</option>
                <option value="Furniture">Furniture</option>
                <option value="Cosmetics">Cosmetics</option>
                <option value="Textbooks">Textbooks</option>
                <option value="Other">Other</option>
            </select>
        </div>

        <div class="form-group">
            <label for="stock">Stock Quantity</label>
            <input type="number" id="stock" name="stock" value="1" min="1" required>
        </div>

        <div class="form-group">
            <label for="image_url">Image URL</label>
            <input type="text" id="image_url" name="image_url" placeholder="category_pictures/Fashion.jpg" required>
            <small style="color: #666;">Tip: Use paths like 'category_pictures/Electronics.jpg' for now.</small>
        </div>
        
        <div class="form-group">
            <label for="description">Description</label>
            <textarea id="description" name="description" rows="4" placeholder="Tell buyers about your item..."></textarea>
        </div>
        
        <button type="submit" class="btn-submit">List Product Now</button>
        <a href="SellerDashboard.php" class="back-link">Cancel and Go Back</a>
    </form>
</main>

</body>
</html>
