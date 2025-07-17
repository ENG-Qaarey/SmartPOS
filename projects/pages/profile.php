<?php
// Get current user data
$user_id = $_SESSION['user_id'];
$user = getUser($conn, $user_id);

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = sanitizeInput($_POST['full_name']);
    $email = sanitizeInput($_POST['email']);
    $phone = sanitizeInput($_POST['phone']);
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    $errors = [];
    $success = false;
    
    // Validate required fields
    if (empty($full_name)) {
        $errors[] = 'Full name is required.';
    }
    
    if (empty($email)) {
        $errors[] = 'Email is required.';
    } elseif (!isValidEmail($email)) {
        $errors[] = 'Please enter a valid email address.';
    }
    
    // Check if email is already taken by another user
    $email_check_sql = "SELECT id FROM users WHERE email = ? AND id != ?";
    $stmt = $conn->prepare($email_check_sql);
    $stmt->bind_param("si", $email, $user_id);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        $errors[] = 'Email address is already taken.';
    }
    
    // Handle password change
    if (!empty($current_password)) {
        if (!password_verify($current_password, $user['password'])) {
            $errors[] = 'Current password is incorrect.';
        } elseif (empty($new_password)) {
            $errors[] = 'New password is required.';
        } elseif (strlen($new_password) < 6) {
            $errors[] = 'New password must be at least 6 characters long.';
        } elseif ($new_password !== $confirm_password) {
            $errors[] = 'New passwords do not match.';
        }
    }
    
    // If no errors, update profile
    if (empty($errors)) {
        $profile_data = [
            'full_name' => $full_name,
            'email' => $email,
            'phone' => $phone
        ];
        
        if (updateUserProfile($conn, $user_id, $profile_data)) {
            if (!empty($new_password)) {
                updateUserPassword($conn, $user_id, $new_password);
            }
            $success = true;
            // Update session data
            $_SESSION['full_name'] = $full_name;
            // Refresh user data
            $user = getUser($conn, $user_id);
        } else {
            $errors[] = 'Failed to update profile. Please try again.';
        }
    }
}

// Get user statistics
$stats = getUserStats($conn, $user_id);
$recent_activity = getUserRecentActivity($conn, $user_id, 10);

// Calculate earnings for cashiers
$hours = ($stats['active_days'] ?? 0) * 8;
$earned = $hours * 2;
$hourly_rate = 2;

// Get monthly earnings
$current_month = date('Y-m');
$monthly_sales_sql = "SELECT COUNT(DISTINCT DATE(created_at)) as active_days FROM sales 
                      WHERE user_id = ? AND DATE_FORMAT(created_at, '%Y-%m') = ?";
$stmt = $conn->prepare($monthly_sales_sql);
$stmt->bind_param("is", $user_id, $current_month);
$stmt->execute();
$monthly_result = $stmt->get_result()->fetch_assoc();
$monthly_hours = ($monthly_result['active_days'] ?? 0) * 8;
$monthly_earned = $monthly_hours * $hourly_rate;
?>

<!-- Loading Screen -->
<div class="profile-loading">
    <div class="loading-spinner"></div>
</div>

<!-- Beautiful Profile Header -->
<div class="profile-header mb-4 animate-fade-in">
    <div class="profile-header-content">
        <div class="profile-avatar">
            <i class="fas fa-user-circle"></i>
        </div>
        <div class="profile-info">
            <h1 class="profile-name gradient-text"><?php echo htmlspecialchars($user['full_name']); ?></h1>
            <p class="profile-role">
                <i class="fas fa-badge animate-pulse"></i>
                <?php echo ucfirst($user['role']); ?>
            </p>
            <p class="profile-member-since">
                <i class="fas fa-calendar-alt"></i>
                Member since <?php echo date('M d, Y', strtotime($user['created_at'])); ?>
            </p>
        </div>
        <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'cashier'): ?>
        <div class="profile-earnings">
            <div class="earnings-card glass-effect">
                <div class="earnings-icon">
                    <i class="fas fa-coins"></i>
                </div>
                <div class="earnings-info">
                    <div class="earnings-amount"><?php echo formatCurrency($earned); ?></div>
                    <div class="earnings-label">Total Earned</div>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
    
    <!-- Floating particles -->
    <div class="floating-particles">
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
    </div>
</div>

<!-- SweetAlert2 CDN -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<?php if (isset($success) && $success): ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                title: '🎉 Success!',
                text: 'Your profile has been updated successfully!',
                icon: 'success',
                confirmButtonText: 'Awesome!',
                confirmButtonColor: '#667eea',
                background: 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)',
                backdrop: 'rgba(0, 0, 0, 0.4)',
                customClass: {
                    popup: 'animated fadeInUp',
                    title: 'text-white',
                    content: 'text-white'
                },
                showClass: {
                    popup: 'animate__animated animate__fadeInUp'
                },
                hideClass: {
                    popup: 'animate__animated animate__fadeOutDown'
                }
            });
        });
    </script>
<?php endif; ?>

<?php if (!empty($errors)): ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                title: '⚠️ Oops!',
                html: '<div class="text-left">' +
                      '<p class="mb-2"><strong>Please fix the following errors:</strong></p>' +
                      '<ul class="text-left">' +
                      '<?php foreach ($errors as $error): ?>' +
                      '<li><?php echo htmlspecialchars($error); ?></li>' +
                      '<?php endforeach; ?>' +
                      '</ul>' +
                      '</div>',
                icon: 'error',
                confirmButtonText: 'Got it!',
                confirmButtonColor: '#e74c3c',
                background: 'linear-gradient(135deg, #e74c3c 0%, #c0392b 100%)',
                backdrop: 'rgba(0, 0, 0, 0.4)',
                customClass: {
                    popup: 'animated fadeInUp',
                    title: 'text-white',
                    content: 'text-white'
                },
                showClass: {
                    popup: 'animate__animated animate__fadeInUp'
                },
                hideClass: {
                    popup: 'animate__animated animate__fadeOutDown'
                }
            });
        });
    </script>
<?php endif; ?>

<div class="row">
    <!-- Profile Information -->
    <div class="col-lg-8 mb-4 animate-fade-in" style="animation-delay: 0.1s;">
        <div class="card profile-card">
            <div class="card-header" style="background: #000; color: #fff;">
                <h5 class="mb-0" style="color: #fff;"><i class="fas fa-user-edit"></i> Profile Information</h5>
            </div>
            <div class="card-body" style="background: #fff; color: #000;">
                <form method="POST">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" style="color: #000; font-weight: 600;">Full Name *</label>
                                <input type="text" class="form-control" name="full_name" 
                                       value="<?php echo htmlspecialchars($user['full_name']); ?>" required 
                                       style="color: #000; background-color: #fff; border: 2px solid #000;">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" style="color: #000; font-weight: 600;">Username</label>
                                <input type="text" class="form-control" value="<?php echo htmlspecialchars($user['username']); ?>" 
                                       readonly style="background-color: #f8f9fa; color: #000; border: 2px solid #000;">
                                <small class="text-muted" style="color: #666;">Username cannot be changed</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" style="color: #000; font-weight: 600;">Email *</label>
                                <input type="email" class="form-control" name="email" 
                                       value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>" required 
                                       style="color: #000; background-color: #fff; border: 2px solid #000;">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" style="color: #000; font-weight: 600;">Phone</label>
                                <input type="tel" class="form-control" name="phone" 
                                       value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>"
                                       style="color: #000; background-color: #fff; border: 2px solid #000;">
                            </div>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <h6 class="mb-3" style="color: #000; font-weight: 600;"><i class="fas fa-lock"></i> Change Password</h6>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label" style="color: #000; font-weight: 600;">Current Password</label>
                                <input type="password" class="form-control" name="current_password" 
                                       style="color: #000; background-color: #fff; border: 2px solid #000;">
                                <small class="text-muted" style="color: #666;">Leave blank if not changing password</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label" style="color: #000; font-weight: 600;">New Password</label>
                                <input type="password" class="form-control" name="new_password" 
                                       style="color: #000; background-color: #fff; border: 2px solid #000;">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label" style="color: #000; font-weight: 600;">Confirm New Password</label>
                                <input type="password" class="form-control" name="confirm_password" 
                                       style="color: #000; background-color: #fff; border: 2px solid #000;">
                            </div>
                        </div>
                    </div>
                    
                    <div class="text-end">
                        <button type="submit" class="btn btn-primary" style="background: #000; border: 2px solid #000; color: #fff; font-weight: 600;">
                            <i class="fas fa-save"></i> Update Profile
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Profile Stats -->
    <div class="col-lg-4 mb-4 animate-fade-in" style="animation-delay: 0.2s;">
        <div class="card profile-card">
            <div class="card-header" style="background: #000; color: #fff;">
                <h5 class="mb-0" style="color: #fff;"><i class="fas fa-chart-line"></i> Your Statistics</h5>
            </div>
            <div class="card-body">
                <div class="stats-card mb-3 shimmer">
                    <div class="stats-icon" style="background: var(--primary-gradient);">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                    <div class="stats-number"><?php echo $stats['total_sales'] ?? 0; ?></div>
                    <div class="stats-label">Total Sales</div>
                </div>
                
                <div class="stats-card mb-3 shimmer">
                    <div class="stats-icon" style="background: var(--success-gradient);">
                        <i class="fas fa-dollar-sign"></i>
                    </div>
                    <div class="stats-number"><?php echo formatCurrency($stats['total_revenue'] ?? 0); ?></div>
                    <div class="stats-label">Total Revenue</div>
                </div>
                
                <div class="stats-card mb-3 shimmer">
                    <div class="stats-icon" style="background: var(--info-gradient);">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <div class="stats-number"><?php echo $stats['active_days'] ?? 0; ?></div>
                    <div class="stats-label">Active Days</div>
                </div>
                
                <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'cashier'): ?>
                <!-- Earnings Section for Cashiers -->
                <div class="earnings-section mb-3">
                    <h6 class="earnings-title">
                        <i class="fas fa-money-bill-wave"></i> Earnings Summary
                    </h6>
                    
                    <div class="earnings-card mb-3 shimmer">
                        <div class="earnings-icon" style="background: linear-gradient(135deg, #ffd700 0%, #ff6bd8 100%);">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="earnings-info">
                            <div class="earnings-amount"><?php echo $hours; ?> hrs</div>
                            <div class="earnings-label">Total Hours Worked</div>
                        </div>
                    </div>
                    
                    <div class="earnings-card mb-3 shimmer">
                        <div class="earnings-icon" style="background: linear-gradient(135deg, #4caf50 0%, #ffd700 100%);">
                            <i class="fas fa-dollar-sign"></i>
                        </div>
                        <div class="earnings-info">
                            <div class="earnings-amount"><?php echo formatCurrency($earned); ?></div>
                            <div class="earnings-label">Total Earned</div>
                        </div>
                    </div>
                    
                    <div class="earnings-card mb-3 shimmer">
                        <div class="earnings-icon" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                        <div class="earnings-info">
                            <div class="earnings-amount"><?php echo formatCurrency($monthly_earned); ?></div>
                            <div class="earnings-label">This Month</div>
                        </div>
                    </div>
                    
                    <div class="earnings-rate">
                        <small class="text-muted">
                            <i class="fas fa-info-circle"></i>
                            Rate: $<?php echo $hourly_rate; ?> per hour (8 hours per active day)
                        </small>
                    </div>
                </div>
                <?php endif; ?>
                
                <?php if ($stats['last_activity']): ?>
                <div class="stats-card shimmer">
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

<!-- Recent Activity -->
<div class="row animate-fade-in" style="animation-delay: 0.3s;">
    <div class="col-12">
        <div class="card profile-card">
            <div class="card-header" style="background: #000; color: #fff;">
                <h5 class="mb-0" style="color: #fff;"><i class="fas fa-history"></i> Recent Activity</h5>
            </div>
            <div class="card-body" style="background: #fff; color: #000;">
                <?php if ($recent_activity->num_rows > 0): ?>
                    <div class="table-responsive">
                        <table class="table" style="color: #000;">
                            <thead>
                                <tr>
                                    <th style="color: #000; font-weight: 600;">Date</th>
                                    <th style="color: #000; font-weight: 600;">Invoice</th>
                                    <th style="color: #000; font-weight: 600;">Customer</th>
                                    <th style="color: #000; font-weight: 600;">Amount</th>
                                    <th style="color: #000; font-weight: 600;">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($activity = $recent_activity->fetch_assoc()): ?>
                                    <tr style="color: #000;">
                                        <td style="color: #000;"><?php echo date('M d, H:i', strtotime($activity['created_at'])); ?></td>
                                        <td style="color: #000;">
                                            <a href="index.php?page=sales&action=view&id=<?php echo $activity['id']; ?>" 
                                               class="text-decoration-none" style="color: #000; font-weight: 600;">
                                                <?php echo htmlspecialchars($activity['invoice_number']); ?>
                                            </a>
                                        </td>
                                        <td style="color: #000;"><?php echo htmlspecialchars($activity['customer_name'] ?? 'Walk-in'); ?></td>
                                        <td style="color: #000; font-weight: 600;"><?php echo formatCurrency($activity['final_amount']); ?></td>
                                        <td>
                                            <span class="badge bg-success">Completed</span>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-center py-4">
                        <i class="fas fa-history fa-3x mb-3" style="color: #000;"></i>
                        <h5 style="color: #000; font-weight: 600;">No recent activity</h5>
                        <p style="color: #000;">Start making sales to see your activity here!</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<style>
/* ===== ULTRA BEAUTIFUL PROFILE STYLES ===== */

/* Advanced CSS Variables for stunning theming */
:root {
    --primary-gradient: linear-gradient(135deg, #082fdc 0%, #2665b9fc 100%);
    --secondary-gradient: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    --success-gradient: linear-gradient(135deg,rgb(79, 131, 254) 0%, #00f2fe 100%);
    --warning-gradient: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
    --danger-gradient: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
    --info-gradient: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%);
    --dark-gradient: linear-gradient(135deg, #2c3e50 0%, #3498db 100%);
    --neon-gradient: linear-gradient(135deg, #ff6b6b 0%, #4ecdc4 50%, #45b7d1 100%);
    --glass-bg: rgba(255, 255, 255, 0.15);
    --glass-border: rgba(255, 255, 255, 0.25);
    --shadow-light: 0 8px 32px rgba(31, 38, 135, 0.37);
    --shadow-medium: 0 15px 35px rgba(0, 0, 0, 0.1);
    --shadow-heavy: 0 25px 50px rgba(0, 0, 0, 0.15);
    --shadow-neon: 0 0 20px rgba(102, 126, 234, 0.5);
    --border-radius: 25px;
    --border-radius-small: 15px;
    --transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    --transition-fast: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
}

/* ===== STUNNING PROFILE HEADER ===== */
.profile-header {
    background: var(--neon-gradient);
    border-radius: var(--border-radius);
    padding: 4rem 3rem;
    color: white;
    position: relative;
    overflow: hidden;
    margin-bottom: 3rem;
    box-shadow: var(--shadow-heavy), var(--shadow-neon);
    backdrop-filter: blur(20px);
    border: 2px solid rgba(255, 255, 255, 0.2);
}

/* Advanced animated background with multiple layers */
.profile-header::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: 
        radial-gradient(circle at 20% 80%, rgba(255, 255, 255, 0.15) 0%, transparent 50%),
        radial-gradient(circle at 80% 20%, rgba(255, 255, 255, 0.15) 0%, transparent 50%),
        radial-gradient(circle at 40% 40%, rgba(255, 255, 255, 0.1) 0%, transparent 50%),
        radial-gradient(circle at 60% 60%, rgba(255, 255, 255, 0.1) 0%, transparent 50%);
    animation: float 8s ease-in-out infinite;
}

.profile-header::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: 
        url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="white" opacity="0.1"/><circle cx="75" cy="75" r="1" fill="white" opacity="0.1"/><circle cx="50" cy="10" r="0.5" fill="white" opacity="0.1"/><circle cx="10" cy="60" r="0.5" fill="white" opacity="0.1"/><circle cx="90" cy="40" r="0.5" fill="white" opacity="0.1"/><circle cx="30" cy="80" r="0.3" fill="white" opacity="0.1"/><circle cx="70" cy="30" r="0.3" fill="white" opacity="0.1"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
    opacity: 0.4;
    animation: shimmer 12s linear infinite;
}

.profile-header-content {
    position: relative;
    z-index: 3;
    display: flex;
    align-items: center;
    gap: 3rem;
}

.profile-avatar {
    font-size: 6rem;
    opacity: 0.98;
    filter: drop-shadow(0 8px 16px rgba(0, 0, 0, 0.4));
    animation: avatarFloat 5s ease-in-out infinite;
    background: rgba(255, 255, 255, 0.25);
    border-radius: 50%;
    padding: 1.5rem;
    backdrop-filter: blur(20px);
    border: 3px solid rgba(255, 255, 255, 0.4);
    position: relative;
    overflow: hidden;
}

.profile-avatar::before {
    content: '';
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: conic-gradient(from 0deg, transparent, rgba(255, 255, 255, 0.3), transparent);
    animation: rotate 8s linear infinite;
    opacity: 0.6;
}

.profile-info {
    flex: 1;
}

.profile-name {
    font-size: 3.5rem;
    font-weight: 900;
    color: #000;
    margin-bottom: 0.8rem;
    text-shadow: 0 6px 12px rgba(0, 0, 0, 0.4);
    background: linear-gradient(45deg, #fff,rgb(237, 228, 228), #fff);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    animation: nameGlow 4s ease-in-out infinite alternate;
    letter-spacing: 1px;
}

.profile-role {
    font-size: 1.5rem;
    margin-bottom: 0.8rem;
    opacity: 0.98;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 0.8rem;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
}

.profile-member-since {
    font-size: 1.1rem;
    opacity: 0.9;
    display: flex;
    align-items: center;
    gap: 0.8rem;
    font-weight: 500;
}

.profile-earnings {
    text-align: center;
    position: relative;
}

.earnings-card {
    background: var(--glass-bg);
    border-radius: var(--border-radius-small);
    padding: 2rem;
    backdrop-filter: blur(25px);
    border: 2px solid var(--glass-border);
    box-shadow: var(--shadow-light), 0 0 30px rgba(255, 255, 255, 0.2);
    transition: var(--transition);
    position: relative;
    overflow: hidden;
}

.earnings-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
    transition: left 0.6s;
}

.earnings-card:hover::before {
    left: 100%;
}

.earnings-card:hover {
    transform: translateY(-8px) scale(1.05);
    box-shadow: 0 25px 50px rgba(0, 0, 0, 0.3), 0 0 40px rgba(255, 255, 255, 0.3);
}

.earnings-amount {
    font-size: 2.5rem;
    font-weight: 900;
    margin-bottom: 0.5rem;
    text-shadow: 0 4px 8px rgba(0, 0, 0, 0.4);
    background: linear-gradient(45deg, #fff, #f0f0f0);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.earnings-label {
    font-size: 1rem;
    opacity: 0.95;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 1px;
}

/* ===== ULTRA BEAUTIFUL PROFILE CARDS ===== */
.profile-card {
    border: none;
    border-radius: var(--border-radius);
    box-shadow: var(--shadow-medium);
    transition: var(--transition);
    background: rgba(255, 255, 255, 0.98);
    backdrop-filter: blur(15px);
    border: 2px solid rgba(255, 255, 255, 0.3);
    overflow: hidden;
    position: relative;
}

.profile-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 5px;
    background: var(--neon-gradient);
    animation: shimmer 3s linear infinite;
}

.profile-card:hover {
    transform: translateY(-12px) scale(1.02);
    box-shadow: var(--shadow-heavy), 0 0 30px rgba(102, 126, 234, 0.3);
}

.profile-card .card-header {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-bottom: 2px solid #dee2e6;
    border-radius: var(--border-radius) var(--border-radius) 0 0 !important;
    padding: 2rem;
    position: relative;
    overflow: hidden;
}

.profile-card .card-header::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(45deg, transparent 30%, rgba(102, 126, 234, 0.15) 50%, transparent 70%);
    animation: shimmer 4s linear infinite;
}

.profile-card .card-body {
    padding: 2.5rem;
}

/* ===== STUNNING EARNINGS SECTION ===== */
.earnings-section {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-radius: var(--border-radius-small);
    padding: 2.5rem;
    margin-top: 2rem;
    position: relative;
    overflow: hidden;
    border: 2px solid rgba(102, 126, 234, 0.15);
    box-shadow: var(--shadow-light);
}

.earnings-section::before {
    content: '';
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: conic-gradient(from 0deg, transparent, rgba(102, 126, 234, 0.15), transparent);
    animation: rotate 12s linear infinite;
    opacity: 0.7;
}

.earnings-title {
    color: #2c3e50;
    font-weight: 800;
    margin-bottom: 2rem;
    text-align: center;
    font-size: 1.4rem;
    position: relative;
    z-index: 1;
    text-transform: uppercase;
    letter-spacing: 1px;
    background: var(--primary-gradient);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.earnings-card {
    display: flex;
    align-items: center;
    gap: 1.5rem;
    background: white;
    border-radius: var(--border-radius-small);
    padding: 2rem;
    box-shadow: var(--shadow-light);
    transition: var(--transition);
    position: relative;
    z-index: 1;
    border: 2px solid rgba(0, 0, 0, 0.05);
    overflow: hidden;
}

.earnings-card::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(45deg, transparent 30%, rgba(102, 126, 234, 0.05) 50%, transparent 70%);
    animation: shimmer 5s linear infinite;
    opacity: 0;
    transition: opacity 0.3s;
}

.earnings-card:hover::after {
    opacity: 1;
}

.earnings-card:hover {
    transform: translateY(-5px) scale(1.03);
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
}

.earnings-icon {
    width: 70px;
    height: 70px;
    border-radius: var(--border-radius-small);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.8rem;
    position: relative;
    overflow: hidden;
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.25);
    border: 2px solid rgba(255, 255, 255, 0.3);
}

.earnings-icon::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: inherit;
    filter: brightness(1.3);
    opacity: 0.8;
}

.earnings-info {
    flex: 1;
}

.earnings-amount {
    font-size: 1.8rem;
    font-weight: 900;
    color: #2c3e50;
    margin-bottom: 0.5rem;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.earnings-label {
    font-size: 1rem;
    color: #6c757d;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 1px;
}

.earnings-rate {
    text-align: center;
    margin-top: 2rem;
    padding-top: 2rem;
    border-top: 3px solid rgba(102, 126, 234, 0.3);
    position: relative;
    z-index: 1;
}

.earnings-rate small {
    background: var(--neon-gradient);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    font-weight: 700;
    font-size: 0.9rem;
}

/* ===== ULTRA BEAUTIFUL STATS CARDS ===== */
.stats-card {
    background: white;
    border-radius: var(--border-radius-small);
    padding: 2.5rem;
    box-shadow: var(--shadow-light);
    transition: var(--transition);
    border: 2px solid rgba(0, 0, 0, 0.05);
    position: relative;
    overflow: hidden;
    margin-bottom: 2rem;
}

.stats-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: var(--primary-gradient);
    animation: shimmer 4s linear infinite;
}

.stats-card:hover {
    transform: translateY(-8px) scale(1.03);
    box-shadow: var(--shadow-medium), 0 0 30px rgba(102, 126, 234, 0.2);
}

.stats-icon {
    width: 80px;
    height: 80px;
    border-radius: var(--border-radius-small);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 2rem;
    margin-bottom: 2rem;
    position: relative;
    overflow: hidden;
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
    border: 2px solid rgba(255, 255, 255, 0.3);
}

.stats-icon::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: inherit;
    filter: brightness(1.4);
    opacity: 0.7;
}

.stats-number {
    font-size: 2.5rem;
    font-weight: 900;
    color: #2c3e50;
    margin-bottom: 0.8rem;
    text-shadow: 0 3px 6px rgba(0, 0, 0, 0.1);
}

.stats-label {
    font-size: 1.1rem;
    color: #6c757d;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 1px;
}

/* ===== ULTRA BEAUTIFUL FORM STYLES ===== */
.form-control, .form-select {
    border-radius: var(--border-radius-small);
    border: 3px solid #e9ecef;
    padding: 1rem 1.2rem;
    transition: var(--transition);
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
    font-size: 1rem;
    font-weight: 500;
}

.form-control:focus, .form-select:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 0.3rem rgba(102, 126, 234, 0.25), 0 0 20px rgba(102, 126, 234, 0.2);
    transform: translateY(-3px);
    background: white;
}

.form-label {
    font-weight: 700;
    color: #2c3e50;
    margin-bottom: 0.8rem;
    font-size: 1rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

/* ===== ULTRA BEAUTIFUL BUTTON STYLES ===== */
.btn {
    border-radius: var(--border-radius-small);
    padding: 1rem 2rem;
    font-weight: 700;
    transition: var(--transition);
    position: relative;
    overflow: hidden;
    border: none;
    text-transform: uppercase;
    letter-spacing: 1px;
    font-size: 1rem;
    box-shadow: var(--shadow-light);
}

.btn::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
    transition: left 0.6s;
}

.btn:hover::before {
    left: 100%;
}

.btn-primary {
    background: var(--neon-gradient);
    box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
}

.btn-primary:hover {
    transform: translateY(-5px);
    box-shadow: 0 12px 30px rgba(102, 126, 234, 0.5);
}

.btn-secondary {
    background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
    box-shadow: 0 6px 20px rgba(108, 117, 125, 0.4);
}

.btn-secondary:hover {
    transform: translateY(-5px);
    box-shadow: 0 12px 30px rgba(108, 117, 125, 0.5);
}

/* ===== ULTRA BEAUTIFUL TABLE STYLES ===== */
.table {
    border-radius: var(--border-radius-small);
    overflow: hidden;
    box-shadow: var(--shadow-light);
    background: white;
    border: 2px solid rgba(0, 0, 0, 0.05);
}

.table thead th {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border: none;
    padding: 1.5rem;
    font-weight: 800;
    color: #2c3e50;
    text-transform: uppercase;
    letter-spacing: 1px;
    font-size: 0.9rem;
    border-bottom: 3px solid #dee2e6;
}

.table tbody td {
    padding: 1.5rem;
    border: none;
    border-bottom: 1px solid #f8f9fa;
    transition: var(--transition);
    font-weight: 500;
}

.table tbody tr:hover {
    background: linear-gradient(135deg, rgba(102, 126, 234, 0.08) 0%, rgba(118, 75, 162, 0.08) 100%);
    transform: scale(1.02);
}

.badge {
    border-radius: var(--border-radius-small);
    padding: 0.6rem 1.2rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    font-size: 0.8rem;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

/* ===== ULTRA BEAUTIFUL ALERT STYLES ===== */
.alert {
    border-radius: var(--border-radius-small);
    border: none;
    padding: 2rem;
    font-weight: 700;
    box-shadow: var(--shadow-light);
    backdrop-filter: blur(15px);
    border-left: 5px solid;
}



/* ===== ULTRA BEAUTIFUL ANIMATIONS ===== */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(50px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes float {
    0%, 100% { transform: translateY(0px) rotate(0deg); }
    50% { transform: translateY(-15px) rotate(2deg); }
}

@keyframes avatarFloat {
    0%, 100% { transform: translateY(0px) scale(1) rotate(0deg); }
    50% { transform: translateY(-12px) scale(1.08) rotate(5deg); }
}

@keyframes nameGlow {
    0% { 
        filter: drop-shadow(0 0 10px rgba(255, 255, 255, 0.4));
        text-shadow: 0 6px 12px rgba(0, 0, 0, 0.4);
    }
    100% { 
        filter: drop-shadow(0 0 25px rgba(255, 255, 255, 0.8));
        text-shadow: 0 8px 16px rgba(0, 0, 0, 0.6);
    }
}

@keyframes shimmer {
    0% { transform: translateX(-100%); }
    100% { transform: translateX(100%); }
}

@keyframes rotate {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

@keyframes pulse {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.1); }
}

@keyframes particleFloat {
    0% {
        transform: translateY(0px) translateX(0px);
        opacity: 0;
    }
    10% {
        opacity: 1;
    }
    90% {
        opacity: 1;
    }
    100% {
        transform: translateY(-150px) translateX(100px);
        opacity: 0;
    }
}

/* ===== FLOATING PARTICLES ===== */
.floating-particles {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    pointer-events: none;
    z-index: 1;
}

.particle {
    position: absolute;
    width: 6px;
    height: 6px;
    background: rgba(255, 255, 255, 0.8);
    border-radius: 50%;
    animation: particleFloat 10s linear infinite;
    box-shadow: 0 0 10px rgba(255, 255, 255, 0.5);
}

.particle:nth-child(1) {
    top: 20%;
    left: 10%;
    animation-delay: 0s;
    animation-duration: 8s;
}

.particle:nth-child(2) {
    top: 60%;
    left: 80%;
    animation-delay: 2s;
    animation-duration: 12s;
}

.particle:nth-child(3) {
    top: 40%;
    left: 60%;
    animation-delay: 4s;
    animation-duration: 10s;
}

.particle:nth-child(4) {
    top: 80%;
    left: 20%;
    animation-delay: 1s;
    animation-duration: 14s;
}

.particle:nth-child(5) {
    top: 30%;
    left: 90%;
    animation-delay: 3s;
    animation-duration: 9s;
}

/* ===== RESPONSIVE DESIGN ===== */
@media (max-width: 768px) {
    .profile-header-content {
        flex-direction: column;
        text-align: center;
        gap: 2rem;
    }
    
    .profile-name {
        font-size: 2.8rem;
    }
    
    .profile-earnings {
        margin-top: 1.5rem;
    }
    
    .earnings-card {
        flex-direction: column;
        text-align: center;
        gap: 1.5rem;
    }
    
    .profile-card .card-body {
        padding: 2rem;
    }
    
    .stats-card {
        padding: 2rem;
    }
    
    .earnings-section {
        padding: 2rem;
    }
}

@media (max-width: 576px) {
    .profile-header {
        padding: 3rem 1.5rem;
    }
    
    .profile-name {
        font-size: 2.2rem;
    }
    
    .profile-avatar {
        font-size: 4.5rem;
    }
    
    .earnings-amount {
        font-size: 1.6rem;
    }
    
    .stats-number {
        font-size: 2rem;
    }
}

/* ===== SCROLLBAR STYLING ===== */
::-webkit-scrollbar {
    width: 12px;
}

::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
}

::-webkit-scrollbar-thumb {
    background: var(--neon-gradient);
    border-radius: 10px;
    border: 2px solid #f1f1f1;
}

::-webkit-scrollbar-thumb:hover {
    background: linear-gradient(135deg, #5a6fd8 0%, #6a4190 100%);
}

/* ===== GLOBAL ENHANCEMENTS ===== */
* {
    transition: var(--transition);
}

body {
    background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
    min-height: 100vh;
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}

/* ===== FOCUS EFFECTS ===== */
.form-control:focus,
.form-select:focus {
    box-shadow: 0 0 0 5px rgba(102, 126, 234, 0.15);
    border-color: #667eea;
    transform: translateY(-3px);
}

/* ===== HOVER EFFECTS ===== */
.btn:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 35px rgba(0, 0, 0, 0.3);
}

.card:hover {
    transform: translateY(-12px);
    box-shadow: 0 35px 70px rgba(0, 0, 0, 0.2);
}

/* ===== LOADING ANIMATIONS ===== */
.animate-fade-in {
    animation: fadeInUp 1s ease-out;
}

.animate-float {
    animation: float 5s ease-in-out infinite;
}

.animate-pulse {
    animation: pulse 2s ease-in-out infinite;
}

/* ===== GLASS MORPHISM EFFECTS ===== */
.glass-effect {
    background: rgba(255, 255, 255, 0.15);
    backdrop-filter: blur(20px);
    border: 2px solid rgba(255, 255, 255, 0.3);
    box-shadow: 0 8px 32px rgba(31, 38, 135, 0.37);
}

/* ===== GRADIENT TEXT EFFECTS ===== */
.gradient-text {
    background: var(--neon-gradient);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

/* ===== SHIMMER EFFECTS ===== */
.shimmer {
    position: relative;
    overflow: hidden;
}

.shimmer::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.5), transparent);
    animation: shimmer 4s infinite;
}

/* ===== LOADING ANIMATION ===== */
.profile-loading {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: var(--neon-gradient);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 9999;
    animation: fadeOut 1.5s ease-in-out 3s forwards;
}

.loading-spinner {
    width: 80px;
    height: 80px;
    border: 5px solid rgba(255, 255, 255, 0.3);
    border-radius: 50%;
    border-top-color: white;
    animation: spin 1s linear infinite;
    box-shadow: 0 0 30px rgba(255, 255, 255, 0.5);
}

@keyframes fadeOut {
    to {
        opacity: 0;
        visibility: hidden;
    }
}

@keyframes spin {
    to {
        transform: rotate(360deg);
    }
}
</style>

<script>
// Hide loading screen after page loads
window.addEventListener('load', function() {
    const loadingScreen = document.querySelector('.profile-loading');
    if (loadingScreen) {
        loadingScreen.style.opacity = '0';
        setTimeout(() => {
            loadingScreen.style.display = 'none';
        }, 500);
    }
});

// Add smooth scrolling
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        document.querySelector(this.getAttribute('href')).scrollIntoView({
            behavior: 'smooth'
        });
    });
});

// Add form validation feedback
document.querySelectorAll('.form-control').forEach(input => {
    input.addEventListener('blur', function() {
        if (this.value.trim() === '') {
            this.classList.add('is-invalid');
        } else {
            this.classList.remove('is-invalid');
        }
    });
});

// Add card hover effects
document.querySelectorAll('.profile-card').forEach(card => {
    card.addEventListener('mouseenter', function() {
        this.style.transform = 'translateY(-5px)';
    });
    
    card.addEventListener('mouseleave', function() {
        this.style.transform = 'translateY(0)';
    });
});

// Add particle animation
function createParticle() {
    const particle = document.createElement('div');
    particle.className = 'particle';
    particle.style.left = Math.random() * 100 + '%';
    particle.style.top = Math.random() * 100 + '%';
    particle.style.animationDelay = Math.random() * 6 + 's';
    
    document.querySelector('.floating-particles').appendChild(particle);
    
    setTimeout(() => {
        particle.remove();
    }, 6000);
}

// Create particles periodically
setInterval(createParticle, 3000);

// Add loading animation for form submission
document.querySelector('form').addEventListener('submit', function(e) {
    e.preventDefault();
    
    // Show loading state
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    
    // Validate form before submission
    const formData = new FormData(this);
    let hasErrors = false;
    let errorMessages = [];
    
    // Check required fields
    if (!formData.get('full_name').trim()) {
        hasErrors = true;
        errorMessages.push('Full name is required');
    }
    
    if (!formData.get('email').trim()) {
        hasErrors = true;
        errorMessages.push('Email is required');
    } else if (!isValidEmail(formData.get('email'))) {
        hasErrors = true;
        errorMessages.push('Please enter a valid email address');
    }
    
    // Check password fields if current password is provided
    if (formData.get('current_password').trim()) {
        if (!formData.get('new_password').trim()) {
            hasErrors = true;
            errorMessages.push('New password is required when changing password');
        } else if (formData.get('new_password').length < 6) {
            hasErrors = true;
            errorMessages.push('New password must be at least 6 characters long');
        } else if (formData.get('new_password') !== formData.get('confirm_password')) {
            hasErrors = true;
            errorMessages.push('New passwords do not match');
        }
    }
    
    if (hasErrors) {
        Swal.fire({
            title: '⚠️ Validation Error',
            html: '<div class="text-left">' +
                  '<p class="mb-2"><strong>Please fix the following errors:</strong></p>' +
                  '<ul class="text-left">' +
                  errorMessages.map(error => '<li>' + error + '</li>').join('') +
                  '</ul>' +
                  '</div>',
            icon: 'warning',
            confirmButtonText: 'Got it!',
            confirmButtonColor: '#f39c12',
            background: 'linear-gradient(135deg, #f39c12 0%, #e67e22 100%)',
            backdrop: 'rgba(0, 0, 0, 0.4)',
            customClass: {
                popup: 'animated fadeInUp',
                title: 'text-white',
                content: 'text-white'
            }
        });
        return;
    }
    
    // Show confirmation dialog
    Swal.fire({
        title: '🔄 Update Profile?',
        text: 'Are you sure you want to update your profile information?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Yes, Update!',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#667eea',
        cancelButtonColor: '#6c757d',
        background: 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)',
        backdrop: 'rgba(0, 0, 0, 0.4)',
        customClass: {
            popup: 'animated fadeInUp',
            title: 'text-white',
            content: 'text-white'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            // Show loading state
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Updating...';
            submitBtn.disabled = true;
            
            // Show loading alert
            Swal.fire({
                title: '⏳ Updating Profile...',
                text: 'Please wait while we update your profile information.',
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                },
                background: 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)',
                backdrop: 'rgba(0, 0, 0, 0.4)',
                customClass: {
                    popup: 'animated fadeInUp',
                    title: 'text-white',
                    content: 'text-white'
                }
            });
            
            // Submit the form
            this.submit();
        }
    });
    
    // Re-enable after a delay (in case of errors)
    setTimeout(() => {
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    }, 5000);
});

// Email validation function
function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

// Add SweetAlert for password change confirmation
document.querySelector('input[name="current_password"]').addEventListener('input', function() {
    if (this.value.trim()) {
        Swal.fire({
            title: '🔐 Password Change',
            text: 'You\'re about to change your password. Make sure to remember your new password!',
            icon: 'info',
            confirmButtonText: 'Got it!',
            confirmButtonColor: '#667eea',
            background: 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)',
            backdrop: 'rgba(0, 0, 0, 0.4)',
            customClass: {
                popup: 'animated fadeInUp',
                title: 'text-white',
                content: 'text-white'
            }
        });
    }
});

// Add SweetAlert for earnings information (for cashiers)
<?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'cashier'): ?>
document.addEventListener('DOMContentLoaded', function() {
    // Show earnings info on page load
    setTimeout(() => {
        Swal.fire({
            title: '💰 Earnings Summary',
            html: '<div class="text-center">' +
                  '<p class="mb-3"><strong>Your current earnings:</strong></p>' +
                  '<div class="earnings-summary">' +
                  '<p><i class="fas fa-clock"></i> Hours Worked: <strong><?php echo $hours; ?> hrs</strong></p>' +
                  '<p><i class="fas fa-dollar-sign"></i> Total Earned: <strong><?php echo formatCurrency($earned); ?></strong></p>' +
                  '<p><i class="fas fa-calendar-alt"></i> This Month: <strong><?php echo formatCurrency($monthly_earned); ?></strong></p>' +
                  '<p class="text-muted mt-2"><small>Rate: $<?php echo $hourly_rate; ?> per hour</small></p>' +
                  '</div>' +
                  '</div>',
            icon: 'info',
            confirmButtonText: 'Awesome!',
            confirmButtonColor: '#28a745',
            background: 'linear-gradient(135deg, #28a745 0%, #20c997 100%)',
            backdrop: 'rgba(0, 0, 0, 0.4)',
            customClass: {
                popup: 'animated fadeInUp',
                title: 'text-white',
                content: 'text-white'
            }
        });
    }, 2000);
});
<?php endif; ?>
</script> 