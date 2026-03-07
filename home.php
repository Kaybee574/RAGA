<?php
/**
 * Dynamic Home Page
 * Converted from the original home.html to allow dynamic session-based navigation.
 * Uses session_helper.php to check if a user is logged in.
 */
require_once 'session_helper.php';
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>RAGA Inc</title>
    <link rel="stylesheet" href="RuStylish.css" />
  </head>
  <body class="mainSection">
    <main>
      <!-- This is the header, it contains links going to the different pages -->
      <header class="myHead">
        <section><a href="home.php">Home</a></section>
        
        <?php if (!isLoggedIn()): ?>
            <section><a href="SignIn.html">Sign In</a></section>
            <section><a href="SignUp.html">Sign Up</a></section>
        <?php else: ?>
            <section>
                <a href="<?php echo (getUserType() === 'seller') ? 'SellerDashboard.php' : 'BuyerDashboard.php'; ?>">Dashboard</a>
            </section>
            <section><a href="logout.php">Sign Out</a></section>
        <?php endif; ?>

        <section>
          <a href="Categories.html">Categories</a>
        </section>
        <section><a href="Explore.php">Explore</a></section>
        <section><a href="sell.php">Sell</a></section>
        <section>
          <a href="Reviews.html">User reviews</a>
        </section>
        <section>
          <a href="AboutUs.html">About Us</a>
        </section>
      </header>

      <section class="section1">
        <h2 class="MainHeader">Welcome to RAGA Inc</h2>
        <p>
          RAGA Inc is a revolutionary online marketplace based in Rhodes
          University, dedicated to connecting Rhodes University students who are
          buyers and sellers in a seamless, secure environment. Our platform was
          created by students for students. Here is some reels to inform you
          more about the origins of RAGA.
        </p>

        <section class="myButton">
          <!-- Dynamic Buttons: Content changes based on whether the user is logged in -->
          <?php if (!isLoggedIn()): ?>
            <!-- Default buttons for guest users -->
            <a href="SignIn.html"><button class="myButton">Get Started</button></a>
            <a href="Categories.html"><button class="myButton">Be My Guest</button></a>
          <?php else: ?>
            <!-- Dashboard/Shop buttons for logged-in users -->
            <a href="<?php echo (getUserType() === 'seller') ? 'SellerDashboard.php' : 'BuyerDashboard.php'; ?>">
                <button class="myButton">View Dashboard</button>
            </a>
            <a href="Explore.php"><button class="myButton">Shop Now</button></a>
          <?php endif; ?>
        </section>
        <br />
      </section>

    </main>

    <footer>
      <h3 class="Foot"><strong>Authors' Contact Details:</strong></h3>
      <address>
        <h3 class="homeEmailLinks">
          Karabo Mgwenya, email:
          <a href="mailto:karabomgwenya@yahoo.com" class="Yes"
            >karabomgwenya@yahoo.com</a
          >
        </h3>
        <h3 class="homeEmailLinks">
          Nigel Qango, email:
          <a href="mailto:stumpkitunathi@gmail.com" class="Yes"
            >stumpkitunathi@gmail.com</a
          >
        </h3>
        <h3 class="homeEmailLinks">
          Justin Mudimbu, email:
          <a href="mailto:youngx997@gmail.com" class="Yes"
            >youngx997@gmail.com</a
          >
        </h3>
        <h3 class="homeEmailLinks">
          Charmaine, email:
          <a href="mailto:chikengezhacharmaine@gmail.com" class="Yes"
            >chikengezhacharmaine@gmail.com</a
          >
        </h3>
      </address>
      <h5 class="Foot">
        <strong>Copyright &copy; 2026 RAGA Inc.</strong> All rights reserved.
      </h5>
    </footer>
  </body>
</html>
