<?php
// Handle CSV export first, before any other processing
if (isset($_GET['export']) && $_GET['export'] === 'csv' && isset($_GET['page']) && $_GET['page'] === 'reports') {
    session_start();
    require_once 'config/database.php';
    require_once 'includes/functions.php';
    
    // Check if user is logged in
    if (!isset($_SESSION['user_id'])) {
        header('Location: login.php');
        exit();
    }
    
    // Check if user is admin
    if (!isAdmin()) {
        header('Location: index.php?page=reports&error=access_denied');
        exit();
    }
    
    $start_date = $_GET['start_date'] ?? date('Y-m-01');
    $end_date = $_GET['end_date'] ?? date('Y-m-d');
    
    // Get sales report data
    $sales_data = getSalesReport($conn, $start_date, $end_date);
    
    // Prepare CSV data
    $csv_data = [];
    while ($sale = $sales_data->fetch_assoc()) {
        $csv_data[] = [
            'Invoice' => $sale['invoice_number'],
            'Date' => date('Y-m-d H:i', strtotime($sale['created_at'])),
            'Customer' => $sale['customer_name'] ?? 'Walk-in',
            'Cashier' => $sale['cashier_name'],
            'Subtotal' => $sale['total_amount'],
            'Discount' => $sale['discount'],
            'Total' => $sale['final_amount'],
            'Payment Method' => ucfirst($sale['payment_method'])
        ];
    }
    
    if (!exportToCSV($csv_data, 'sales_report_' . date('Y-m-d') . '.csv')) {
        // If headers were already sent, redirect with error
        header('Location: index.php?page=reports&error=export_failed');
        exit();
    }
    exit();
}

session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';

// Check if user is logged in
if (!isset($_SESSION['user_id']) && basename($_SERVER['PHP_SELF']) !== 'login.php') {
    header('Location: login.php');
    exit();
}

// Get current page
$page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';

// Handle form processing and redirects before including header
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    switch ($page) {
        case 'products':
            $action = isset($_GET['action']) ? $_GET['action'] : 'list';
            if ($action === 'add' || $action === 'edit') {
                $product_id = isset($_GET['id']) ? $_GET['id'] : null;
                
                $name = sanitizeInput($_POST['name']);
                $description = sanitizeInput($_POST['description']);
                $price = floatval($_POST['price']);
                $stock = intval($_POST['stock']);
                $category_id = !empty($_POST['category_id']) ? $_POST['category_id'] : null;
                $low_stock_threshold = intval($_POST['low_stock_threshold']);
                
                // Handle image upload
                $image_path = null;
                if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                    $image_path = uploadProductImage($_FILES['image']);
                }
                
                if (!empty($name) && $price > 0) {
                    if ($action === 'add') {
                        $sql = "INSERT INTO products (name, description, price, stock, category_id, low_stock_threshold, image) 
                                VALUES (?, ?, ?, ?, ?, ?, ?)";
                        $stmt = $conn->prepare($sql);
                        $stmt->bind_param("ssdiiss", $name, $description, $price, $stock, $category_id, $low_stock_threshold, $image_path);
                    } else {
                        // For edit, check if we need to update image
                        if ($image_path) {
                            // Delete old image if exists
                            $old_product = getProduct($conn, $product_id);
                            if ($old_product && $old_product['image']) {
                                deleteProductImage($old_product['image']);
                            }
                            
                            $sql = "UPDATE products SET name=?, description=?, price=?, stock=?, category_id=?, low_stock_threshold=?, image=? 
                                    WHERE id=?";
                            $stmt = $conn->prepare($sql);
                            $stmt->bind_param("ssdiissi", $name, $description, $price, $stock, $category_id, $low_stock_threshold, $image_path, $product_id);
                        } else {
                            $sql = "UPDATE products SET name=?, description=?, price=?, stock=?, category_id=?, low_stock_threshold=? 
                                    WHERE id=?";
                            $stmt = $conn->prepare($sql);
                            $stmt->bind_param("ssdiisi", $name, $description, $price, $stock, $category_id, $low_stock_threshold, $product_id);
                        }
                    }
                    
                    if ($stmt->execute()) {
                        header('Location: index.php?page=products&success=1');
                        exit();
                    }
                }
            }
            break;
            
        case 'customers':
            $action = isset($_GET['action']) ? $_GET['action'] : 'list';
            if ($action === 'add' || $action === 'edit') {
                $customer_id = isset($_GET['id']) ? $_GET['id'] : null;
                
                $name = sanitizeInput($_POST['name']);
                $email = sanitizeInput($_POST['email']);
                $phone = sanitizeInput($_POST['phone']);
                $address = sanitizeInput($_POST['address']);
                
                if (!empty($name)) {
                    if ($action === 'add') {
                        $sql = "INSERT INTO customers (name, email, phone, address) VALUES (?, ?, ?, ?)";
                        $stmt = $conn->prepare($sql);
                        $stmt->bind_param("ssss", $name, $email, $phone, $address);
                    } else {
                        $sql = "UPDATE customers SET name=?, email=?, phone=?, address=? WHERE id=?";
                        $stmt = $conn->prepare($sql);
                        $stmt->bind_param("ssssi", $name, $email, $phone, $address, $customer_id);
                    }
                    
                    if ($stmt->execute()) {
                        header('Location: index.php?page=customers&success=1');
                        exit();
                    }
                }
            }
            break;
            
                    case 'users':
                $action = isset($_GET['action']) ? $_GET['action'] : 'list';
                if ($action === 'add' || $action === 'edit') {
                    $user_id = isset($_GET['id']) ? $_GET['id'] : null;
                    
                    $username = sanitizeInput($_POST['username']);
                    $full_name = sanitizeInput($_POST['full_name']);
                    $role = sanitizeInput($_POST['role']);
                    $password = $_POST['password'];
                    $confirm_password = $_POST['confirm_password'];
                    
                    if (empty($username) || empty($full_name) || empty($role)) {
                        // Validation failed, continue to display form with error
                    } elseif ($action === 'add' && empty($password)) {
                        // Password required for new users
                    } elseif (!empty($password) && $password !== $confirm_password) {
                        // Passwords don't match
                    } else {
                        // Check if username already exists (for new users or if username changed)
                        $check_sql = "SELECT id FROM users WHERE username = ? AND id != ?";
                        $stmt = $conn->prepare($check_sql);
                        $check_id = $action === 'add' ? 0 : $user_id;
                        $stmt->bind_param("si", $username, $check_id);
                        $stmt->execute();
                        
                        if ($stmt->get_result()->num_rows > 0) {
                            // Username already exists
                        } else {
                            if ($action === 'add') {
                                $password_hash = password_hash($password, PASSWORD_DEFAULT);
                                $sql = "INSERT INTO users (username, password, full_name, role) VALUES (?, ?, ?, ?)";
                                $stmt = $conn->prepare($sql);
                                $stmt->bind_param("ssss", $username, $password_hash, $full_name, $role);
                            } else {
                                if (!empty($password)) {
                                    $password_hash = password_hash($password, PASSWORD_DEFAULT);
                                    $sql = "UPDATE users SET username=?, password=?, full_name=?, role=? WHERE id=?";
                                    $stmt = $conn->prepare($sql);
                                    $stmt->bind_param("ssssi", $username, $password_hash, $full_name, $role, $user_id);
                                } else {
                                    $sql = "UPDATE users SET username=?, full_name=?, role=? WHERE id=?";
                                    $stmt = $conn->prepare($sql);
                                    $stmt->bind_param("sssi", $username, $full_name, $role, $user_id);
                                }
                            }
                            
                            if ($stmt->execute()) {
                                header('Location: index.php?page=users&success=1');
                                exit();
                            }
                        }
                    }
                }
                break;
                
            case 'sales':
                $action = isset($_GET['action']) ? $_GET['action'] : 'list';
                if ($action === 'new') {
                    $customer_id = !empty($_POST['customer_id']) ? $_POST['customer_id'] : null;
                    $discount = floatval($_POST['discount'] ?? 0);
                    $payment_method = $_POST['payment_method'] ?? 'cash';
                    $items = $_POST['items'] ?? [];
                    
                    if (!empty($items)) {
                        $invoice_number = generateInvoiceNumber();
                        $total_amount = 0;
                        
                        // Calculate total
                        foreach ($items as $item) {
                            $total_amount += $item['price'] * $item['quantity'];
                        }
                        
                        $final_amount = $total_amount - $discount;
                        
                        // Insert sale
                        $sql = "INSERT INTO sales (invoice_number, user_id, customer_id, total_amount, discount, final_amount, payment_method) 
                                VALUES (?, ?, ?, ?, ?, ?, ?)";
                        $stmt = $conn->prepare($sql);
                        $stmt->bind_param("siiddds", $invoice_number, $_SESSION['user_id'], $customer_id, $total_amount, $discount, $final_amount, $payment_method);
                        
                        if ($stmt->execute()) {
                            $sale_id = $conn->insert_id;
                            
                            // Insert sale items and update stock
                            foreach ($items as $item) {
                                $item_total = $item['price'] * $item['quantity'];
                                
                                $sql = "INSERT INTO sale_items (sale_id, product_id, quantity, price, total) VALUES (?, ?, ?, ?, ?)";
                                $stmt = $conn->prepare($sql);
                                $stmt->bind_param("iiidd", $sale_id, $item['product_id'], $item['quantity'], $item['price'], $item_total);
                                $stmt->execute();
                                
                                // Update product stock
                                updateProductStock($conn, $item['product_id'], $item['quantity']);
                            }
                            
                            header("Location: index.php?page=sales&action=view&id=$sale_id&success=1");
                            exit();
                        }
                    }
                }
                break;
    }
}

// Handle GET actions that need redirects (like delete operations)
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $action = isset($_GET['action']) ? $_GET['action'] : '';
    
    if ($action === 'delete') {
        switch ($page) {
            case 'products':
                if (isset($_GET['id'])) {
                    $product_id = $_GET['id'];
                    
                    // Check if product exists and can be deleted
                    $product = getProduct($conn, $product_id);
                    if ($product) {
                        // Check if product is used in any sales
                        $check_sql = "SELECT COUNT(*) as count FROM sale_items WHERE product_id = ?";
                        $stmt = $conn->prepare($check_sql);
                        $stmt->bind_param("i", $product_id);
                        $stmt->execute();
                        $result = $stmt->get_result();
                        $row = $result->fetch_assoc();
                        
                        if ($row['count'] == 0) {
                            // Delete product image if exists
                            if ($product['image']) {
                                deleteProductImage($product['image']);
                            }
                            
                            $sql = "DELETE FROM products WHERE id = ?";
                            $stmt = $conn->prepare($sql);
                            $stmt->bind_param("i", $product_id);
                            
                            if ($stmt->execute()) {
                                header('Location: index.php?page=products&success=1');
                                exit();
                            } else {
                                header('Location: index.php?page=products&error=delete_failed');
                                exit();
                            }
                        } else {
                            header('Location: index.php?page=products&error=in_use');
                            exit();
                        }
                    } else {
                        header('Location: index.php?page=products&error=not_found');
                        exit();
                    }
                }
                break;
                
            case 'customers':
                if (isset($_GET['id'])) {
                    $customer_id = $_GET['id'];
                    
                    // Check if customer exists
                    $customer = getCustomer($conn, $customer_id);
                    if ($customer) {
                        // Check if customer has any sales
                        $check_sql = "SELECT COUNT(*) as count FROM sales WHERE customer_id = ?";
                        $stmt = $conn->prepare($check_sql);
                        $stmt->bind_param("i", $customer_id);
                        $stmt->execute();
                        $result = $stmt->get_result();
                        $row = $result->fetch_assoc();
                        
                        $has_purchases = $row['count'] > 0;
                        
                        // Check permissions based on purchase history and user role
                        $can_delete = false;
                        if (!$has_purchases) {
                            // No purchase history - both admin and cashier can delete
                            $can_delete = true;
                        } elseif (isAdmin()) {
                            // Has purchase history but user is admin - can delete
                            $can_delete = true;
                        }
                        
                        if ($can_delete) {
                            $sql = "DELETE FROM customers WHERE id = ?";
                            $stmt = $conn->prepare($sql);
                            $stmt->bind_param("i", $customer_id);
                            
                            if ($stmt->execute()) {
                                header('Location: index.php?page=customers&success=1');
                                exit();
                            } else {
                                header('Location: index.php?page=customers&error=delete_failed');
                                exit();
                            }
                        } else {
                            // Cashier trying to delete customer with purchase history
                            header('Location: index.php?page=customers&error=permission_denied');
                            exit();
                        }
                    } else {
                        header('Location: index.php?page=customers&error=not_found');
                        exit();
                    }
                }
                break;
                
            case 'users':
                if (isset($_GET['id'])) {
                    $user_id = $_GET['id'];
                    
                    // Prevent deleting own account
                    if ($user_id != $_SESSION['user_id']) {
                        // Check if user exists
                        $user = getUser($conn, $user_id);
                        if ($user) {
                            // Check if user has any sales records
                            if (!userHasSales($conn, $user_id)) {
                                // No sales records, safe to delete
                                $sql = "DELETE FROM users WHERE id = ?";
                                $stmt = $conn->prepare($sql);
                                $stmt->bind_param("i", $user_id);
                                
                                if ($stmt->execute()) {
                                    header('Location: index.php?page=users&success=1');
                                    exit();
                                } else {
                                    header('Location: index.php?page=users&error=delete_failed');
                                    exit();
                                }
                            } else {
                                // User has sales records, cannot delete
                                header('Location: index.php?page=users&error=has_sales');
                                exit();
                            }
                        } else {
                            header('Location: index.php?page=users&error=not_found');
                            exit();
                        }
                    } else {
                        header('Location: index.php?page=users&error=cannot_delete_self');
                        exit();
                    }
                }
                break;
        }
    }
}

// Include header
include 'includes/header.php';

// Route to appropriate page
switch ($page) {
    case 'dashboard':
        include 'pages/dashboard.php';
        break;
    case 'sales':
        include 'pages/sales.php';
        break;
    case 'products':
        include 'pages/products.php';
        break;
    case 'customers':
        include 'pages/customers.php';
        break;
    case 'reports':
        include 'pages/reports.php';
        break;
    case 'users':
        include 'pages/users.php';
        break;
    case 'profile':
        include 'pages/profile.php';
        break;
    case 'about':
        include 'pages/about.php';
        break;
    default:
        include 'pages/dashboard.php';
}

// Include footer
include 'includes/footer.php';
?>