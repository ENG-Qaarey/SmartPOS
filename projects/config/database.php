<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'smartpos');

// Create connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create database if not exists
$sql = "CREATE DATABASE IF NOT EXISTS " . DB_NAME;
if ($conn->query($sql) === TRUE) {
    $conn->select_db(DB_NAME);
} else {
    die("Error creating database: " . $conn->error);
}

// Create tables
$tables = [
    "CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        full_name VARCHAR(100) NOT NULL,
        email VARCHAR(100),
        phone VARCHAR(20),
        role ENUM('admin', 'cashier') NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )",
    
    "CREATE TABLE IF NOT EXISTS categories (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )",
    
    "CREATE TABLE IF NOT EXISTS products (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(200) NOT NULL,
        description TEXT,
        price DECIMAL(10,2) NOT NULL,
        stock INT NOT NULL DEFAULT 0,
        category_id INT,
        low_stock_threshold INT DEFAULT 10,
        image VARCHAR(255),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
    )",
    
    "CREATE TABLE IF NOT EXISTS customers (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        email VARCHAR(100),
        phone VARCHAR(20),
        address TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )",
    
    "CREATE TABLE IF NOT EXISTS sales (
        id INT AUTO_INCREMENT PRIMARY KEY,
        invoice_number VARCHAR(20) UNIQUE NOT NULL,
        user_id INT NOT NULL,
        customer_id INT,
        total_amount DECIMAL(10,2) NOT NULL,
        discount DECIMAL(10,2) DEFAULT 0,
        final_amount DECIMAL(10,2) NOT NULL,
        payment_method ENUM('cash', 'card') DEFAULT 'cash',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id),
        FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE SET NULL
    )",
    
    "CREATE TABLE IF NOT EXISTS sale_items (
        id INT AUTO_INCREMENT PRIMARY KEY,
        sale_id INT NOT NULL,
        product_id INT NOT NULL,
        quantity INT NOT NULL,
        price DECIMAL(10,2) NOT NULL,
        total DECIMAL(10,2) NOT NULL,
        FOREIGN KEY (sale_id) REFERENCES sales(id) ON DELETE CASCADE,
        FOREIGN KEY (product_id) REFERENCES products(id)
    )"
];

// Execute table creation
foreach ($tables as $sql) {
    if ($conn->query($sql) !== TRUE) {
        die("Error creating table: " . $conn->error);
    }
}

// Add image column to products table if it doesn't exist
$check_image_column = $conn->query("SHOW COLUMNS FROM products LIKE 'image'");
if ($check_image_column->num_rows == 0) {
    $conn->query("ALTER TABLE products ADD COLUMN image VARCHAR(255) AFTER low_stock_threshold");
}

// Add email and phone columns to users table if they don't exist
$check_email_column = $conn->query("SHOW COLUMNS FROM users LIKE 'email'");
if ($check_email_column->num_rows == 0) {
    $conn->query("ALTER TABLE users ADD COLUMN email VARCHAR(100) AFTER full_name");
}

$check_phone_column = $conn->query("SHOW COLUMNS FROM users LIKE 'phone'");
if ($check_phone_column->num_rows == 0) {
    $conn->query("ALTER TABLE users ADD COLUMN phone VARCHAR(20) AFTER email");
}

// Insert default admin user if not exists
$admin_check = $conn->query("SELECT id FROM users WHERE username = 'admin' LIMIT 1");
if ($admin_check->num_rows == 0) {
    $admin_password = password_hash('admin123', PASSWORD_DEFAULT);
    $conn->query("INSERT INTO users (username, password, full_name, role) VALUES ('admin', '$admin_password', 'Administrator', 'admin')");
}

// Insert default categories if not exists
$categories = ['Electronics', 'Clothing', 'Food & Beverages', 'Home & Garden', 'Books', 'Sports'];
foreach ($categories as $category) {
    $conn->query("INSERT IGNORE INTO categories (name) VALUES ('$category')");
}

// Insert sample products if none exist
$products_check = $conn->query("SELECT COUNT(*) as count FROM products");
$products_count = $products_check->fetch_assoc()['count'];

if ($products_count == 0) {
    // Fetch category IDs by name
    $category_map = [];
    $cat_result = $conn->query("SELECT id, name FROM categories");
    while ($row = $cat_result->fetch_assoc()) {
        $category_map[$row['name']] = $row['id'];
    }

    $sample_products = [
        ['name' => 'Laptop', 'description' => 'High-performance laptop', 'price' => 999.99, 'stock' => 10, 'category' => 'Electronics'],
        ['name' => 'Smartphone', 'description' => 'Latest smartphone model', 'price' => 599.99, 'stock' => 15, 'category' => 'Electronics'],
        ['name' => 'T-Shirt', 'description' => 'Cotton t-shirt', 'price' => 19.99, 'stock' => 50, 'category' => 'Clothing'],
        ['name' => 'Coffee', 'description' => 'Premium coffee beans', 'price' => 12.99, 'stock' => 25, 'category' => 'Food & Beverages'],
        ['name' => 'Desk Lamp', 'description' => 'LED desk lamp', 'price' => 29.99, 'stock' => 20, 'category' => 'Home & Garden'],
        ['name' => 'Programming Book', 'description' => 'Learn PHP programming', 'price' => 39.99, 'stock' => 8, 'category' => 'Books'],
        ['name' => 'Basketball', 'description' => 'Official size basketball', 'price' => 24.99, 'stock' => 12, 'category' => 'Sports']
    ];

    foreach ($sample_products as $product) {
        $category_id = isset($category_map[$product['category']]) ? $category_map[$product['category']] : null;
        $sql = "INSERT INTO products (name, description, price, stock, category_id, low_stock_threshold) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $low_stock_threshold = 5;
        $stmt->bind_param("ssdiis", $product['name'], $product['description'], $product['price'], $product['stock'], $category_id, $low_stock_threshold);
        $stmt->execute();
    }
}
?> 