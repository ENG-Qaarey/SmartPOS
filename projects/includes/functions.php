<?php
// Utility functions for SmartPOS

// Check if user is admin
function isAdmin() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}

// Check if user is cashier
function isCashier() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'cashier';
}

// Format currency
function formatCurrency($amount) {
    return '$' . number_format($amount, 2);
}

// Generate invoice number
function generateInvoiceNumber() {
    return 'INV-' . date('Ymd') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
}

// Get low stock products
function getLowStockProducts($conn) {
    $sql = "SELECT p.*, c.name as category_name 
            FROM products p 
            LEFT JOIN categories c ON p.category_id = c.id 
            WHERE p.stock <= p.low_stock_threshold 
            ORDER BY p.stock ASC";
    return $conn->query($sql);
}

// Get today's sales
function getTodaySales($conn) {
    $sql = "SELECT SUM(final_amount) as total FROM sales WHERE DATE(created_at) = CURDATE()";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    return $row['total'] ?? 0;
}

// Get monthly sales
function getMonthlySales($conn) {
    $sql = "SELECT SUM(final_amount) as total FROM sales WHERE MONTH(created_at) = MONTH(CURDATE()) AND YEAR(created_at) = YEAR(CURDATE())";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    return $row['total'] ?? 0;
}

// Get total products
function getTotalProducts($conn) {
    $sql = "SELECT COUNT(*) as total FROM products";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    return $row['total'] ?? 0;
}

// Get total customers
function getTotalCustomers($conn) {
    $sql = "SELECT COUNT(*) as total FROM customers";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    return $row['total'] ?? 0;
}

// Sanitize input
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Validate email
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

// Get product by ID
function getProduct($conn, $id) {
    $sql = "SELECT p.*, c.name as category_name 
            FROM products p 
            LEFT JOIN categories c ON p.category_id = c.id 
            WHERE p.id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

// Get customer by ID
function getCustomer($conn, $id) {
    $sql = "SELECT * FROM customers WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

// Get user by ID
function getUser($conn, $id) {
    $sql = "SELECT id, username, full_name, email, phone, role, created_at FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

// Get user statistics
function getUserStats($conn, $user_id) {
    $sql = "SELECT 
        COUNT(DISTINCT s.id) as total_sales,
        SUM(s.final_amount) as total_revenue,
        COUNT(DISTINCT DATE(s.created_at)) as active_days,
        MAX(s.created_at) as last_activity
    FROM sales s 
    WHERE s.user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

// Get user recent activity
function getUserRecentActivity($conn, $user_id, $limit = 10) {
    $sql = "SELECT s.*, c.name as customer_name 
    FROM sales s 
    LEFT JOIN customers c ON s.customer_id = c.id 
    WHERE s.user_id = ? 
    ORDER BY s.created_at DESC 
    LIMIT ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $user_id, $limit);
    $stmt->execute();
    return $stmt->get_result();
}

// Update user profile
function updateUserProfile($conn, $user_id, $data) {
    $sql = "UPDATE users SET full_name = ?, email = ?, phone = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssi", $data['full_name'], $data['email'], $data['phone'], $user_id);
    return $stmt->execute();
}

// Update user password
function updateUserPassword($conn, $user_id, $password) {
    $password_hash = password_hash($password, PASSWORD_DEFAULT);
    $sql = "UPDATE users SET password = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $password_hash, $user_id);
    return $stmt->execute();
}

// Update product stock
function updateProductStock($conn, $product_id, $quantity) {
    $sql = "UPDATE products SET stock = stock - ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $quantity, $product_id);
    return $stmt->execute();
}

// Get sales report data
function getSalesReport($conn, $start_date, $end_date) {
    $sql = "SELECT s.*, u.full_name as cashier_name, c.name as customer_name
            FROM sales s
            LEFT JOIN users u ON s.user_id = u.id
            LEFT JOIN customers c ON s.customer_id = c.id
            WHERE DATE(s.created_at) BETWEEN ? AND ?
            ORDER BY s.created_at DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $start_date, $end_date);
    $stmt->execute();
    return $stmt->get_result();
}

// Export to CSV
function exportToCSV($data, $filename) {
    // Ensure no output has been sent
    if (headers_sent()) {
        return false;
    }
    
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    
    $output = fopen('php://output', 'w');
    
    // Add a beautiful title row
    fputcsv($output, ["SmartPOS Sales Report"]);
    // Add report generation date
    fputcsv($output, ["Generated: " . date('Y-m-d H:i')]);
    // Add a blank row for clarity
    fputcsv($output, [""]);
    
    // Add headers
    if (!empty($data)) {
        fputcsv($output, array_keys($data[0]));
    }
    
    // Add data, formatting currency columns
    foreach ($data as $row) {
        foreach (["Subtotal","Discount","Total"] as $col) {
            if (isset($row[$col])) {
                $row[$col] = '$' . number_format((float)$row[$col], 2);
            }
        }
        fputcsv($output, $row);
    }
    
    fclose($output);
    return true;
}

// Handle image upload
function uploadProductImage($file) {
    $upload_dir = 'uploads/products/';
    
    // Create directory if it doesn't exist
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }
    
    // Check if file was uploaded
    if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
        return false;
    }
    
    // Validate file type
    $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
    if (!in_array($file['type'], $allowed_types)) {
        return false;
    }
    
    // Validate file size (max 5MB)
    if ($file['size'] > 5 * 1024 * 1024) {
        return false;
    }
    
    // Generate unique filename
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = uniqid() . '.' . $extension;
    $filepath = $upload_dir . $filename;
    
    // Move uploaded file
    if (move_uploaded_file($file['tmp_name'], $filepath)) {
        return $filepath;
    }
    
    return false;
}

// Delete product image
function deleteProductImage($image_path) {
    if ($image_path && file_exists($image_path)) {
        unlink($image_path);
    }
}

// Get product image URL
function getProductImageUrl($image_path) {
    if ($image_path && file_exists($image_path)) {
        return $image_path;
    }
    return 'assets/images/no-image.png';
}

// Check if user has sales records
function userHasSales($conn, $user_id) {
    $sql = "SELECT COUNT(*) as sales_count FROM sales WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return $row['sales_count'] > 0;
}
?> 