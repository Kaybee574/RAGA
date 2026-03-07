<?php
require_once 'session_helper.php';
require_once 'DatabaseConnection.php';

$product_id = $_GET['id'] ?? null;

if (!$product_id) {
    header("Location: Explore.php");
    exit();
}

// Fetch product details joined with store info
$query = "
    SELECT 
        p.*, 
        s.store_name, 
        s.location as store_location,
        s.id as store_id
    FROM products p
    JOIN stores s ON p.store_id = s.id
    WHERE p.id = $product_id
";

$result = mysqli_query($conn, $query);
$product = mysqli_fetch_assoc($result);

if (!$product) {
    echo "Product not found.";
    exit();
}

// Fetch some related products (same category)
$related_query = "SELECT * FROM products WHERE category = '" . mysqli_real_escape_string($conn, $product['category']) . "' AND id != $product_id LIMIT 4";
$related_result = mysqli_query($conn, $related_query);
$related_products = [];
if ($related_result) {
    while($row = mysqli_fetch_assoc($related_result)) {
        $related_products[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($product['title']); ?> · Raga</title>
    <link rel="stylesheet" href="mainCss.css">
    <style>
        .product-container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 20px;
            display: flex;
            gap: 40px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .product-image-section {
            flex: 1;
            max-width: 500px;
        }
        .product-image-section img {
            width: 100%;
            height: auto;
            border-radius: 8px;
            border: 1px solid #eee;
        }
        .product-info-section {
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 20px;
        }
        .product-title {
            font-size: 2.5rem;
            color: #333;
            margin: 0;
        }
        .product-price {
            font-size: 2rem;
            color: #000;
            font-weight: bold;
        }
        .product-meta {
            color: #666;
            font-size: 1rem;
        }
        .product-description {
            font-size: 1.1rem;
            line-height: 1.6;
            color: #444;
            border-top: 1px solid #eee;
            padding-top: 20px;
        }
        .store-info {
            background: #fdf2e9;
            padding: 15px;
            border-radius: 8px;
            border: 1px solid #f9dabf;
        }
        .store-info h4 {
            margin: 0 0 5px 0;
            color: #e67e22;
        }
        .btn-large-cart {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 15px 30px;
            background: #ffaa50;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1.2rem;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.2s;
            text-decoration: none;
            width: fit-content;
        }
        .btn-large-cart:hover {
            background: #e68f3c;
        }
        .related-section {
            max-width: 1200px;
            margin: 40px auto;
            padding: 20px;
        }
        .related-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        .related-card {
            background: white;
            padding: 15px;
            border-radius: 8px;
            border: 1px solid #ddd;
            text-align: center;
            text-decoration: none;
            color: inherit;
            transition: transform 0.2s;
        }
        .related-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .related-card img {
            width: 100%;
            height: 150px;
            object-fit: contain;
            margin-bottom: 10px;
        }

        @media screen and (max-width: 800px) {
            .product-container {
                flex-direction: column;
            }
            .product-image-section {
                max-width: 100%;
            }
        }
    </style>
</head>
<body>

<?php include 'navbar.php'; ?>

<main>
    <div class="product-container">
        <div class="product-image-section">
            <img src="<?php echo htmlspecialchars($product['image_url']); ?>" alt="<?php echo htmlspecialchars($product['title']); ?>">
        </div>
        
        <div class="product-info-section">
            <div class="product-meta">
                <span>Category: <?php echo htmlspecialchars($product['category']); ?></span>
            </div>
            <h1 class="product-title"><?php echo htmlspecialchars($product['title']); ?></h1>
            <div class="product-price">R<?php echo number_format($product['price'], 2); ?></div>
            
            <div class="product-description">
                <h3>Description</h3>
                <p><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>
            </div>

            <div class="store-info">
                <h4>Sold by</h4>
                <p><strong><?php echo htmlspecialchars($product['store_name']); ?></strong></p>
                <p style="font-size: 0.9rem; color: #777;">Location: <?php echo htmlspecialchars($product['store_location']); ?></p>
            </div>

            <form action="add_to_cart.php" method="POST">
                <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                <div style="display: flex; gap: 10px; align-items: center;">
                    <label for="quantity">Qty:</label>
                    <input type="number" id="quantity" name="quantity" value="1" min="1" max="<?php echo $product['stock_quantity']; ?>" style="width: 60px; padding: 10px; border-radius: 6px; border: 1px solid #ccc;">
                    <button type="submit" class="btn-large-cart">Add to Cart</button>
                </div>
                <p style="font-size: 0.9rem; color: #27ae60; margin-top: 10px;">In Stock: <?php echo $product['stock_quantity']; ?> units</p>
            </form>
        </div>
    </div>

    <?php if(!empty($related_products)): ?>
    <section class="related-section">
        <h2>Related Products</h2>
        <div class="related-grid">
            <?php foreach($related_products as $rp): ?>
            <a href="product.php?id=<?php echo $rp['id']; ?>" class="related-card">
                <img src="<?php echo htmlspecialchars($rp['image_url']); ?>" alt="<?php echo htmlspecialchars($rp['title']); ?>">
                <p style="font-weight: bold; margin: 5px 0;"><?php echo htmlspecialchars($rp['title']); ?></p>
                <p style="color: #ffaa50;">R<?php echo number_format($rp['price'], 2); ?></p>
            </a>
            <?php endforeach; ?>
        </div>
    </section>
    <?php endif; ?>
</main>

</body>
</html>
