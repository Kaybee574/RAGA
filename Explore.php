<?php
require_once 'session_helper.php';
require_once 'DatabaseConnection.php';

// 1. Get filter parameters
$search = $_GET['q'] ?? '';
$selected_categories = $_GET['categories'] ?? [];
$sort = $_GET['sort'] ?? 'relevant';

// 2. Build Query
$query = "
    SELECT 
        p.id, 
        p.title, 
        p.price, 
        p.category,
        p.image_url as image, 
        s.store_name, 
        CONCAT('Stores/Store', s.id, '/Store', s.id, '.html') as store_link 
    FROM products p
    JOIN stores s ON p.store_id = s.id
    WHERE p.stock_quantity > 0
";

if (!empty($search)) {
    $search = mysqli_real_escape_string($conn, $search);
    $query .= " AND (p.title LIKE '%$search%' OR p.description LIKE '%$search%')";
}

if (!empty($selected_categories)) {
    $categories_escaped = array_map(function($c) use ($conn) { return "'" . mysqli_real_escape_string($conn, $c) . "'"; }, $selected_categories);
    $query .= " AND p.category IN (" . implode(',', $categories_escaped) . ")";
}

// 3. Add sorting
switch ($sort) {
    case 'price_low':
        $query .= " ORDER BY p.price ASC";
        break;
    case 'price_high':
        $query .= " ORDER BY p.price DESC";
        break;
    case 'newest':
        $query .= " ORDER BY p.created_at DESC";
        break;
    default:
        // 'relevant' or default
        break;
}

$result = mysqli_query($conn, $query);

$products = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $products[] = $row;
    }
}

// List of available categories for the sidebar
$cat_query = "SELECT DISTINCT category FROM products WHERE category IS NOT NULL AND category != ''";
$cat_result = mysqli_query($conn, $cat_query);
$available_categories = [];
if ($cat_result) {
    while($row = mysqli_fetch_assoc($cat_result)) {
        $available_categories[] = $row['category'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Explore · Raga</title>
    
    <link rel="stylesheet" href="mainCss.css"> 
    <style>
        html, body {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        .page-header {
            text-align: center;
            color: #ffaa50;
            font-size: 2.5rem;
            margin-bottom: 30px;
        }
        h1 {
            text-align: center;
            margin: 40px 0;
            font-size: 2.5rem;
            color: #000000;
        }

        .top {
            display: flex;
            align-items: center;
            gap: 15px;
            flex-wrap: wrap;
        }
        .bottom {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            margin-top: 20px;
        }
        .icon {
            width: 30px;
            height: 30px;
        }
        .search-input {
            flex: 1;
            min-width: 200px;
            padding: 8px;
        }
        .myContainer { 
            display: flex; 
        }  

        .myFoot {
            background: #ffffff;
            padding: 15px 30px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }
        .links a {
            font-weight: 500;
            color: #333;
            transition: color 0.2s;
        }
        .links a:hover {
            color: black;
        }

        .search-button:hover {
            background: #ff9500;
        }

        .explore-layout {
            display: flex;
            gap: 30px;
            margin: 40px 0;
        }

        /* Sidebar for filters */
        .filter-sidebar {
            display: grid;
            flex: 0 0 260px;
            background: rgb(235, 235, 235);
            padding: 20px;
            border-radius: 12px;
            height: fit-content;
            border: 1px solid white;
            justify-content: center;
            font-family: inherit;
            font-size: larger;
        }

        .filter-group {
            margin-bottom: 30px;
        }
        .filter-group h4 {
            font-size: 1.1rem;
            margin-bottom: 12px;
            color: #444;
            font-weight: 600;
        }
        .filter-group label {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 10px;
            font-size: 0.95rem;
            color: #555;
            cursor: pointer;
        }
        .filter-group input[type="checkbox"] {
            accent-color: #ffaa50;
            width: 16px;
            height: 16px;
        }

        .filter-actions {
            display: grid;
            gap: 10px;
            justify-content: center;
        }

        /* product grid*/
        .product-grid {
            flex: 1;
            display: flex;
            flex-direction: column;
        }
        .grid-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            flex-wrap: wrap;
            gap: 15px;
        }
        .grid-header p {
            font-size: 1.3rem;
            color:black;
            font-weight: 500;
        }
        .sort-select {
            padding: 8px 16px;
            border-radius: 30px;
            border: 1px solid white;
            background: white;
            font-size: 0.9rem;
        }

        .Categories {
            display: flex;
            flex-wrap: wrap;
            gap: 25px;
            justify-content: flex-start; /* aligned left */
        }

        .price {
            color: #000000;
            font-weight: 700;
            font-size: 1.2rem;
        }
        /* responsive dESIGN */
        @media screen and (max-width: 1000px) {
            .explore-layout {
                flex-direction: column;
            }
            .filter-sidebar {
                flex: auto;
                width: 100%;
            }
        }
        @media screen and (max-width: 900px) {
            .category-card img {
                height: auto;
            }
        }
        @media screen and (max-width: 700px) {
            .Categories {
                justify-content: center;
            }
            .category-card {
                min-width: 200px;
            }
        }
        @media screen and (max-width: 600px) {
            .Categories {
                flex-direction: column;
                align-items: center;
            }
            .category-card {
                width: 100%;
                max-width: 320px;
            }
            .page-header { font-size: 2rem; }
        }
        @media screen and (max-width: 480px) {
            main { padding: 0 15px; }
            h1 { font-size: 1.8rem; }
        }
        /* THIS IS FOR STYLING THE ACTUAL ITEM  */
        .my-category-card {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            min-width: 240px;
            max-width: 280px;
            border: 1px solid #ddd;   
            padding-bottom: 25px;         
        }
        .my-category-card:hover {
            background-color: #ffffff;
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
            border-color: #ffaa50;
        }
        .my-category-card img {
            width: 100%;
            height: 200px;
            object-fit: contain;
            border-radius: 12px;
            margin-bottom: 12px;
            background: #fff;
        }
        .my-category-card p {
            font-size: 1.1rem;
            font-weight: 600;
            margin: 8px 0 4px;
            color: #333;
        }      
        .linkToShop {
            color: #7e3285;
            font-size: 0.9rem !important;
            margin-bottom: 15px !important;
        }  
        .btnAddToCart {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            padding: 12px 10px;
            background: #ffaa50;
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.2s;
            margin-top: 15px;
            line-height: 1.2;
        }
        .btnAddToCart:hover {
            background: #e68f3c;
        }
        .btnReset,.btnApply {
            font-size: large;
            width: 150px;
            font-style: inherit;
        }

    </style>
</head>
<body>
  
<?php include 'navbar.php'; ?>

<main>
    <br>
    <p>Let's Explore</p>
    
    <section class="explore-layout">
        <!-- This is the filter section -->
        <aside class="filter-sidebar">
            <form action="Explore.php" method="GET">
                <!-- Keep search term if present -->
                <?php if(!empty($search)): ?>
                    <input type="hidden" name="q" value="<?php echo htmlspecialchars($search); ?>">
                <?php endif; ?>
                
                <h3>Filter by</h3>
                <div class="filter-group">
                    <h4>Category</h4>
                    <?php if(empty($available_categories)): ?>
                        <p style="font-size: 0.8rem; color: #888;">No categories found.</p>
                    <?php else: ?>
                        <?php foreach($available_categories as $cat): ?>
                            <label>
                                <input type="checkbox" name="categories[]" value="<?php echo htmlspecialchars($cat); ?>" 
                                    <?php echo in_array($cat, $selected_categories) ? 'checked' : ''; ?>> 
                                <?php echo htmlspecialchars($cat); ?>
                            </label>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <div class="filter-actions">
                    <button type="submit" class="btnApply" style="background: #ffaa50; color: white; border: none; padding: 10px; border-radius: 6px; cursor: pointer; font-weight: bold;">Apply filters</button>
                    <a href="Explore.php" class="btnReset" style="background: #ccc; color: #333; border: none; padding: 10px; border-radius: 6px; text-decoration: none; text-align: center; font-weight: bold; display: block; margin-top: 5px;">Reset All</a>
                </div>
            </form>
            <p style="margin-top: 20px; font-weight: bold;"><?php echo count($products); ?> items found</p>
        </aside>

        <!-- This grid which displays the products -->
        <div class="product-grid">
            <div class="grid-header">
                <p><?php echo !empty($search) ? 'Search results for "'.htmlspecialchars($search).'"' : 'Especially chosen For you'; ?></p>
                <form action="Explore.php" method="GET" id="sortForm">
                    <!-- Maintain existing filters when sorting -->
                    <?php if(!empty($search)): ?>
                        <input type="hidden" name="q" value="<?php echo htmlspecialchars($search); ?>">
                    <?php endif; ?>
                    <?php foreach($selected_categories as $cat): ?>
                        <input type="hidden" name="categories[]" value="<?php echo htmlspecialchars($cat); ?>">
                    <?php endforeach; ?>
                    
                    <select name="sort" class="sort-select" onchange="this.form.submit()">
                        <option value="relevant" <?php echo $sort == 'relevant' ? 'selected' : ''; ?>>Most relevant</option>
                        <option value="price_low" <?php echo $sort == 'price_low' ? 'selected' : ''; ?>>Price low-high</option>
                        <option value="price_high" <?php echo $sort == 'price_high' ? 'selected' : ''; ?>>Price high-low</option>
                        <option value="newest" <?php echo $sort == 'newest' ? 'selected' : ''; ?>>Newest</option>
                    </select>
                </form>
            </div>
            <div class="Categories">
                <?php foreach ($products as $product): ?>
                <section class="my-category-card">
                    <a href="product.php?id=<?php echo $product['id']; ?>" class="category-card">
                        <img src="<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['title']); ?>">
                        <p><?php echo htmlspecialchars($product['title']); ?></p>
                        <span class="price">R<?php echo number_format($product['price'], 2); ?></span>
                        <a href="<?php echo htmlspecialchars($product['store_link']); ?>" class="Shopitem"><p class="linkToShop"><?php echo htmlspecialchars($product['store_name']); ?></p></a>
                    </a>
                    <form action="add_to_cart.php" method="POST" style="width: 100%;">
                        <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                        <button type="submit" class="btnAddToCart">Add to Cart</button>
                    </form>
                </section>
                <?php endforeach; ?>
            </div> 
        </div> 
    </section>
</main>

</body>
</html>
