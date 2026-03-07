<?php
require_once 'DatabaseConnection.php';

// Temporarily disable foreign key checks so we can drop tables safely
mysqli_query($conn, 'SET FOREIGN_KEY_CHECKS = 0');

// Drop existing tables if they exist
$tables = ['reviews', 'order_items', 'orders', 'cart_items', 'products', 'stores', 'sellers', 'buyers'];
foreach ($tables as $table) {
    mysqli_query($conn, "DROP TABLE IF EXISTS $table");
}

// 1. Buyers
$sql = "CREATE TABLE buyers (
    student_number VARCHAR(50) PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    address TEXT,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
mysqli_query($conn, $sql) or die(mysqli_error($conn));

// 2. Sellers
$sql = "CREATE TABLE sellers (
    seller_id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    contact_number VARCHAR(20),
    password VARCHAR(255) NOT NULL,
    address TEXT,
    shop_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
mysqli_query($conn, $sql) or die(mysqli_error($conn));

// 3. Stores (Seller Profiles)
$sql = "CREATE TABLE stores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    seller_id INT NOT NULL,
    store_name VARCHAR(100) NOT NULL,
    description TEXT,
    location VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (seller_id) REFERENCES sellers(seller_id) ON DELETE CASCADE
)";
mysqli_query($conn, $sql) or die(mysqli_error($conn));

// 4. Products (Inventory)
$sql = "CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    store_id INT NOT NULL,
    title VARCHAR(150) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    stock_quantity INT DEFAULT 0,
    category VARCHAR(50),
    image_url VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (store_id) REFERENCES stores(id) ON DELETE CASCADE
)";
mysqli_query($conn, $sql) or die(mysqli_error($conn));

// 5. Cart Items
$sql = "CREATE TABLE cart_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    buyer_student_number VARCHAR(50) NOT NULL,
    product_id INT NOT NULL,
    quantity INT DEFAULT 1,
    added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (buyer_student_number) REFERENCES buyers(student_number) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
)";
mysqli_query($conn, $sql) or die(mysqli_error($conn));

// 6. Orders
$sql = "CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    buyer_student_number VARCHAR(50) NOT NULL,
    total_amount DECIMAL(10, 2) NOT NULL,
    status ENUM('pending', 'paid', 'shipped', 'delivered', 'cancelled') DEFAULT 'pending',
    shipping_address TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (buyer_student_number) REFERENCES buyers(student_number) ON DELETE CASCADE
)";
mysqli_query($conn, $sql) or die(mysqli_error($conn));

// 7. Order Items
$sql = "CREATE TABLE order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    price_at_purchase DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE NO ACTION
)";
mysqli_query($conn, $sql) or die(mysqli_error($conn));

// 8. Reviews
$sql = "CREATE TABLE reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    buyer_student_number VARCHAR(50) NOT NULL,
    rating INT CHECK(rating >= 1 AND rating <= 5),
    comment TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    FOREIGN KEY (buyer_student_number) REFERENCES buyers(student_number) ON DELETE CASCADE
)";
mysqli_query($conn, $sql) or die(mysqli_error($conn));

mysqli_query($conn, 'SET FOREIGN_KEY_CHECKS = 1');

echo "Tables created successfully.\n";

// Seed Data
// Insert test buyer
mysqli_query($conn, "INSERT INTO buyers (student_number, full_name, address, password) VALUES ('g23x0000', 'Test Buyer', '10 Res Road', '" . password_hash("password", PASSWORD_DEFAULT) . "')");

// Insert test seller
mysqli_query($conn, "INSERT INTO sellers (full_name, email, contact_number, password, address) VALUES ('Test Seller', 'seller@campus.ru.ac.za', '0810000000', '" . password_hash("password", PASSWORD_DEFAULT) . "', '20 Business Ave')");
$seller_id = mysqli_insert_id($conn);

// Insert test store
mysqli_query($conn, "INSERT INTO stores (seller_id, store_name, description, location) VALUES ($seller_id, 'Campus Tech', 'Best campus deals', 'Student Center')");
$store_id = mysqli_insert_id($conn);
mysqli_query($conn, "UPDATE sellers SET shop_id = $store_id WHERE seller_id = $seller_id");

// Insert mock products (matching previous mock data from the UI when possible)
$products = [
    [
        'title' => 'T-Shirt',
        'description' => 'Comfortable cotton T-shirt.',
        'price' => 150.00,
        'stock_quantity' => 50,
        'category' => 'Clothing',
        'image_url' => 'https://via.placeholder.com/150'
    ],
    [
        'title' => 'Sneakers',
        'description' => 'Stylish and durable sneakers.',
        'price' => 800.00,
        'stock_quantity' => 20,
        'category' => 'Clothing',
        'image_url' => 'https://via.placeholder.com/150'
    ],
    [
        'title' => 'Headphones',
        'description' => 'Noise-canceling headphones.',
        'price' => 1200.00,
        'stock_quantity' => 15,
        'category' => 'Electronics',
        'image_url' => 'https://via.placeholder.com/150'
    ],
    [
        'title' => 'Laptop',
        'description' => 'High-performance laptop.',
        'price' => 15000.00,
        'stock_quantity' => 5,
        'category' => 'Electronics',
        'image_url' => 'https://via.placeholder.com/150'
    ],
    [
        'title' => 'Textbook',
        'description' => 'Advanced computer science textbook.',
        'price' => 450.00,
        'stock_quantity' => 30,
        'category' => 'Books',
        'image_url' => 'https://via.placeholder.com/150'
    ]
];

foreach ($products as $p) {
    $title = mysqli_real_escape_string($conn, $p['title']);
    $desc = mysqli_real_escape_string($conn, $p['description']);
    $category = mysqli_real_escape_string($conn, $p['category']);
    $image = mysqli_real_escape_string($conn, $p['image_url']);
    
    $query = "INSERT INTO products (store_id, title, description, price, stock_quantity, category, image_url) 
              VALUES ($store_id, '$title', '$desc', {$p['price']}, {$p['stock_quantity']}, '$category', '$image')";
    mysqli_query($conn, $query) or die(mysqli_error($conn));
}

echo "Database seeded with default mock products!\n";

?>
