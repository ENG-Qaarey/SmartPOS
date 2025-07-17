<?php
$action = isset($_GET['action']) ? $_GET['action'] : 'list';

if ($action === 'add' || $action === 'edit') {
    $customer_id = isset($_GET['id']) ? $_GET['id'] : null;
    $customer = null;
    
    if ($action === 'edit' && $customer_id) {
        $customer = getCustomer($conn, $customer_id);
        if (!$customer) {
            echo '<div class="alert alert-danger">Customer not found.</div>';
            return;
        }
    }
    ?>
    
    <div class="row mb-4">
        <div class="col-12">
            <h2><i class="fas fa-users"></i> <?php echo $action === 'add' ? 'Add Customer' : 'Edit Customer'; ?></h2>
        </div>
    </div>
    
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Customer Information</h5>
        </div>
        <div class="card-body">
            <form method="POST">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Full Name *</label>
                            <input type="text" class="form-control" name="name" 
                                   value="<?php echo htmlspecialchars($customer['name'] ?? ''); ?>" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" name="email" 
                                   value="<?php echo htmlspecialchars($customer['email'] ?? ''); ?>">
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Phone</label>
                            <input type="tel" class="form-control" name="phone" 
                                   value="<?php echo htmlspecialchars($customer['phone'] ?? ''); ?>">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Address</label>
                            <textarea class="form-control" name="address" rows="3"><?php echo htmlspecialchars($customer['address'] ?? ''); ?></textarea>
                        </div>
                    </div>
                </div>
                
                <div class="text-end">
                    <a href="index.php?page=customers" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Cancel
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Save Customer
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <?php
} elseif ($action === 'history' && isset($_GET['id'])) {
    $customer_id = $_GET['id'];
    $customer = getCustomer($conn, $customer_id);
    
    if (!$customer) {
        echo '<div class="alert alert-danger">Customer not found.</div>';
        return;
    }
    
    // Get customer's purchase history
    $history_sql = "SELECT s.*, u.full_name as cashier_name 
                    FROM sales s 
                    LEFT JOIN users u ON s.user_id = u.id 
                    WHERE s.customer_id = ? 
                    ORDER BY s.created_at DESC";
    $stmt = $conn->prepare($history_sql);
    $stmt->bind_param("i", $customer_id);
    $stmt->execute();
    $history = $stmt->get_result();
    ?>
    
    <div class="row mb-4">
        <div class="col-12">
            <h2><i class="fas fa-history"></i> Purchase History</h2>
            <p class="text-muted">Customer: <strong><?php echo htmlspecialchars($customer['name']); ?></strong></p>
        </div>
    </div>
    
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Transaction History</h5>
        </div>
        <div class="card-body">
            <?php if ($history->num_rows > 0): ?>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Invoice</th>
                                <th>Date</th>
                                <th>Cashier</th>
                                <th>Amount</th>
                                <th>Payment</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($sale = $history->fetch_assoc()): ?>
                                <tr>
                                    <td>
                                        <strong><?php echo htmlspecialchars($sale['invoice_number']); ?></strong>
                                    </td>
                                    <td><?php echo date('M d, Y H:i', strtotime($sale['created_at'])); ?></td>
                                    <td><?php echo htmlspecialchars($sale['cashier_name']); ?></td>
                                    <td><?php echo formatCurrency($sale['total_amount']); ?></td>
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
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="text-center py-4">
                    <i class="fas fa-history fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No purchase history</h5>
                    <p class="text-muted">This customer hasn't made any purchases yet.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <div class="mt-3">
        <a href="index.php?page=customers" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Customers
        </a>
    </div>
    
    <?php
} else {
    // List all customers
    $customers_sql = "SELECT c.*, COUNT(s.id) as total_sales, SUM(s.total_amount) as total_spent 
                      FROM customers c 
                      LEFT JOIN sales s ON c.id = s.customer_id 
                      GROUP BY c.id 
                      ORDER BY c.name";
    $customers = $conn->query($customers_sql);
    ?>
    
    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h2><i class="fas fa-users"></i> Customers</h2>
            <a href="index.php?page=customers&action=add" class="btn btn-primary">
                <i class="fas fa-plus"></i> Add Customer
            </a>
        </div>
    </div>
    

    
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-list"></i> Customer Directory</h5>
        </div>
        <div class="card-body">
            <?php if ($customers->num_rows > 0): ?>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Customer</th>
                                <th>Contact</th>
                                <th>Total Sales</th>
                                <th>Total Spent</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($customer = $customers->fetch_assoc()): ?>
                                <tr>
                                    <td>
                                        <strong><?php echo htmlspecialchars($customer['name']); ?></strong>
                                        <?php if ($customer['address']): ?>
                                            <br><small class="text-muted"><?php echo htmlspecialchars($customer['address']); ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($customer['email']): ?>
                                            <div><i class="fas fa-envelope"></i> <?php echo htmlspecialchars($customer['email']); ?></div>
                                        <?php endif; ?>
                                        <?php if ($customer['phone']): ?>
                                            <div><i class="fas fa-phone"></i> <?php echo htmlspecialchars($customer['phone']); ?></div>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="badge bg-info"><?php echo $customer['total_sales']; ?> transactions</span>
                                    </td>
                                    <td><?php echo formatCurrency($customer['total_spent'] ?? 0); ?></td>
                                    <td>
                                        <a href="index.php?page=customers&action=history&id=<?php echo $customer['id']; ?>" 
                                           class="btn btn-sm btn-outline-info">
                                            <i class="fas fa-history"></i>
                                        </a>
                                        <a href="index.php?page=customers&action=edit&id=<?php echo $customer['id']; ?>" 
                                           class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <?php 
                                        // Show delete button based on conditions:
                                        // 1. If customer has no purchase history, both admin and cashier can delete
                                        // 2. If customer has purchase history, only admin can delete
                                        $can_delete = false;
                                        if ($customer['total_sales'] == 0) {
                                            // No purchase history - both admin and cashier can delete
                                            $can_delete = true;
                                        } elseif (isAdmin()) {
                                            // Has purchase history but user is admin - can delete
                                            $can_delete = true;
                                        }
                                        
                                        if ($can_delete): 
                                        ?>
                                        <a href="index.php?page=customers&action=delete&id=<?php echo $customer['id']; ?>" 
                                           class="btn btn-sm btn-outline-danger delete-customer-btn"
                                           data-customer-name="<?php echo htmlspecialchars($customer['name']); ?>"
                                           data-customer-id="<?php echo $customer['id']; ?>"
                                           data-has-purchases="<?php echo $customer['total_sales'] > 0 ? 'true' : 'false'; ?>">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="text-center py-4">
                    <i class="fas fa-users fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No customers found</h5>
                    <p class="text-muted">Start by adding your first customer.</p>
                    <a href="index.php?page=customers&action=add" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Add Customer
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <?php
}
?>

<!-- SweetAlert2 CDN -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
// Add SweetAlert for delete confirmation
document.addEventListener('DOMContentLoaded', function() {
    const deleteButtons = document.querySelectorAll('.delete-customer-btn');
    
    deleteButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            
            const customerName = this.getAttribute('data-customer-name');
            const customerId = this.getAttribute('data-customer-id');
            
            const hasPurchases = this.getAttribute('data-has-purchases') === 'true';
            const isAdmin = <?php echo isAdmin() ? 'true' : 'false'; ?>;
            
            let title = 'Delete Customer?';
            let html = `Are you sure you want to delete <strong>${customerName}</strong>?<br><br>`;
            
            if (hasPurchases && isAdmin) {
                html += `<small class="text-warning"><i class="fas fa-exclamation-triangle"></i> This customer has purchase history. Only administrators can delete customers with purchase history.</small>`;
            } else if (hasPurchases && !isAdmin) {
                html += `<small class="text-danger"><i class="fas fa-ban"></i> This customer has purchase history. Only administrators can delete customers with purchase history.</small>`;
            } else {
                html += `<small class="text-muted">This action cannot be undone.</small>`;
            }
            
            Swal.fire({
                title: title,
                html: html,
                icon: hasPurchases ? 'warning' : 'question',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete!',
                cancelButtonText: 'Cancel',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = `index.php?page=customers&action=delete&id=${customerId}`;
                }
            });
        });
    });
    
    // Show SweetAlert for success/error messages
    <?php if (isset($_GET['success'])): ?>
    Swal.fire({
        title: 'Success!',
        text: 'Customer saved successfully!',
        icon: 'success',
        timer: 3000,
        timerProgressBar: true,
        showConfirmButton: false
    });
    <?php endif; ?>
    
    <?php if (isset($_GET['error'])): ?>
    Swal.fire({
        title: 'Error!',
        text: '<?php 
        switch ($_GET['error']) {
            case 'delete_failed':
                echo 'Error deleting customer.';
                break;
            case 'in_use':
                echo 'Cannot delete this customer as they have purchase history.';
                break;
            case 'permission_denied':
                echo 'Only administrators can delete customers with purchase history.';
                break;
            case 'not_found':
                echo 'Customer not found.';
                break;
            default:
                echo 'An error occurred.';
        }
        ?>',
        icon: 'error',
        confirmButtonColor: '#3085d6'
    });
    <?php endif; ?>
});
</script> 