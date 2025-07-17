-- =====================================================
-- SMARTPOS DATABASE QUERIES
-- Point of Sale System Database Schema and Queries
-- =====================================================

-- =====================================================
-- TABLE CREATION QUERIES
-- =====================================================

-- Users Table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100),
    phone VARCHAR(20),
    role ENUM('admin', 'cashier') DEFAULT 'cashier',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    last_hourly_credit DATETIME DEFAULT NULL
);

-- Categories Table
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Products Table
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(200) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    stock INT DEFAULT 0,
    category_id INT,
    image VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
);

-- Customers Table
CREATE TABLE IF NOT EXISTS customers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100),
    phone VARCHAR(20),
    address TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Sales Table
CREATE TABLE IF NOT EXISTS sales (
    id INT AUTO_INCREMENT PRIMARY KEY,
    invoice_number VARCHAR(50) UNIQUE NOT NULL,
    user_id INT NOT NULL,
    customer_id INT,
    total_amount DECIMAL(10,2) NOT NULL,
    discount DECIMAL(10,2) DEFAULT 0,
    final_amount DECIMAL(10,2) NOT NULL,
    payment_method ENUM('cash', 'card') DEFAULT 'cash',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE SET NULL
);

-- Sale Items Table
CREATE TABLE IF NOT EXISTS sale_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sale_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    total DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (sale_id) REFERENCES sales(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE RESTRICT
);

-- Cashier Daily Hours Table
CREATE TABLE IF NOT EXISTS cashier_daily_hours (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cashier_id INT NOT NULL,
    date DATE NOT NULL,
    hours_credited INT DEFAULT 0,
    UNIQUE KEY (cashier_id, date),
    FOREIGN KEY (cashier_id) REFERENCES users(id)
);

-- =====================================================
-- SAMPLE DATA INSERTION QUERIES
-- =====================================================

-- Insert Default Admin User
INSERT INTO users (username, password, full_name, email, role) VALUES 
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'System Administrator', 'admin@smartpos.com', 'admin');

-- Insert Sample Categories
INSERT INTO categories (name, description) VALUES 
('Electronics', 'Electronic devices and accessories'),
('Clothing', 'Apparel and fashion items'),
('Food & Beverages', 'Food and drink products'),
('Home & Garden', 'Home improvement and garden items'),
('Books', 'Books and educational materials');

-- Insert Sample Products
INSERT INTO products (name, description, price, stock, category_id) VALUES 
('iPhone 13', 'Latest smartphone with advanced features', 999.99, 50, 1),
('Samsung TV 55"', '4K Ultra HD Smart TV', 799.99, 25, 1),
('Nike Running Shoes', 'Comfortable running shoes', 89.99, 100, 2),
('Coffee Maker', 'Automatic coffee brewing machine', 149.99, 30, 4),
('Programming Book', 'Learn coding from scratch', 29.99, 200, 5),
('Pizza Margherita', 'Classic Italian pizza', 12.99, 50, 3),
('Garden Hose', '50ft heavy-duty garden hose', 39.99, 75, 4),
('Wireless Headphones', 'Bluetooth noise-canceling headphones', 199.99, 40, 1);

-- Insert Sample Customers
INSERT INTO customers (name, email, phone, address) VALUES 
('John Doe', 'john@example.com', '+1234567890', '123 Main St, City, State'),
('Jane Smith', 'jane@example.com', '+0987654321', '456 Oak Ave, Town, State'),
('Mike Johnson', 'mike@example.com', '+1122334455', '789 Pine Rd, Village, State');

-- =====================================================
-- COMMON SELECT QUERIES
-- =====================================================

-- Get all users with their sales count
SELECT 
    u.id,
    u.username,
    u.full_name,
    u.email,
    u.role,
    u.created_at,
    COUNT(s.id) as total_sales,
    SUM(s.final_amount) as total_revenue
FROM users u 
LEFT JOIN sales s ON u.id = s.user_id 
GROUP BY u.id, u.username, u.full_name, u.email, u.role, u.created_at
ORDER BY u.created_at DESC;

-- Get user statistics
SELECT 
    COUNT(DISTINCT s.id) as total_sales,
    SUM(s.final_amount) as total_revenue,
    COUNT(DISTINCT DATE(s.created_at)) as active_days,
    MAX(s.created_at) as last_activity
FROM sales s 
WHERE s.user_id = ?;

-- Get user recent activity
SELECT s.*, c.name as customer_name 
FROM sales s 
LEFT JOIN customers c ON s.customer_id = c.id 
WHERE s.user_id = ? 
ORDER BY s.created_at DESC 
LIMIT 10;

-- Get all products with category information
SELECT 
    p.*,
    c.name as category_name
FROM products p 
LEFT JOIN categories c ON p.category_id = c.id 
WHERE p.stock > 0 
ORDER BY p.name;

-- Get sales with customer and cashier information
SELECT 
    s.*,
    u.full_name as cashier_name,
    c.name as customer_name
FROM sales s 
LEFT JOIN users u ON s.user_id = u.id 
LEFT JOIN customers c ON s.customer_id = c.id 
ORDER BY s.created_at DESC;

-- Get sale details with items
SELECT 
    si.*,
    p.name as product_name
FROM sale_items si 
JOIN products p ON si.product_id = p.id 
WHERE si.sale_id = ?;

-- Get low stock products (stock < 10)
SELECT 
    p.*,
    c.name as category_name
FROM products p 
LEFT JOIN categories c ON p.category_id = c.id 
WHERE p.stock < 10 
ORDER BY p.stock ASC;

-- Get top selling products
SELECT 
    p.name,
    p.price,
    SUM(si.quantity) as total_sold,
    SUM(si.total) as total_revenue
FROM sale_items si 
JOIN products p ON si.product_id = p.id 
JOIN sales s ON si.sale_id = s.id 
GROUP BY p.id, p.name, p.price 
ORDER BY total_sold DESC 
LIMIT 10;

-- Get daily sales report
SELECT 
    DATE(s.created_at) as sale_date,
    COUNT(s.id) as total_sales,
    SUM(s.final_amount) as total_revenue,
    AVG(s.final_amount) as average_sale
FROM sales s 
WHERE DATE(s.created_at) BETWEEN ? AND ?
GROUP BY DATE(s.created_at)
ORDER BY sale_date DESC;

-- Get monthly sales report
SELECT 
    DATE_FORMAT(s.created_at, '%Y-%m') as sale_month,
    COUNT(s.id) as total_sales,
    SUM(s.final_amount) as total_revenue,
    AVG(s.final_amount) as average_sale
FROM sales s 
WHERE DATE_FORMAT(s.created_at, '%Y-%m') BETWEEN ? AND ?
GROUP BY DATE_FORMAT(s.created_at, '%Y-%m')
ORDER BY sale_month DESC;

-- Get cashier earnings (for cashiers)
SELECT 
    u.id,
    u.full_name,
    COUNT(DISTINCT DATE(s.created_at)) as active_days,
    COUNT(DISTINCT DATE(s.created_at)) * 8 as hours_worked,
    COUNT(DISTINCT DATE(s.created_at)) * 8 * 2 as total_earned
FROM users u 
LEFT JOIN sales s ON u.id = s.user_id 
WHERE u.role = 'cashier'
GROUP BY u.id, u.full_name;

-- Get monthly earnings for specific cashier
SELECT 
    COUNT(DISTINCT DATE(s.created_at)) as active_days,
    COUNT(DISTINCT DATE(s.created_at)) * 8 as hours_worked,
    COUNT(DISTINCT DATE(s.created_at)) * 8 * 2 as monthly_earned
FROM sales s 
WHERE s.user_id = ? 
AND DATE_FORMAT(s.created_at, '%Y-%m') = ?;

-- =====================================================
-- UPDATE QUERIES
-- =====================================================

-- Update user profile
UPDATE users 
SET full_name = ?, email = ?, phone = ? 
WHERE id = ?;

-- Update user password
UPDATE users 
SET password = ? 
WHERE id = ?;

-- Update product stock after sale
UPDATE products 
SET stock = stock - ? 
WHERE id = ?;

-- Update product information
UPDATE products 
SET name = ?, description = ?, price = ?, stock = ?, category_id = ? 
WHERE id = ?;

-- =====================================================
-- DELETE QUERIES
-- =====================================================

-- Delete user (only if no sales records)
DELETE FROM users 
WHERE id = ? 
AND NOT EXISTS (SELECT 1 FROM sales WHERE user_id = users.id);

-- Delete product (only if no sale records)
DELETE FROM products 
WHERE id = ? 
AND NOT EXISTS (SELECT 1 FROM sale_items WHERE product_id = products.id);

-- Delete customer (only if no sales records)
DELETE FROM customers 
WHERE id = ? 
AND NOT EXISTS (SELECT 1 FROM sales WHERE customer_id = customers.id);

-- =====================================================
-- UTILITY QUERIES
-- =====================================================

-- Generate unique invoice number
SELECT CONCAT('INV-', DATE_FORMAT(NOW(), '%Y%m%d'), '-', LPAD(COALESCE(MAX(SUBSTRING_INDEX(invoice_number, '-', -1)), 0) + 1, 4, '0')) as next_invoice
FROM sales 
WHERE invoice_number LIKE CONCAT('INV-', DATE_FORMAT(NOW(), '%Y%m%d'), '-%');

-- Check if user has sales records
SELECT COUNT(*) as sales_count 
FROM sales 
WHERE user_id = ?;

-- Check if product has sale records
SELECT COUNT(*) as sales_count 
FROM sale_items 
WHERE product_id = ?;

-- Get total system statistics
SELECT 
    (SELECT COUNT(*) FROM users) as total_users,
    (SELECT COUNT(*) FROM products) as total_products,
    (SELECT COUNT(*) FROM customers) as total_customers,
    (SELECT COUNT(*) FROM sales) as total_sales,
    (SELECT SUM(final_amount) FROM sales) as total_revenue;

-- =====================================================
-- INDEXES FOR BETTER PERFORMANCE
-- =====================================================

-- Create indexes for better query performance
CREATE INDEX idx_sales_user_id ON sales(user_id);
CREATE INDEX idx_sales_customer_id ON sales(customer_id);
CREATE INDEX idx_sales_created_at ON sales(created_at);
CREATE INDEX idx_sale_items_sale_id ON sale_items(sale_id);
CREATE INDEX idx_sale_items_product_id ON sale_items(product_id);
CREATE INDEX idx_products_category_id ON products(category_id);
CREATE INDEX idx_products_stock ON products(stock);
CREATE INDEX idx_users_role ON users(role);
CREATE INDEX idx_users_username ON users(username);

-- =====================================================
-- VIEWS FOR COMMON REPORTS
-- =====================================================

-- Create view for sales summary
CREATE VIEW sales_summary AS
SELECT 
    s.id,
    s.invoice_number,
    s.created_at,
    u.full_name as cashier_name,
    c.name as customer_name,
    s.total_amount,
    s.discount,
    s.final_amount,
    s.payment_method,
    COUNT(si.id) as item_count
FROM sales s
LEFT JOIN users u ON s.user_id = u.id
LEFT JOIN customers c ON s.customer_id = c.id
LEFT JOIN sale_items si ON s.id = si.sale_id
GROUP BY s.id, s.invoice_number, s.created_at, u.full_name, c.name, s.total_amount, s.discount, s.final_amount, s.payment_method;

-- Create view for product sales
CREATE VIEW product_sales AS
SELECT 
    p.id,
    p.name,
    p.price,
    p.stock,
    c.name as category_name,
    COUNT(si.id) as times_sold,
    SUM(si.quantity) as total_quantity_sold,
    SUM(si.total) as total_revenue
FROM products p
LEFT JOIN categories c ON p.category_id = c.id
LEFT JOIN sale_items si ON p.id = si.product_id
GROUP BY p.id, p.name, p.price, p.stock, c.name;

-- =====================================================
-- END OF DATABASE QUERIES
-- ===================================================== 

-- =====================================================
-- UPDATE QUERIES
-- =====================================================

-- Update sales table to allow NULL cashier_id
ALTER TABLE sales DROP FOREIGN KEY sales_ibfk_1;
ALTER TABLE sales ADD CONSTRAINT sales_ibfk_1 FOREIGN KEY (cashier_id) REFERENCES users(id) ON DELETE SET NULL; 