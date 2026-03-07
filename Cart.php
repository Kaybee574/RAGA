<?php
require_once 'DatabaseConnection.php';
require_once 'session_helper.php';

if (!isLoggedIn()) {
    header("Location: SignIn.html");
    exit();
}

$buyer_id = $_SESSION['student_number']; // We MUST use student_number for database queries to match the foreign key constraint.

$cart_items = [];
$total = 0;

$query = "
    SELECT 
        ci.id as cart_item_id, 
        p.id as product_id, 
        p.title, 
        p.price, 
        s.email as seller, 
        ci.quantity 
    FROM cart_items ci
    JOIN products p ON ci.product_id = p.id
    JOIN stores st ON p.store_id = st.id
    JOIN sellers s ON st.seller_id = s.seller_id
    WHERE ci.buyer_student_number = '$buyer_id'
";

$result = mysqli_query($conn, $query);
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        // We'll map 'cart_item_id' to 'id' so the frontend code below doesn't break
        $row['id'] = $row['cart_item_id'];
        $cart_items[] = $row;
        $total += $row['price'] * $row['quantity'];
    }
}
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Shopping Cart - RAGA Inc</title>
    <link rel="stylesheet" href="mainCss.css">
    <style>
      .MainCart {
        border: 1px solid black;
        padding: 20px;
        min-height: 400px;
        background-color: #f9f9f9;
        border-radius: 8px;
        max-width: 1000px;
        margin: 0 auto;
      }

      h1 {
        color: #ffaa50;
        text-align: center;
        font-size: 2.5rem;
        margin-bottom: 30px;
      }

      .cart-item {
        display: flex;
        align-items: center;
        padding: 15px;
        margin-bottom: 15px;
        background-color: white;
        border: 1px solid #ddd;
        border-radius: 4px;
        transition: transform 0.3s;
      }

      .cart-item:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
      }

      .cart-item-image {
        width: 100px;
        height: 100px;
        background-color: #eacdf6;
        margin-right: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #666;
        border-radius: 4px;
      }

      .cart-item-details {
        flex: 1;
      }

      .cart-item-title {
        font-weight: bold;
        font-size: 1.2rem;
        color: #333;
      }

      .cart-item-price {
        color: #ffaa50;
        font-weight: bold;
        font-size: 1.1rem;
      }

      .cart-item-seller {
        color: #666;
        font-size: 0.9rem;
      }

      .cart-total {
        margin-top: 20px;
        padding: 20px;
        background-color: white;
        border: 1px solid #ddd;
        border-radius: 4px;
        text-align: right;
      }

      .cart-total h3 {
        color: #333;
        font-size: 1.5rem;
      }

      .cart-total span {
        color: #ffaa50;
        font-weight: bold;
      }

      .checkout-button {
        display: inline-block;
        padding: 15px 30px;
        background-color: #ffaa50;
        color: white;
        text-decoration: none;
        border-radius: 4px;
        font-size: 1.2rem;
        margin-top: 10px;
        border: none;
        cursor: pointer;
      }

      .checkout-button:hover {
        background-color: #ff8c69;
      }

      /* Cart styling */
      .empty-cart {
        display: none;
        text-align: center;
        padding: 50px;
        color: #666;
      }

      .empty-cart p {
        font-size: 1.2rem;
        margin-bottom: 20px;
      }

      .shop-now {
        display: inline-block;
        padding: 10px 20px;
        background-color: #ffaa50;
        color: white;
        text-decoration: none;
        border-radius: 4px;
      }
      /* Override broad styles from mainCss.css */
      .MainCart p {
        display: block;
        justify-content: initial;
        font-size: initial;
        margin: initial;
        text-align: initial;
      }
    </style>
  </head>

  <body>
<?php include 'navbar.php'; ?>
    <main>
      <header>
        <h1>Your Shopping Cart</h1>
      </header>

      <?php if (!empty($cart_items)): ?>
      <section class="MainCart" id="main-cart">
        
        <?php foreach ($cart_items as $index => $item): ?>
        <article class="cart-item" data-price="<?php echo $item['price']; ?>">
          <div class="cart-item-details">
            <p class="cart-item-title"><a href="product.php?id=<?php echo $item['product_id']; ?>" style="text-decoration: none; color: inherit;"><?php echo htmlspecialchars($item['title']); ?></a></p>
            <p class="cart-item-seller">Seller: <?php echo htmlspecialchars($item['seller']); ?></p>
            <p class="cart-item-price">R<?php echo number_format($item['price'], 2); ?></p>
          </div>
          <div>
            <label for="quantity<?php echo $index; ?>">Qty:</label>
            <form action="update_cart.php" method="POST" style="display:inline-block;">
                <input type="hidden" name="cart_item_id" value="<?php echo $item['id']; ?>">
                <select id="quantity<?php echo $index; ?>" name="quantity" style="padding: 5px" onchange="this.form.submit()">
                  <?php for($i=1; $i<=5; $i++): ?>
                    <option value="<?php echo $i; ?>" <?php echo $item['quantity'] == $i ? 'selected' : ''; ?>><?php echo $i; ?></option>
                  <?php endfor; ?>
                </select>
            </form>
            <form action="remove_cart_item.php" method="POST" style="display:inline-block; margin-left:10px;">
                <input type="hidden" name="cart_item_id" value="<?php echo $item['id']; ?>">
                <button type="submit" style="color:red; background:none; border:none; cursor:pointer;" title="Remove Item">✖</button>
            </form>
          </div>
        </article>
        <?php endforeach; ?>

        <div class="cart-total">
          <h3>Total: <span id="cart-total-amount">R<?php echo number_format($total, 2); ?></span></h3>
          <a href="Checkout.php" class="checkout-button"
            >Proceed to Checkout</a
          >
        </div>
      </section>
      <?php else: ?>
      <section class="MainCart empty-cart" style="display: block;">
        <p>Your cart is currently empty.</p>
        <a href="Explore.php" class="shop-now">Shop Now</a>
      </section>
      <?php endif; ?>
    </main>

    <footer style="text-align: center; margin-top: 20px">
      <a href="Explore.php" style="color: #ffaa50; text-decoration: none"
        >← Continue Shopping</a
      >
    </footer>

  </body>
</html>
