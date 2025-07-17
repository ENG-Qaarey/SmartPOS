<?php
// Check if user is admin
if (!isAdmin()) {
    echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>';
    echo '<script>
        document.addEventListener("DOMContentLoaded", function() {
            Swal.fire({
                title: "🚫 Access Denied",
                text: "Admin privileges required to access this page.",
                icon: "error",
                confirmButtonText: "Go Back",
                confirmButtonColor: "#dc3545",
                background: "linear-gradient(135deg, #dc3545 0%, #c82333 100%)",
                backdrop: "rgba(0, 0, 0, 0.4)",
                customClass: {
                    popup: "animated fadeInUp",
                    title: "text-white",
                    content: "text-white"
                }
            }).then(() => {
                window.location.href = "index.php";
            });
        });
    </script>';
    return;
}

$action = isset($_GET['action']) ? $_GET['action'] : 'list';

if ($action === 'add' || $action === 'edit' || $action === 'view') {
    $user_id = isset($_GET['id']) ? $_GET['id'] : null;
    $user = null;
    
    if ($action === 'edit' && $user_id) {
        $user = getUser($conn, $user_id);
        if (!$user) {
            echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>';
            echo '<script>
                document.addEventListener("DOMContentLoaded", function() {
                    Swal.fire({
                        title: "❌ User Not Found",
                        text: "The requested user could not be found.",
                        icon: "error",
                        confirmButtonText: "Go Back",
                        confirmButtonColor: "#dc3545",
                        background: "linear-gradient(135deg, #dc3545 0%, #c82333 100%)",
                        backdrop: "rgba(0, 0, 0, 0.4)",
                        customClass: {
                            popup: "animated fadeInUp",
                            title: "text-white",
                            content: "text-white"
                        }
                    }).then(() => {
                        window.location.href = "index.php?page=users";
                    });
                });
            </script>';
            return;
        }
    }
    ?>
    
    <div class="row mb-4">
        <div class="col-12">
            <h2><i class="fas fa-user-cog"></i> 
                <?php 
                switch ($action) {
                    case 'add': echo 'Add User'; break;
                    case 'edit': echo 'Edit User'; break;
                    case 'view': echo 'User Profile'; break;
                }
                ?>
            </h2>
        </div>
    </div>
    
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">User Information</h5>
        </div>
        <div class="card-body">
            <form method="POST">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Username *</label>
                            <input type="text" class="form-control" name="username" 
                                   value="<?php echo htmlspecialchars($user['username'] ?? ''); ?>" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Full Name *</label>
                            <input type="text" class="form-control" name="full_name" 
                                   value="<?php echo htmlspecialchars($user['full_name'] ?? ''); ?>" required>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" name="email" 
                                   value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Phone</label>
                            <input type="tel" class="form-control" name="phone" 
                                   value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>">
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Role *</label>
                            <select class="form-select" name="role" required>
                                <option value="">Select Role</option>
                                <option value="admin" <?php echo ($user['role'] ?? '') === 'admin' ? 'selected' : ''; ?>>Admin</option>
                                <option value="cashier" <?php echo ($user['role'] ?? '') === 'cashier' ? 'selected' : ''; ?>>Cashier</option>
                            </select>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Password <?php echo $action === 'add' ? '*' : '(leave blank to keep current)'; ?></label>
                            <input type="password" class="form-control" name="password" 
                                   <?php echo $action === 'add' ? 'required' : ''; ?>>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Confirm Password <?php echo $action === 'add' ? '*' : '(leave blank to keep current)'; ?></label>
                            <input type="password" class="form-control" name="confirm_password" 
                                   <?php echo $action === 'add' ? 'required' : ''; ?>>
                        </div>
                    </div>
                </div>
                
                <div class="text-end">
                    <a href="index.php?page=users" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Cancel
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Save User
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <?php
} elseif ($action === 'view') {
    // Get user statistics
    $stats_sql = "SELECT 
        COUNT(DISTINCT s.id) as total_sales,
        SUM(s.final_amount) as total_revenue,
        COUNT(DISTINCT DATE(s.created_at)) as active_days,
        MAX(s.created_at) as last_activity
    FROM sales s 
    WHERE s.user_id = ?";
    $stmt = $conn->prepare($stats_sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stats = $stmt->get_result()->fetch_assoc();
    
    // Get recent sales
    $sales_sql = "SELECT s.*, c.name as customer_name 
    FROM sales s 
    LEFT JOIN customers c ON s.customer_id = c.id 
    WHERE s.user_id = ? 
    ORDER BY s.created_at DESC 
    LIMIT 10";
    $stmt = $conn->prepare($sales_sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $recent_sales = $stmt->get_result();
    ?>
    
    <div class="row">
        <!-- User Information -->
        <div class="col-md-8 mb-4 animate-fade-in" style="animation-delay: 0.1s;">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-user"></i> User Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Username</label>
                                <p class="form-control-plaintext"><?php echo htmlspecialchars($user['username']); ?></p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Full Name</label>
                                <p class="form-control-plaintext"><?php echo htmlspecialchars($user['full_name']); ?></p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Role</label>
                                <p class="form-control-plaintext">
                                    <span class="badge bg-<?php echo $user['role'] === 'admin' ? 'danger' : 'info'; ?>">
                                        <?php echo ucfirst($user['role']); ?>
                                    </span>
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Member Since</label>
                                <p class="form-control-plaintext"><?php echo date('M d, Y', strtotime($user['created_at'])); ?></p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="text-end">
                        <a href="index.php?page=users&action=edit&id=<?php echo $user['id']; ?>" class="btn btn-primary">
                            <i class="fas fa-edit"></i> Edit User
                        </a>
                        <a href="index.php?page=users" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Users
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- User Statistics -->
        <div class="col-md-4 mb-4 animate-fade-in" style="animation-delay: 0.2s;">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-chart-line"></i> User Statistics</h5>
                </div>
                <div class="card-body">
                    <div class="stats-card mb-3">
                        <div class="stats-icon" style="background: var(--primary-gradient);">
                            <i class="fas fa-shopping-cart"></i>
                        </div>
                        <div class="stats-number"><?php echo $stats['total_sales'] ?? 0; ?></div>
                        <div class="stats-label">Total Sales</div>
                    </div>
                    
                    <div class="stats-card mb-3">
                        <div class="stats-icon" style="background: var(--success-gradient);">
                            <i class="fas fa-dollar-sign"></i>
                        </div>
                        <div class="stats-number"><?php echo formatCurrency($stats['total_revenue'] ?? 0); ?></div>
                        <div class="stats-label">Total Revenue</div>
                    </div>
                    
                    <div class="stats-card mb-3">
                        <div class="stats-icon" style="background: var(--info-gradient);">
                            <i class="fas fa-calendar-check"></i>
                        </div>
                        <div class="stats-number"><?php echo $stats['active_days'] ?? 0; ?></div>
                        <div class="stats-label">Active Days</div>
                    </div>
                    
                    <?php if ($stats['last_activity']): ?>
                    <div class="stats-card">
                        <div class="stats-icon" style="background: var(--warning-gradient);">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="stats-number"><?php echo date('M d', strtotime($stats['last_activity'])); ?></div>
                        <div class="stats-label">Last Activity</div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Recent Sales -->
    <div class="row animate-fade-in" style="animation-delay: 0.3s;">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-history"></i> Recent Sales</h5>
                </div>
                <div class="card-body">
                    <?php if ($recent_sales->num_rows > 0): ?>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Invoice</th>
                                        <th>Customer</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($sale = $recent_sales->fetch_assoc()): ?>
                                        <tr>
                                            <td><?php echo date('M d, H:i', strtotime($sale['created_at'])); ?></td>
                                            <td>
                                                <a href="index.php?page=sales&action=view&id=<?php echo $sale['id']; ?>" 
                                                   class="text-decoration-none">
                                                    <?php echo htmlspecialchars($sale['invoice_number']); ?>
                                                </a>
                                            </td>
                                            <td><?php echo htmlspecialchars($sale['customer_name'] ?? 'Walk-in'); ?></td>
                                            <td><?php echo formatCurrency($sale['final_amount']); ?></td>
                                            <td>
                                                <span class="badge bg-success">Completed</span>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <p class="text-muted mb-0">No sales found for this user.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <?php
} else {
    // List all users
    $users_sql = "SELECT u.id, u.username, u.full_name, u.role, u.created_at,
                          COUNT(s.id) as total_sales,
                          SUM(s.total_amount) as total_amount
                   FROM users u 
                   LEFT JOIN sales s ON u.id = s.user_id 
                   GROUP BY u.id, u.username, u.full_name, u.role, u.created_at
                   ORDER BY u.created_at DESC";
    $users = $conn->query($users_sql);
    ?>
    
    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h2><i class="fas fa-user-cog"></i> Users</h2>
            <a href="index.php?page=users&action=add" class="btn btn-primary">
                <i class="fas fa-plus"></i> Add User
            </a>
        </div>
    </div>
    
    <!-- SweetAlert2 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <?php if (isset($_GET['success'])): ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                title: '✅ Success!',
                text: 'User saved successfully!',
                icon: 'success',
                confirmButtonText: 'Great!',
                confirmButtonColor: '#28a745',
                background: 'linear-gradient(135deg, #28a745 0%, #20c997 100%)',
                backdrop: 'rgba(0, 0, 0, 0.4)',
                customClass: {
                    popup: 'animated fadeInUp',
                    title: 'text-white',
                    content: 'text-white'
                }
            });
        });
    </script>
    <?php endif; ?>
    
    <?php if (isset($_GET['error'])): ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let errorMessage = '';
            <?php 
            switch ($_GET['error']) {
                case 'delete_failed':
                    echo 'errorMessage = "Error deleting user.";';
                    break;
                case 'cannot_delete_self':
                    echo 'errorMessage = "You cannot delete your own account.";';
                    break;
                case 'has_sales':
                    echo 'errorMessage = "Cannot delete user. This user has sales records and cannot be removed.";';
                    break;
                case 'not_found':
                    echo 'errorMessage = "User not found.";';
                    break;
                default:
                    echo 'errorMessage = "An error occurred.";';
            }
            ?>
            
            Swal.fire({
                title: '❌ Error',
                text: errorMessage,
                icon: 'error',
                confirmButtonText: 'Got it!',
                confirmButtonColor: '#dc3545',
                background: 'linear-gradient(135deg, #dc3545 0%, #c82333 100%)',
                backdrop: 'rgba(0, 0, 0, 0.4)',
                customClass: {
                    popup: 'animated fadeInUp',
                    title: 'text-white',
                    content: 'text-white'
                }
            });
        });
    </script>
    <?php endif; ?>
    
    <!-- Info Alert for User Management -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(() => {
                Swal.fire({
                    title: 'ℹ️ User Management Info',
                    html: '<div class="text-left">' +
                          '<p><strong>Important Notes:</strong></p>' +
                          '<ul class="text-left">' +
                          '<li>Users with sales records cannot be deleted</li>' +
                          '<li>Users with a lock icon 🔒 have sales history</li>' +
                          '<li>Protected users are marked with a lock icon</li>' +
                          '</ul>' +
                          '</div>',
                    icon: 'info',
                    confirmButtonText: 'Got it!',
                    confirmButtonColor: '#17a2b8',
                    background: 'linear-gradient(135deg, #17a2b8 0%, #138496 100%)',
                    backdrop: 'rgba(0, 0, 0, 0.4)',
                    customClass: {
                        popup: 'animated fadeInUp',
                        title: 'text-white',
                        content: 'text-white'
                    }
                });
            }, 1000);
        });
    </script>
    
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-list"></i> User Management</h5>
        </div>
        <div class="card-body">
            <?php if ($users->num_rows > 0): ?>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>User</th>
                                <th>Role</th>
                                <th>Total Sales</th>
                                <th>Total Amount</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($user = $users->fetch_assoc()): ?>
                                <tr>
                                    <td>
                                        <strong><?php echo htmlspecialchars($user['full_name']); ?></strong>
                                        <br><small class="text-muted">@<?php echo htmlspecialchars($user['username']); ?></small>
                                    </td>
                                    <td>
                                        <span class="badge bg-<?php echo $user['role'] === 'admin' ? 'danger' : 'info'; ?>">
                                            <?php echo ucfirst($user['role']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary"><?php echo $user['total_sales']; ?> sales</span>
                                    </td>
                                    <td><?php echo formatCurrency($user['total_amount'] ?? 0); ?></td>
                                    <td><?php echo date('M d, Y', strtotime($user['created_at'])); ?></td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="index.php?page=users&action=view&id=<?php echo $user['id']; ?>" 
                                               class="btn btn-sm btn-outline-info" title="View Profile">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        <a href="index.php?page=users&action=edit&id=<?php echo $user['id']; ?>" 
                                               class="btn btn-sm btn-outline-primary" title="Edit User">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <?php if ($user['id'] != $_SESSION['user_id']): ?>
                                            <?php if ($user['total_sales'] > 0): ?>
                                                <button class="btn btn-sm btn-outline-secondary" 
                                                        title="Cannot delete - User has sales records" disabled>
                                                    <i class="fas fa-lock"></i>
                                                </button>
                                            <?php else: ?>
                                                <button class="btn btn-sm btn-outline-danger delete-user-btn"
                                                        data-user-id="<?php echo $user['id']; ?>"
                                                        data-user-name="<?php echo htmlspecialchars($user['full_name']); ?>"
                                                        title="Delete User">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="text-center py-4">
                    <i class="fas fa-user-cog fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No users found</h5>
                    <p class="text-muted">Start by adding your first user.</p>
                    <a href="index.php?page=users&action=add" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Add User
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <?php
}
?>

<script>
// Add SweetAlert for delete confirmation
document.querySelectorAll('.delete-user-btn').forEach(button => {
    button.addEventListener('click', function() {
        const userId = this.getAttribute('data-user-id');
        const userName = this.getAttribute('data-user-name');
        
        Swal.fire({
            title: '🗑️ Delete User?',
            html: `<p>Are you sure you want to delete <strong>${userName}</strong>?</p>
                   <p class="text-warning"><small>This action cannot be undone!</small></p>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, Delete!',
            cancelButtonText: 'Cancel',
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            background: 'linear-gradient(135deg, #ffc107 0%, #e0a800 100%)',
            backdrop: 'rgba(0, 0, 0, 0.4)',
            customClass: {
                popup: 'animated fadeInUp',
                title: 'text-white',
                content: 'text-white'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = `index.php?page=users&action=delete&id=${userId}`;
            }
        });
    });
});
</script> 