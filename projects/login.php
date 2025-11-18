<?php
session_start();

// Initialize error variable
$error = '';

// If already logged in, redirect to dashboard
if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Include database connection
    require_once 'config/database.php';
    
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($username) || empty($password)) {
        $error = 'Please enter both username and password.';
    } else {
        try {
            // Prepare and execute query
            $sql = "SELECT id, username, password, full_name, role FROM users WHERE username = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows === 1) {
                $user = $result->fetch_assoc();
                
                // Verify password
                if (password_verify($password, $user['password'])) {
                    // Set session variables
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['full_name'] = $user['full_name'];
                    $_SESSION['user_role'] = $user['role'];
                    
                    // Redirect to dashboard
                    header('Location: index.php');
                    exit();
                } else {
                    $error = 'Invalid username or password.';
                }
            } else {
                $error = 'Invalid username or password.';
            }
        } catch (Exception $e) {
            $error = 'An error occurred. Please try again later.';
            // Log the error for debugging
            error_log('Login Error: ' . $e->getMessage());
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SmartPOS - Login</title>
    <link rel="icon" type="image/png" href="pos_logo.png">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --primary-blue: #2563eb;
            --secondary-blue: #3b82f6;
            --light-blue: #60a5fa;
            --electric-blue: #00d4ff;
            --primary-green: #10b981;
            --secondary-green: #34d399;
            --emerald: #059669;
            --dark-bg: #0f172a;
            --darker-bg: #020617;
            --card-bg: rgba(255, 255, 255, 0.08);
        }

        body {
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            background: linear-gradient(135deg, var(--darker-bg) 0%, var(--dark-bg) 50%, #1e293b 100%);
            color: #fff;
            min-height: 100vh;
            overflow-x: hidden;
            line-height: 1.6;
        }

        /* Animated Background */
        .animated-bg {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -2;
            overflow: hidden;
        }

        .gradient-orbs {
            position: absolute;
            width: 100%;
            height: 100%;
        }

        .orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(60px);
            opacity: 0.4;
            animation: orb-float 25s infinite linear;
        }

        .orb-1 {
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, var(--primary-blue) 0%, transparent 70%);
            top: -200px;
            left: -100px;
            animation-delay: 0s;
        }

        .orb-2 {
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, var(--primary-green) 0%, transparent 70%);
            bottom: -150px;
            right: -100px;
            animation-delay: -8s;
        }

        .orb-3 {
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, var(--electric-blue) 0%, transparent 70%);
            top: 50%;
            left: 70%;
            animation-delay: -15s;
        }

        @keyframes orb-float {
            0%, 100% {
                transform: translate(0, 0) scale(1);
            }
            25% {
                transform: translate(50px, 50px) scale(1.1);
            }
            50% {
                transform: translate(0, 100px) scale(1.2);
            }
            75% {
                transform: translate(-50px, 50px) scale(1.1);
            }
        }

        /* Grid Background */
        .grid-bg {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: 
                linear-gradient(rgba(37, 99, 235, 0.1) 1px, transparent 1px),
                linear-gradient(90deg, rgba(37, 99, 235, 0.1) 1px, transparent 1px);
            background-size: 50px 50px;
            animation: grid-move 20s linear infinite;
            opacity: 0.3;
        }

        @keyframes grid-move {
            0% {
                transform: translate(0, 0);
            }
            100% {
                transform: translate(50px, 50px);
            }
        }

        /* Floating Elements */
        .floating-elements {
            position: fixed;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: -1;
        }

        .floating-element {
            position: absolute;
            background: linear-gradient(135deg, var(--primary-blue), var(--electric-blue));
            border-radius: 20px;
            opacity: 0.1;
            animation: float-element 30s infinite linear;
        }

        .floating-element:nth-child(1) {
            width: 80px;
            height: 80px;
            top: 20%;
            left: 10%;
            animation-delay: 0s;
        }

        .floating-element:nth-child(2) {
            width: 60px;
            height: 60px;
            top: 60%;
            left: 85%;
            background: linear-gradient(135deg, var(--primary-green), var(--secondary-green));
            animation-delay: -10s;
        }

        .floating-element:nth-child(3) {
            width: 100px;
            height: 100px;
            top: 80%;
            left: 15%;
            animation-delay: -20s;
        }

        @keyframes float-element {
            0%, 100% {
                transform: translateY(0) rotate(0deg);
            }
            33% {
                transform: translateY(-30px) rotate(120deg);
            }
            66% {
                transform: translateY(15px) rotate(240deg);
            }
        }

        /* Main Container */
        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 20px;
            position: relative;
            z-index: 1;
        }

        /* Login Section */
        .login-section {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 40px 20px;
        }

        .login-content {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 60px;
            align-items: center;
            max-width: 1200px;
            width: 100%;
        }

        /* Welcome Text */
        .welcome-text {
            animation: slideInLeft 1s ease-out;
        }

        @keyframes slideInLeft {
            0% {
                opacity: 0;
                transform: translateX(-50px);
            }
            100% {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .welcome-badge {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            padding: 12px 24px;
            border-radius: 50px;
            margin-bottom: 30px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% {
                box-shadow: 0 0 20px rgba(37, 99, 235, 0.3);
            }
            50% {
                box-shadow: 0 0 30px rgba(37, 99, 235, 0.6);
            }
        }

        .welcome-title {
            font-size: 3.5rem;
            font-weight: 800;
            margin-bottom: 20px;
            line-height: 1.1;
        }

        .welcome-gradient {
            background: linear-gradient(135deg, var(--primary-blue) 0%, var(--electric-blue) 25%, var(--primary-green) 50%, var(--secondary-green) 75%, var(--light-blue) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            background-size: 200% 200%;
            animation: gradient-shift 8s ease infinite;
        }

        @keyframes gradient-shift {
            0%, 100% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
        }

        .welcome-subtitle {
            font-size: 1.3rem;
            color: #94a3b8;
            margin-bottom: 40px;
            line-height: 1.6;
        }

        .features-list {
            list-style: none;
            margin-top: 40px;
        }

        .features-list li {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 20px;
            color: #cbd5e1;
            font-size: 1.1rem;
        }

        .features-list li i {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            background: linear-gradient(135deg, var(--primary-blue), var(--electric-blue));
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
        }

        .features-list li:nth-child(even) i {
            background: linear-gradient(135deg, var(--primary-green), var(--secondary-green));
        }

        /* Login Form */
        .login-form-container {
            animation: slideInRight 1s ease-out;
        }

        @keyframes slideInRight {
            0% {
                opacity: 0;
                transform: translateX(50px);
            }
            100% {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .login-card {
            background: var(--card-bg);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 25px;
            padding: 50px 40px;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.3);
            position: relative;
            overflow: hidden;
        }

        .login-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.05), transparent);
            transition: left 0.6s ease;
        }

        .login-card:hover::before {
            left: 100%;
        }

        .login-header {
            text-align: center;
            margin-bottom: 40px;
        }

        .login-logo {
            width: 80px;
            height: 80px;
            border-radius: 20px;
            margin: 0 auto 20px;
            border: 2px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            animation: logoBounce 3s ease-in-out infinite;
        }

        @keyframes logoBounce {
            0%, 100% { transform: translateY(0) scale(1); }
            50% { transform: translateY(-10px) scale(1.05); }
        }

        .login-title {
            font-size: 2.2rem;
            font-weight: 700;
            margin-bottom: 10px;
            background: linear-gradient(135deg, var(--primary-blue), var(--electric-blue));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .login-subtitle {
            color: #94a3b8;
            font-size: 1.1rem;
        }

        /* Form Styles */
        .form-group {
            margin-bottom: 25px;
            position: relative;
        }

        .form-label {
            display: block;
            margin-bottom: 8px;
            color: #cbd5e1;
            font-weight: 500;
            font-size: 0.95rem;
        }

        .input-with-icon {
            position: relative;
        }

        .input-with-icon i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            font-size: 1.2rem;
            transition: all 0.3s ease;
        }

        .form-input {
            width: 100%;
            padding: 15px 15px 15px 50px;
            background: rgba(255, 255, 255, 0.05);
            border: 2px solid rgba(255, 255, 255, 0.1);
            border-radius: 15px;
            color: white;
            font-size: 1rem;
            font-weight: 400;
            outline: none;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
        }

        .form-input::placeholder {
            color: #64748b;
        }

        .form-input:focus {
            border-color: var(--primary-blue);
            box-shadow: 0 0 20px rgba(37, 99, 235, 0.3);
            transform: translateY(-2px);
        }

        .form-input:focus + i {
            color: var(--electric-blue);
            transform: translateY(-50%) scale(1.1);
        }

        .password-toggle {
            position: absolute;
            right: 55px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #94a3b8;
            cursor: pointer;
            font-size: 1.2rem;
            transition: color 0.3s ease;
        }

        .password-toggle:hover {
            color: var(--electric-blue);
        }

        .login-btn {
            width: 100%;
            padding: 16px;
            background: linear-gradient(135deg, var(--primary-blue), var(--electric-blue));
            border: none;
            border-radius: 15px;
            color: white;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(37, 99, 235, 0.3);
            margin-top: 10px;
        }

        .login-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }

        .login-btn:hover::before {
            left: 100%;
        }

        .login-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 40px rgba(37, 99, 235, 0.4);
        }

        .login-btn:active {
            transform: translateY(-1px);
        }

        .login-btn.loading {
            pointer-events: none;
            opacity: 0.8;
        }

        .login-btn.loading::after {
            content: '';
            position: absolute;
            width: 20px;
            height: 20px;
            top: 50%;
            left: 50%;
            margin: -10px 0 0 -10px;
            border: 2px solid transparent;
            border-top: 2px solid white;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .login-footer {
            text-align: center;
            margin-top: 30px;
            color: #64748b;
            font-size: 0.9rem;
        }

        .demo-credentials {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 10px;
            padding: 15px;
            margin-top: 20px;
            border-left: 4px solid var(--primary-green);
        }

        .demo-credentials h4 {
            color: var(--secondary-green);
            margin-bottom: 8px;
            font-size: 0.95rem;
        }

        .demo-credentials p {
            font-size: 0.85rem;
            color: #94a3b8;
            margin-bottom: 5px;
        }

        /* Error Message */
        .error-message {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.3);
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 20px;
            color: #fca5a5;
            font-size: 0.95rem;
            text-align: center;
            animation: shake 0.5s ease-in-out;
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }

        /* Responsive */
        @media (max-width: 968px) {
            .login-content {
                grid-template-columns: 1fr;
                gap: 40px;
            }
            
            .welcome-title {
                font-size: 2.8rem;
            }
        }

        @media (max-width: 768px) {
            .welcome-title {
                font-size: 2.2rem;
            }
            
            .login-card {
                padding: 40px 30px;
            }
        }
    </style>
</head>
<body>
    <!-- Animated Background -->
    <div class="animated-bg">
        <div class="gradient-orbs">
            <div class="orb orb-1"></div>
            <div class="orb orb-2"></div>
            <div class="orb orb-3"></div>
        </div>
        <div class="grid-bg"></div>
        <div class="floating-elements">
            <div class="floating-element"></div>
            <div class="floating-element"></div>
            <div class="floating-element"></div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container">
        <section class="login-section">
            <div class="login-content">
                <!-- Welcome Text -->
                <div class="welcome-text">
                    <div class="welcome-badge">
                        <i class="fas fa-rocket"></i>
                        Welcome Back!
                    </div>
                    <h1 class="welcome-title">
                        Sign in to <span class="welcome-gradient">SmartPOS</span>
                    </h1>
                    <p class="welcome-subtitle">
                        Access your business dashboard and continue managing your sales, 
                        inventory, and customers with our powerful POS system.
                    </p>
                    
                    <ul class="features-list">
                        <li>
                            <i class="fas fa-bolt"></i>
                            <span>Lightning-fast transaction processing</span>
                        </li>
                        <li>
                            <i class="fas fa-chart-line"></i>
                            <span>Real-time analytics and reports</span>
                        </li>
                        <li>
                            <i class="fas fa-shield-alt"></i>
                            <span>Enterprise-grade security</span>
                        </li>
                        <li>
                            <i class="fas fa-sync"></i>
                            <span>Automatic cloud synchronization</span>
                        </li>
                    </ul>
                </div>

                <!-- Login Form -->
                <div class="login-form-container">
                    <div class="login-card">
                        <div class="login-header">
                            <img src="pos_logo.png" alt="SmartPOS" class="login-logo">
                            <h2 class="login-title">Welcome Back</h2>
                            <p class="login-subtitle">Sign in to your account</p>
                        </div>

                        <?php if ($error): ?>
                        <div class="error-message">
                            <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
                        </div>
                        <?php endif; ?>

                        <form method="POST" autocomplete="off">
                            <div class="form-group">
                                <label class="form-label" for="username">Username</label>
                                <div class="input-with-icon">
                                    <input type="text" 
                                           id="username" 
                                           name="username" 
                                           class="form-input" 
                                           placeholder="Enter your username"
                                           value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>"
                                           autocomplete="off"
                                           readonly
                                           onfocus="this.removeAttribute('readonly')"
                                           required>
                                    <i class="fas fa-user"></i>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="form-label" for="password">Password</label>
                                <div class="input-with-icon">
                                    <input type="password" 
                                           id="password" 
                                           name="password" 
                                           class="form-input" 
                                           placeholder="Enter your password"
                                           autocomplete="off"
                                           autocomplete="new-password"
                                           readonly
                                           onfocus="this.removeAttribute('readonly')"
                                           required>
                                    <i class="fas fa-lock"></i>
                                    <button type="button" class="password-toggle" id="togglePassword">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </div>

                            <button type="submit" class="login-btn" id="loginButton">
                                <i class="fas fa-sign-in-alt"></i> Sign In
                            </button>
                        </form>

                        <div class="login-footer">
                            <!-- <div class="demo-credentials">
                                <h4><i class="fas fa-info-circle"></i> Demo Credentials</h4>
                                <p><strong>Username:</strong> admin</p>
                                <p><strong>Password:</strong> admin123</p>
                            </div> -->
                            <p>Need help? Contact <a href="mailto:support@smartpos.com" style="color: var(--electric-blue); text-decoration: none;">support@smartpos.com</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <script>
        // Password toggle functionality
        const togglePassword = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('password');
        
        if (togglePassword && passwordInput) {
            togglePassword.addEventListener('click', function() {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                
                // Toggle eye icon
                const icon = this.querySelector('i');
                if (type === 'password') {
                    icon.classList.remove('fa-eye-slash');
                    icon.classList.add('fa-eye');
                } else {
                    icon.classList.remove('fa-eye');
                    icon.classList.add('fa-eye-slash');
                }
            });
        }

        // Form submission loading state
        const loginForm = document.querySelector('form');
        const loginButton = document.getElementById('loginButton');
        
        if (loginForm && loginButton) {
            loginForm.addEventListener('submit', function(e) {
                const username = document.getElementById('username').value.trim();
                const password = document.getElementById('password').value.trim();
                
                if (!username || !password) {
                    e.preventDefault();
                    return false;
                }
                
                // Add loading state
                loginButton.classList.add('loading');
                loginButton.innerHTML = '';
                
                // Simulate loading for demo
                setTimeout(() => {
                    loginButton.classList.remove('loading');
                    loginButton.innerHTML = '<i class="fas fa-sign-in-alt"></i> Sign In';
                }, 2000);
            });
        }

        // Add input focus effects
        document.querySelectorAll('.form-input').forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.parentElement.style.transform = 'translateY(-2px)';
            });
            
            input.addEventListener('blur', function() {
                this.parentElement.parentElement.style.transform = 'translateY(0)';
            });
        });

        // Auto-focus username field
        document.addEventListener('DOMContentLoaded', function() {
            const usernameField = document.getElementById('username');
            if (usernameField) {
                setTimeout(() => {
                    usernameField.focus();
                }, 500);
            }
        });

        // Add floating animation to login card on hover
        const loginCard = document.querySelector('.login-card');
        if (loginCard) {
            loginCard.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-10px)';
            });
            
            loginCard.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0)';
            });
        }
    </script>
</body>
</html>
