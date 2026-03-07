<?php
require_once 'DatabaseConnection.php';
require_once 'session_helper.php';

if (!isLoggedIn() || getUserType() !== 'seller') {
    die("Unauthorized access.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $storeId = mysqli_real_escape_string($conn, $_POST['store_id']);
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $price = mysqli_real_escape_string($conn, $_POST['price']);
    $category = mysqli_real_escape_string($conn, $_POST['category']);
    $stock = mysqli_real_escape_string($conn, $_POST['stock']);
    $imageUrl = mysqli_real_escape_string($conn, $_POST['image_url']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);

    $query = "INSERT INTO products (store_id, title, description, price, stock_quantity, category, image_url) 
              VALUES ('$storeId', '$title', '$description', '$price', '$stock', '$category', '$imageUrl')";

    if (mysqli_query($conn, $query)) {
        header("Location: SellerDashboard.php?success=1");
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>
