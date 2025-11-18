<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SmartPOS - Revolutionize Your Business</title>
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

        /* Navigation */
        .nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 30px 0;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 15px;
            font-size: 1.8rem;
            font-weight: 800;
            background: linear-gradient(135deg, var(--primary-blue), var(--electric-blue));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .logo-img {
            width: 50px;
            height: 50px;
            border-radius: 12px;
        }

        .nav-links {
            display: flex;
            gap: 40px;
            align-items: center;
        }

        .nav-link {
            color: #cbd5e1;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
            position: relative;
        }

        .nav-link:hover {
            color: #fff;
        }

        .nav-link::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 0;
            width: 0;
            height: 2px;
            background: linear-gradient(90deg, var(--primary-blue), var(--primary-green));
            transition: width 0.3s ease;
        }

        .nav-link:hover::after {
            width: 100%;
        }

        /* Hero Section */
        .hero {
            text-align: center;
            padding: 100px 0 150px 0;
            position: relative;
        }

        .hero-badge {
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

        .hero h1 {
            font-size: 5rem;
            font-weight: 800;
            margin-bottom: 30px;
            line-height: 1.1;
        }

        .hero-gradient {
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

        .hero-subtitle {
            font-size: 1.5rem;
            color: #cbd5e1;
            max-width: 700px;
            margin: 0 auto 50px auto;
            line-height: 1.6;
        }

        .cta-buttons {
            display: flex;
            gap: 20px;
            justify-content: center;
            align-items: center;
            flex-wrap: wrap;
        }

        .btn {
            padding: 18px 45px;
            border-radius: 50px;
            font-size: 1.1rem;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 12px;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary-blue), var(--electric-blue));
            color: white;
            box-shadow: 0 10px 30px rgba(37, 99, 235, 0.4);
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 40px rgba(37, 99, 235, 0.6);
        }

        .btn-secondary {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            color: white;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.15);
            transform: translateY(-3px);
        }

        /* Stats Section */
        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 30px;
            margin: -80px auto 100px auto;
            max-width: 1000px;
            position: relative;
            z-index: 2;
        }

        .stat-card {
            background: var(--card-bg);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            padding: 40px 30px;
            text-align: center;
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-10px);
            border-color: rgba(37, 99, 235, 0.3);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
        }

        .stat-number {
            font-size: 3rem;
            font-weight: 800;
            background: linear-gradient(135deg, var(--primary-blue), var(--primary-green));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 10px;
        }

        .stat-label {
            color: #94a3b8;
            font-size: 1.1rem;
        }

        /* Features Section */
        .section {
            padding: 100px 0;
        }

        .section-header {
            text-align: center;
            margin-bottom: 80px;
        }

        .section-title {
            font-size: 3.5rem;
            font-weight: 700;
            margin-bottom: 20px;
            background: linear-gradient(135deg, var(--primary-blue), var(--primary-green));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .section-subtitle {
            font-size: 1.3rem;
            color: #94a3b8;
            max-width: 600px;
            margin: 0 auto;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 40px;
        }

        .feature-card {
            background: var(--card-bg);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 25px;
            padding: 50px 40px;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .feature-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.05), transparent);
            transition: left 0.6s ease;
        }

        .feature-card:hover::before {
            left: 100%;
        }

        .feature-card:hover {
            transform: translateY(-15px) scale(1.02);
            border-color: rgba(37, 99, 235, 0.3);
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.4);
        }

        .feature-icon {
            width: 80px;
            height: 80px;
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 30px;
            font-size: 2rem;
            background: linear-gradient(135deg, var(--primary-blue), var(--electric-blue));
            color: white;
        }

        .feature-card.green .feature-icon {
            background: linear-gradient(135deg, var(--primary-green), var(--secondary-green));
        }

        .feature-card h3 {
            font-size: 1.8rem;
            margin-bottom: 20px;
            color: white;
        }

        .feature-card p {
            color: #cbd5e1;
            line-height: 1.7;
            margin-bottom: 25px;
        }

        .feature-list {
            list-style: none;
        }

        .feature-list li {
            color: #94a3b8;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .feature-list li::before {
            content: '✓';
            color: var(--primary-green);
            font-weight: bold;
        }

        /* CTA Section */
        .cta-section {
            background: linear-gradient(135deg, var(--primary-blue) 0%, var(--primary-green) 100%);
            border-radius: 40px;
            padding: 80px 60px;
            text-align: center;
            margin: 100px 0;
            position: relative;
            overflow: hidden;
        }

        .cta-section::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
            animation: cta-rotate 20s linear infinite;
        }

        @keyframes cta-rotate {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .cta-content {
            position: relative;
            z-index: 1;
        }

        .cta-title {
            font-size: 3rem;
            font-weight: 700;
            margin-bottom: 20px;
            color: white;
        }

        .cta-subtitle {
            font-size: 1.3rem;
            color: rgba(255, 255, 255, 0.9);
            margin-bottom: 40px;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }

        .btn-white {
            background: white;
            color: var(--primary-blue);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        }

        .btn-white:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.3);
        }

        /* Footer */
        .footer {
            background: rgba(0, 0, 0, 0.4);
            backdrop-filter: blur(20px);
            padding: 60px 0 30px 0;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }

        .footer-content {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 50px;
            margin-bottom: 50px;
        }

        .footer-column h3 {
            font-size: 1.3rem;
            margin-bottom: 25px;
            color: white;
        }

        .footer-links {
            list-style: none;
        }

        .footer-links li {
            margin-bottom: 15px;
        }

        .footer-links a {
            color: #94a3b8;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .footer-links a:hover {
            color: var(--electric-blue);
        }

        .footer-bottom {
            text-align: center;
            padding-top: 30px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            color: #94a3b8;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .hero h1 {
                font-size: 3rem;
            }
            
            .section-title {
                font-size: 2.5rem;
            }
            
            .cta-buttons {
                flex-direction: column;
            }
            
            .btn {
                width: 100%;
                justify-content: center;
            }
            
            .nav-links {
                display: none;
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
        <!-- Navigation -->
        <nav class="nav">
            <div class="logo">
                <img src="pos_logo.png" alt="SmartPOS" class="logo-img">
                SmartPOS
            </div>
            <div class="nav-links">
                <a href="#features" class="nav-link">Features</a>
                <a href="#solutions" class="nav-link">Solutions</a>
                <a href="#pricing" class="nav-link">Pricing</a>
                <a href="#about" class="nav-link">About</a>
                <a href="login" class="btn btn-secondary">Sign In</a>
            </div>
        </nav>

        <!-- Hero Section -->
        <section class="hero">
            <div class="hero-badge">
                <i class="fas fa-rocket"></i>
                The Future of Point of Sale is Here
            </div>
            <h1>
                <span class="hero-gradient">Revolutionize</span><br>
                Your Business Operations
            </h1>
            <p class="hero-subtitle">
                SmartPOS combines cutting-edge technology with intuitive design to transform how you manage sales, 
                inventory, and customer relationships. Experience the next generation of business management.
            </p>
            <div class="cta-buttons">
                <a href="login" class="btn btn-primary">
                    Start Free Trial <i class="fas fa-arrow-right"></i>
                </a>
                <a href="#demo" class="btn btn-secondary">
                    <i class="fas fa-play-circle"></i>
                    Watch Demo
                </a>
            </div>
        </section>

        <!-- Stats Section -->
        <div class="stats">
            <div class="stat-card">
                <div class="stat-number">99.9%</div>
                <div class="stat-label">Uptime</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">10K+</div>
                <div class="stat-label">Businesses</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">$2B+</div>
                <div class="stat-label">Processed</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">24/7</div>
                <div class="stat-label">Support</div>
            </div>
        </div>

        <!-- Features Section -->
        <section class="section" id="features">
            <div class="section-header">
                <h2 class="section-title">Powerful Features</h2>
                <p class="section-subtitle">Everything you need to run your business efficiently and grow exponentially</p>
            </div>
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-bolt"></i>
                    </div>
                    <h3>Lightning Fast Sales</h3>
                    <p>Process transactions in milliseconds with our optimized cloud infrastructure. Never keep customers waiting again.</p>
                    <ul class="feature-list">
                        <li>Instant transaction processing</li>
                        <li>Offline mode capability</li>
                        <li>Batch operations</li>
                        <li>Quick product search</li>
                    </ul>
                </div>

                <div class="feature-card green">
                    <div class="feature-icon">
                        <i class="fas fa-chart-network"></i>
                    </div>
                    <h3>Smart Analytics</h3>
                    <p>Make data-driven decisions with real-time insights and predictive analytics that grow with your business.</p>
                    <ul class="feature-list">
                        <li>Real-time sales dashboards</li>
                        <li>Predictive inventory management</li>
                        <li>Customer behavior analysis</li>
                        <li>Custom report builder</li>
                    </ul>
                </div>

                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-shield-check"></i>
                    </div>
                    <h3>Enterprise Security</h3>
                    <p>Bank-level security with end-to-end encryption, multi-factor authentication, and compliance certifications.</p>
                    <ul class="feature-list">
                        <li>PCI DSS compliant</li>
                        <li>End-to-end encryption</li>
                        <li>Multi-factor authentication</li>
                        <li>Automated backups</li>
                    </ul>
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <section class="cta-section">
            <div class="cta-content">
                <h2 class="cta-title">Ready to Transform Your Business?</h2>
                <p class="cta-subtitle">Join thousands of businesses that trust SmartPOS to power their operations and drive growth.</p>
                <a href="login" class="btn btn-white">
                    Get Started Today <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </section>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-column">
                    <h3>SmartPOS</h3>
                    <p style="color: #94a3b8; margin-bottom: 20px;">The ultimate point of sale solution for modern businesses.</p>
                </div>
                <div class="footer-column">
                    <h3>Product</h3>
                    <ul class="footer-links">
                        <li><a href="#features">Features</a></li>
                        <li><a href="#solutions">Solutions</a></li>
                        <li><a href="#pricing">Pricing</a></li>
                        <li><a href="#demo">Demo</a></li>
                    </ul>
                </div>
                <div class="footer-column">
                    <h3>Resources</h3>
                    <ul class="footer-links">
                        <li><a href="#blog">Blog</a></li>
                        <li><a href="#help">Help Center</a></li>
                        <li><a href="#community">Community</a></li>
                        <li><a href="#api">API Docs</a></li>
                    </ul>
                </div>
                <div class="footer-column">
                    <h3>Company</h3>
                    <ul class="footer-links">
                        <li><a href="#about">About Us</a></li>
                        <li><a href="#careers">Careers</a></li>
                        <li><a href="#contact">Contact</a></li>
                        <li><a href="#partners">Partners</a></li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2024 SmartPOS. All rights reserved. | <a href="mailto:info@smartpos.com" style="color: var(--electric-blue);">info@smartpos.com</a></p>
            </div>
        </div>
    </footer>

    <script>
        // Animate stats counting
        function animateStats() {
            const statNumbers = document.querySelectorAll('.stat-number');
            statNumbers.forEach(stat => {
                const target = parseInt(stat.textContent);
                let current = 0;
                const increment = target / 50;
                const timer = setInterval(() => {
                    current += increment;
                    if (current >= target) {
                        stat.textContent = target + (stat.textContent.includes('%') ? '%' : '+');
                        clearInterval(timer);
                    } else {
                        stat.textContent = Math.floor(current) + (stat.textContent.includes('%') ? '%' : '+');
                    }
                }, 40);
            });
        }

        // Intersection Observer for animations
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    if (entry.target.classList.contains('stats')) {
                        animateStats();
                    }
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, { threshold: 0.1 });

        // Observe elements for animation
        document.addEventListener('DOMContentLoaded', () => {
            const animatedElements = document.querySelectorAll('.stat-card, .feature-card, .section-header');
            animatedElements.forEach(el => {
                el.style.opacity = '0';
                el.style.transform = 'translateY(30px)';
                el.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
                observer.observe(el);
            });
        });

        // Add floating particles on mouse move
        document.addEventListener('mousemove', (e) => {
            const particles = document.createElement('div');
            particles.style.position = 'fixed';
            particles.style.width = '4px';
            particles.style.height = '4px';
            particles.style.background = `radial-gradient(circle, ${Math.random() > 0.5 ? 'var(--electric-blue)' : 'var(--secondary-green)'}, transparent)`;
            particles.style.borderRadius = '50%';
            particles.style.pointerEvents = 'none';
            particles.style.left = e.clientX + 'px';
            particles.style.top = e.clientY + 'px';
            particles.style.zIndex = '9999';
            particles.style.animation = `particle-fade 1s forwards`;
            
            document.body.appendChild(particles);
            
            setTimeout(() => {
                particles.remove();
            }, 1000);
        });

        // Add CSS for particle fade animation
        const style = document.createElement('style');
        style.textContent = `
            @keyframes particle-fade {
                0% { opacity: 1; transform: scale(1); }
                100% { opacity: 0; transform: scale(3); }
            }
        `;
        document.head.appendChild(style);
    </script>
</body>
</html>
