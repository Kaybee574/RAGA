<?php
/**
 * Navigation Bar Component
 * This file handles the top and bottom navigation bars across the site.
 * It uses session_helper.php to dynamically show links based on login status.
 */
require_once 'session_helper.php';
?>
<header>
    <div class="top" style="display: flex; align-items: center; justify-content: space-between;">
        <h3 style="margin: 0;">Raga</h3>
        <form action="Explore.php" method="GET" style="flex: 1; display: flex; align-items: center; margin: 0 20px; height: 45px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); border-radius: 6px; overflow: hidden; background: white;">
            <input type="text" name="q" value="<?php echo htmlspecialchars($_GET['q'] ?? ''); ?>" placeholder="Looking for..." class="search-input" style="flex: 1; height: 100%; padding: 0 15px; border: 1px solid #ccc; border-right: none; border-radius: 6px 0 0 6px; font-family: inherit; font-size: 1rem; outline: none; box-sizing: border-box;">
            <button type="submit" class="search-button" style="height: 100%; padding: 0 25px; font-family: inherit; font-weight: bold; cursor: pointer; background: white; border: 1px solid #ccc; border-radius: 0 6px 6px 0; white-space: nowrap; font-size: 1rem; transition: background 0.2s; box-sizing: border-box;">Go</button>
        </form>
        <div style="display: flex; align-items: center; gap: 15px;">
            <!-- Shopping cart icon linked to Cart.php (shared across all pages) -->
            <a href="Cart.php"><img class="icon" src="Icons/shopping-cart.png" alt="cart" style="width: 30px; height: 30px;"></a>
            
            <?php if (isLoggedIn()): ?>
                <!-- User Greeting Section: Only shows if a session is active -->
                <div style="display: flex; align-items: center; gap: 10px;">
                    <span style="font-size: 0.9rem;">Hi, <?php echo htmlspecialchars(getUserName()); ?>!</span>
                    <img id="nav-avatar" src="<?php echo $_SESSION['avatar_url'] ?? 'Explore images/default_avatar.png'; ?>" alt="Avatar" style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover; border: 2px solid var(--accent-color);">
                </div>
            <?php endif; ?>
        </div>
    </div>
    <div class="bottom" style="display: flex; gap: 15px; margin-top: 15px; flex-wrap: wrap;">
        <section class="links"><a href="home.php">Home</a></section>

        <?php if (!isLoggedIn()): ?>
            <!-- Show these if user is NOT logged in -->
            <section class="links"><a href="SignIn.html">Sign In</a></section>
            <section class="links"><a href="SignUp.html">Sign Up</a></section>
        <?php else: ?>
            <!-- Show these if user IS logged in -->
            <?php if (getUserType() === 'both'): ?>
                <!-- Dual role users see both dashboards -->
                <section class="links"><a href="BuyerDashboard.php">Buyer Dash</a></section>
                <section class="links"><a href="SellerDashboard.php">Seller Dash</a></section>
            <?php else: ?>
                <!-- Single role users see their specific dashboard -->
                <section class="links">
                    <a href="<?php echo (getUserType() === 'seller') ? 'SellerDashboard.php' : 'BuyerDashboard.php'; ?>">Dashboard</a>
                </section>
            <?php endif; ?>
            <section class="links"><a href="logout.php">Sign Out</a></section>
        <?php endif; ?>

        <section class="links"><a href="AboutUs.html">About Us</a></section>
        <section class="links"><a href="sell.php">Sell</a></section>
        <section class="links"><a href="Categories.html">Categories</a></section>
        <section class="links"><a href="Explore.php">Explore</a></section>
        <section class="links"><a href="Reviews.html">User reviews</a></section>
    </div>
</header>