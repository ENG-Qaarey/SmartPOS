<?php
// Get dashboard statistics
$today_sales = getTodaySales($conn);
$monthly_sales = getMonthlySales($conn);
$total_products = getTotalProducts($conn);
$total_customers = getTotalCustomers($conn);

// Get low stock alerts
$low_stock_products = getLowStockProducts($conn);

// Get recent sales
$recent_sales_sql = "SELECT s.*, u.full_name as cashier_name, c.name as customer_name 
                     FROM sales s 
                     LEFT JOIN users u ON s.user_id = u.id 
                     LEFT JOIN customers c ON s.customer_id = c.id 
                     ORDER BY s.created_at DESC LIMIT 5";
$recent_sales = $conn->query($recent_sales_sql);
?>

<!-- Beautiful Welcome Section -->
<div class="welcome-section mb-4">
    <div class="welcome-container">
        <div class="welcome-content">
            <div class="welcome-icon">
                <i class="fas fa-user-circle animate-float"></i>
            </div>
            <div class="welcome-text">
                <h1 class="welcome-title">
                    Welcome back, 
                    <span class="user-name"><?php echo htmlspecialchars($_SESSION['full_name']); ?></span>
                    <i class="fas fa-hand-wave animate-wave"></i>
                </h1>
                <p class="welcome-subtitle">
                    <i class="fas fa-clock animate-pulse"></i>
                    Ready to serve customers today!
                </p>
                <!-- <div class="welcome-heart">
                    <i class="fas fa-heart animate-heart"></i>
                </div> -->
                <div class="welcome-time">
                    <i class="fas fa-calendar-alt"></i>
                    <?php echo date('l, F j, Y'); ?>
                    <i class="fas fa-clock"></i>
                    <?php echo date('g:i A'); ?>
                </div>
            </div>
            <div class="welcome-decoration">
                <i class="fas fa-star animate-glow"></i>
                <i class="fas fa-star animate-glow-delayed"></i>
                <i class="fas fa-star animate-glow-slow"></i>
            </div>
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-md-3 mb-3">
        <div class="card text-white bg-primary">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h5 class="card-title">Today's Sales</h5>
                        <h3><?php echo formatCurrency($today_sales); ?></h3>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-dollar-sign fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-3">
        <div class="card text-white bg-success">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h5 class="card-title">Monthly Sales</h5>
                        <h3><?php echo formatCurrency($monthly_sales); ?></h3>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-chart-line fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-3">
        <div class="card text-white bg-info">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h5 class="card-title">Total Products</h5>
                        <h3><?php echo $total_products; ?></h3>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-box fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-3">
        <div class="card text-white bg-warning">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h5 class="card-title">Total Customers</h5>
                        <h3><?php echo $total_customers; ?></h3>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-users fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Low Stock Alerts -->
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-exclamation-triangle"></i> Low Stock Alerts</h5>
            </div>
            <div class="card-body">
                <?php if ($low_stock_products->num_rows > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Category</th>
                                    <th>Stock</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($product = $low_stock_products->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($product['name']); ?></td>
                                        <td><?php echo htmlspecialchars($product['category_name'] ?? 'N/A'); ?></td>
                                        <td>
                                            <span class="badge bg-danger"><?php echo $product['stock']; ?></span>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="text-muted mb-0">No low stock alerts.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Recent Sales -->
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-clock"></i> Recent Sales</h5>
            </div>
            <div class="card-body">
                <?php if ($recent_sales->num_rows > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Invoice</th>
                                    <th>Customer</th>
                                    <th>Amount</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($sale = $recent_sales->fetch_assoc()): ?>
                                    <tr>
                                        <td>
                                            <a href="index.php?page=sales&action=view&id=<?php echo $sale['id']; ?>" 
                                               class="text-decoration-none">
                                                <?php echo htmlspecialchars($sale['invoice_number']); ?>
                                            </a>
                                        </td>
                                        <td><?php echo htmlspecialchars($sale['customer_name'] ?? 'Walk-in'); ?></td>
                                        <td><?php echo formatCurrency($sale['final_amount']); ?></td>
                                        <td><?php echo date('M d, H:i', strtotime($sale['created_at'])); ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="text-muted mb-0">No recent sales.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-bolt"></i> Quick Actions</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 mb-2">
                        <a href="index.php?page=sales" class="btn btn-primary w-100">
                            <i class="fas fa-plus"></i> New Sale
                        </a>
                    </div>
                    <div class="col-md-3 mb-2">
                        <a href="index.php?page=products" class="btn btn-success w-100">
                            <i class="fas fa-box"></i> Manage Products
                        </a>
                    </div>
                    <div class="col-md-3 mb-2">
                        <a href="index.php?page=customers" class="btn btn-info w-100">
                            <i class="fas fa-users"></i> Manage Customers
                        </a>
                    </div>
                    <?php if (isAdmin()): ?>
                    <div class="col-md-3 mb-2">
                        <a href="index.php?page=reports" class="btn btn-warning w-100">
                            <i class="fas fa-chart-bar"></i> View Reports
                        </a>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div> 