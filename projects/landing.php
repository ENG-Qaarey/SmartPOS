<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to SmartPOS</title>
    <link rel="icon" type="image/png" href="pos_logo.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        body {
            background: linear-gradient(135deg, #f8fafc 0%, #e0e7ef 100%);
            min-height: 100vh;
        }
        .hero {
            padding: 80px 0 60px 0;
            background: linear-gradient(120deg, #4f8cff 0%, #6ed6ff 100%);
            color: #fff;
            text-align: center;
        }
        .hero-logo {
            width: 100px;
            margin-bottom: 20px;
        }
        .features {
            margin-top: 60px;
        }
        .feature-icon {
            font-size: 2.5rem;
            color: #4f8cff;
            margin-bottom: 15px;
        }
        .footer {
            background: #222;
            color: #fff;
            padding: 30px 0 10px 0;
            text-align: center;
            margin-top: 60px;
        }
        .cta-btn {
            font-size: 1.2rem;
            padding: 12px 36px;
            border-radius: 30px;
            margin-top: 30px;
        }
    </style>
</head>
<body>
    <!-- Hero Section -->
    <section class="hero">
        <img src="pos_logo.png" alt="SmartPOS Logo" class="hero-logo rounded-circle shadow">
        <h1 class="display-4 fw-bold">Welcome to SmartPOS</h1>
        <p class="lead mt-3 mb-4">Modern, easy-to-use Point of Sale system for your business.<br>Manage sales, products, customers, and more—all in one place.</p>
        <a href="login.php" class="btn btn-light cta-btn shadow">Get Started <i class="fas fa-arrow-right ms-2"></i></a>
    </section>

    <!-- Features Section -->
    <section class="container features">
        <div class="row text-center">
            <div class="col-md-4 mb-5">
                <div class="feature-icon mb-3"><i class="fas fa-cash-register"></i></div>
                <h5 class="fw-bold">Easy Sales</h5>
                <p>Process sales quickly and efficiently with an intuitive interface.</p>
            </div>
            <div class="col-md-4 mb-5">
                <div class="feature-icon mb-3"><i class="fas fa-boxes"></i></div>
                <h5 class="fw-bold">Product Management</h5>
                <p>Track inventory, add new products, and manage stock levels with ease.</p>
            </div>
            <div class="col-md-4 mb-5">
                <div class="feature-icon mb-3"><i class="fas fa-users"></i></div>
                <h5 class="fw-bold">Customer Insights</h5>
                <p>Understand your customers and grow your business with powerful analytics.</p>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <p class="mb-1">&copy; <?php echo date('Y'); ?> SmartPOS. All rights reserved.</p>
            <small>Contact: <a href="mailto:info@smartpos.com" class="text-light">info@smartpos.com</a></small>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 