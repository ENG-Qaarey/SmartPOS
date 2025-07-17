<?php
// Check if user is admin
if (!isAdmin()) {
    echo '<div class="alert alert-danger">Access denied. Admin privileges required.</div>';
    return;
}

$start_date = $_GET['start_date'] ?? date('Y-m-01'); // First day of current month
$end_date = $_GET['end_date'] ?? date('Y-m-d'); // Today

// Get sales report data
$sales_data = getSalesReport($conn, $start_date, $end_date);

// Calculate totals
$total_sales = 0;
$total_discount = 0;
$total_transactions = 0;
$sales_array = [];

while ($sale = $sales_data->fetch_assoc()) {
    $total_sales += $sale['final_amount'];
    $total_discount += $sale['discount'];
    $total_transactions++;
    $sales_array[] = $sale;
}

// Get daily sales for chart
$daily_sales_sql = "SELECT DATE(created_at) as date, SUM(final_amount) as total 
                    FROM sales 
                    WHERE DATE(created_at) BETWEEN ? AND ? 
                    GROUP BY DATE(created_at) 
                    ORDER BY date";
$stmt = $conn->prepare($daily_sales_sql);
$stmt->bind_param("ss", $start_date, $end_date);
$stmt->execute();
$daily_sales = $stmt->get_result();

$chart_labels = [];
$chart_data = [];

while ($day = $daily_sales->fetch_assoc()) {
    $chart_labels[] = date('M d', strtotime($day['date']));
    $chart_data[] = $day['total'];
}

// Calculate total cashier payouts
$total_cashier_payouts = 0;
$users_sql = "SELECT id, role FROM users";
$users = $conn->query($users_sql);
if ($users) {
    while ($user = $users->fetch_assoc()) {
        if ($user['role'] === 'cashier') {
            // Count active days for this cashier
            $sql = "SELECT COUNT(DISTINCT DATE(created_at)) as active_days FROM sales WHERE user_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $user['id']);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            $active_days = $row['active_days'] ?? 0;
            $hours = $active_days * 8;
            $total_cashier_payouts += $hours * 2;
        }
    }
}
?>

<div class="row mb-4">
    <div class="col-12">
        <h2><i class="fas fa-chart-bar"></i> Sales Reports</h2>
    </div>
</div>

<!-- Date Filter -->
<div class="card mb-4">
    <div class="card-header">
        <h5 class="mb-0"><i class="fas fa-filter"></i> Filter Options</h5>
    </div>
    <div class="card-body">
        <form method="GET" class="row">
            <input type="hidden" name="page" value="reports">
            <div class="col-md-3">
                <label class="form-label">Start Date</label>
                <input type="date" class="form-control" name="start_date" value="<?php echo $start_date; ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label">End Date</label>
                <input type="date" class="form-control" name="end_date" value="<?php echo $end_date; ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label">&nbsp;</label>
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-search"></i> Filter
                </button>
            </div>
            <div class="col-md-3">
                <label class="form-label">&nbsp;</label>
                <a href="index.php?page=reports&start_date=<?php echo $start_date; ?>&end_date=<?php echo $end_date; ?>&export=csv" 
                   class="btn btn-success w-100">
                    <i class="fas fa-download"></i> Export CSV
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Summary Cards -->
<div class="row mb-4">
    <div class="col-md-3 mb-3">
        <div class="card text-white bg-primary">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h5 class="card-title">Total Sales</h5>
                        <h3><?php echo formatCurrency($total_sales); ?></h3>
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
                        <h5 class="card-title">Transactions</h5>
                        <h3><?php echo $total_transactions; ?></h3>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-receipt fa-2x"></i>
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
                        <h5 class="card-title">Total Discount</h5>
                        <h3><?php echo formatCurrency($total_discount); ?></h3>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-percentage fa-2x"></i>
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
                        <h5 class="card-title">Average Sale</h5>
                        <h3><?php echo $total_transactions > 0 ? formatCurrency($total_sales / $total_transactions) : '$0.00'; ?></h3>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-chart-line fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card text-white bg-dark">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h5 class="card-title">Total Cashier Payouts</h5>
                        <h3><?php echo formatCurrency($total_cashier_payouts); ?></h3>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-money-bill-wave fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Sales Chart -->
<?php if (!empty($chart_data)): ?>
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-chart-area"></i> Daily Sales Chart</h5>
            </div>
            <div class="card-body">
                <canvas id="salesChart" width="400" height="200"></canvas>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Sales Details -->
<div class="card">
    <div class="card-header">
        <h5 class="mb-0"><i class="fas fa-list"></i> Sales Details</h5>
    </div>
    <div class="card-body">
        <?php if (!empty($sales_array)): ?>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Invoice</th>
                            <th>Date</th>
                            <th>Customer</th>
                            <th>Cashier</th>
                            <th>Subtotal</th>
                            <th>Discount</th>
                            <th>Total</th>
                            <th>Payment</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($sales_array as $sale): ?>
                            <tr>
                                <td>
                                    <strong><?php echo htmlspecialchars($sale['invoice_number']); ?></strong>
                                </td>
                                <td><?php echo date('M d, Y H:i', strtotime($sale['created_at'])); ?></td>
                                <td><?php echo htmlspecialchars($sale['customer_name'] ?? 'Walk-in'); ?></td>
                                <td><?php echo htmlspecialchars($sale['cashier_name']); ?></td>
                                <td><?php echo formatCurrency($sale['total_amount']); ?></td>
                                <td><?php echo formatCurrency($sale['discount']); ?></td>
                                <td><strong><?php echo formatCurrency($sale['final_amount']); ?></strong></td>
                                <td>
                                    <span class="badge bg-<?php echo $sale['payment_method'] === 'cash' ? 'success' : 'info'; ?>">
                                        <?php echo ucfirst($sale['payment_method']); ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="index.php?page=sales&action=view&id=<?php echo $sale['id']; ?>" 
                                       class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p class="text-muted text-center">No sales found for the selected date range.</p>
        <?php endif; ?>
    </div>
</div>

<?php if (!empty($chart_data)): ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('salesChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: <?php echo json_encode($chart_labels); ?>,
            datasets: [{
                label: 'Daily Sales',
                data: <?php echo json_encode($chart_data); ?>,
                borderColor: 'rgb(102, 126, 234)',
                backgroundColor: 'rgba(102, 126, 234, 0.1)',
                tension: 0.1,
                fill: true
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return '$' + value.toFixed(2);
                        }
                    }
                }
            }
        }
    });
</script>
<?php endif; ?> 