<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SmartPOS - Point of Sale System</title>
    <link rel="icon" type="image/png" href="pos_logo.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/sidebar.css">
    <script src="assets/js/animations.js" defer></script>
    <script>
    // Live time display
    function updateTime() {
        const now = new Date();
        const timeString = now.toLocaleTimeString('en-US', {
            hour12: true,
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit'
        });

        const timeElement = document.getElementById('current-time');
        if (timeElement) {
            timeElement.textContent = timeString;
        }
    }

    // Wait for DOM to be ready
    document.addEventListener('DOMContentLoaded', function() {
        // Update time immediately
        updateTime();

        // Update time every second
        setInterval(updateTime, 1000);

        // Also update when page becomes visible (in case tab was inactive)
        document.addEventListener('visibilitychange', function() {
            if (!document.hidden) {
                updateTime();
            }
        });
    });
    </script>
</head>

<body>
    <!-- Fixed Sidebar -->
    <div class="sidebar">
        <div class="p-4 text-center position-relative">
            <!-- Mobile Close Button -->
            <button class="sidebar-close d-md-none" id="sidebarClose">
                <i class="fas fa-times"></i>
            </button>

            <!-- Animated Background Icons -->
            <div class="sidebar-bg-icons">
                <i class="fas fa-coins animate-float-delayed"></i>
                <i class="fas fa-chart-line animate-pulse-delayed"></i>
                <i class="fas fa-shopping-bag animate-bounce-delayed"></i>
            </div>

            <h4>
                <span class="brand-text">SmartPOS</span>
            </h4>
            <small>
                Point of Sale System
            </small>
        </div>

        <nav class="nav flex-column">
            <a class="nav-link <?php echo $page === 'dashboard' ? 'active' : ''; ?>" href="index.php">
                <i class="fas fa-tachometer-alt"></i> Dashboard
            </a>

            <a class="nav-link <?php echo $page === 'sales' ? 'active' : ''; ?>" href="index.php?page=sales">
                <i class="fas fa-shopping-cart"></i> Sales
            </a>

            <a class="nav-link <?php echo $page === 'products' ? 'active' : ''; ?>" href="index.php?page=products">
                <i class="fas fa-box"></i> Products
            </a>

            <a class="nav-link <?php echo $page === 'customers' ? 'active' : ''; ?>" href="index.php?page=customers">
                <i class="fas fa-users"></i> Customers
            </a>

            <?php if (isAdmin()): ?>
            <a class="nav-link <?php echo $page === 'reports' ? 'active' : ''; ?>" href="index.php?page=reports">
                <i class="fas fa-chart-bar"></i> Reports
            </a>

            <a class="nav-link <?php echo $page === 'users' ? 'active' : ''; ?>" href="index.php?page=users">
                <i class="fas fa-user-cog"></i> Users
            </a>
            <a class="nav-link <?php echo $page === 'about' ? 'active' : ''; ?>" href="index.php?page=about">
                <i class="fas fa-info-circle"></i> About
            </a>
            <?php endif; ?>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Top Navbar -->
        <nav class="navbar navbar-expand-lg">
            <div class="container-fluid">
                <!-- Time Display -->
                <div class="navbar-nav me-auto">
                    <div class="nav-item">
                        <div class="nav-link d-flex align-items-center">
                            <i class="fas fa-clock me-2"></i>
                            <span id="current-time" class="fw-bold">
                                <?php echo date('h:i:s A'); ?>
                            </span>
                        </div>
                    </div>
                </div>

                <div class="navbar-nav ms-auto">
                    <div class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
                            data-bs-toggle="dropdown">
                            <i class="fas fa-user-circle"></i>
                            <?php echo htmlspecialchars($_SESSION['full_name']); ?>
                            <span class="badge bg-secondary ms-1"><?php echo ucfirst($_SESSION['user_role']); ?></span>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item profile-link" href="index.php?page=profile">
                                    <i class="fas fa-user"></i> My Profile
                                </a></li>
                            <?php if (isAdmin()): ?>
                            <li><a class="dropdown-item admin-link" href="index.php?page=users">
                                    <i class="fas fa-users"></i> Manage Users
                                </a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <?php endif; ?>
                            <li><a class="dropdown-item logout-link" href="logout.php">
                                    <i class="fas fa-sign-out-alt"></i> Logout
                                </a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Page Content -->
        <div class="p-4">