// SmartPOS - Beautiful & Perfect Animations

document.addEventListener('DOMContentLoaded', function() {
    
    // Add Google Fonts for better typography
    const link = document.createElement('link');
    link.href = 'https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap';
    link.rel = 'stylesheet';
    document.head.appendChild(link);
    
    // Add loading animation to buttons
    const buttons = document.querySelectorAll('.btn');
    buttons.forEach(button => {
        button.addEventListener('click', function() {
            if (!this.classList.contains('btn-loading')) {
                this.classList.add('btn-loading');
                const originalText = this.innerHTML;
                this.innerHTML = '<span class="loading"></span> Loading...';
                
                // Remove loading state after a delay (simulate loading)
                setTimeout(() => {
                    this.classList.remove('btn-loading');
                    this.innerHTML = originalText;
                }, 1000);
            }
        });
    });
    
    // Add stunning hover effects to cards
    const cards = document.querySelectorAll('.card, .stats-card');
    cards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-8px) scale(1.02)';
            this.style.boxShadow = '0 25px 50px rgba(0, 0, 0, 0.15)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0) scale(1)';
            this.style.boxShadow = '';
        });
    });
    
    // Add beautiful click ripple effect to buttons
    function createRipple(event) {
        const button = event.currentTarget;
        const ripple = document.createElement('span');
        const rect = button.getBoundingClientRect();
        const size = Math.max(rect.width, rect.height);
        const x = event.clientX - rect.left - size / 2;
        const y = event.clientY - rect.top - size / 2;
        
        ripple.style.width = ripple.style.height = size + 'px';
        ripple.style.left = x + 'px';
        ripple.style.top = y + 'px';
        ripple.classList.add('ripple');
        
        button.appendChild(ripple);
        
        setTimeout(() => {
            ripple.remove();
        }, 600);
    }
    
    buttons.forEach(button => {
        button.addEventListener('click', createRipple);
    });
    
    // Add scroll animations with Intersection Observer
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('animate-fade-in');
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0) scale(1)';
            }
        });
    }, observerOptions);
    
    // Observe all cards and tables
    const elementsToAnimate = document.querySelectorAll('.card, .table, .stats-card, .alert');
    elementsToAnimate.forEach(el => {
        el.style.opacity = '0';
        el.style.transform = 'translateY(30px) scale(0.95)';
        observer.observe(el);
    });
    
    // Add beautiful typing animation to numbers
    function animateNumber(element, target, duration = 1500) {
        const start = 0;
        const increment = target / (duration / 16);
        let current = start;
        
        const timer = setInterval(() => {
            current += increment;
            if (current >= target) {
                current = target;
                clearInterval(timer);
            }
            element.textContent = Math.floor(current).toLocaleString();
        }, 16);
    }
    
    // Animate stats numbers on page load with beautiful effects
    const statsNumbers = document.querySelectorAll('.stats-number');
    statsNumbers.forEach((number, index) => {
        const target = parseInt(number.textContent.replace(/[^0-9]/g, ''));
        if (!isNaN(target)) {
            number.textContent = '0';
            setTimeout(() => {
                animateNumber(number, target);
                number.style.animation = 'glow 2s ease-in-out';
            }, 500 + (index * 200));
        }
    });
    
    // Add smooth scrolling to anchor links
    const anchorLinks = document.querySelectorAll('a[href^="#"]');
    anchorLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
    
    // Add beautiful form validation animations
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        const inputs = form.querySelectorAll('input, select, textarea');
        inputs.forEach(input => {
            input.addEventListener('blur', function() {
                if (this.value.trim() === '') {
                    this.classList.add('shake');
                    setTimeout(() => {
                        this.classList.remove('shake');
                    }, 500);
                }
            });
            
            input.addEventListener('input', function() {
                if (this.classList.contains('shake')) {
                    this.classList.remove('shake');
                }
            });
            
            // Add focus effects
            input.addEventListener('focus', function() {
                this.parentElement.style.transform = 'translateY(-2px)';
            });
            
            input.addEventListener('blur', function() {
                this.parentElement.style.transform = 'translateY(0)';
            });
        });
    });
    
    // Add beautiful success/error message animations
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        alert.addEventListener('click', function() {
            this.style.transform = 'scale(0.95)';
            setTimeout(() => {
                this.style.transform = 'scale(1)';
            }, 150);
        });
        
        // Auto-hide alerts after 5 seconds
        setTimeout(() => {
            if (alert.classList.contains('alert-success') || alert.classList.contains('alert-danger')) {
                alert.style.transform = 'translateX(100%)';
                alert.style.opacity = '0';
                setTimeout(() => {
                    alert.remove();
                }, 300);
            }
        }, 5000);
    });
    
    // Add beautiful table row hover effects
    const tableRows = document.querySelectorAll('tbody tr');
    tableRows.forEach(row => {
        row.addEventListener('mouseenter', function() {
            this.style.backgroundColor = 'rgba(102, 126, 234, 0.05)';
            this.style.transform = 'scale(1.01)';
        });
        
        row.addEventListener('mouseleave', function() {
            this.style.backgroundColor = '';
            this.style.transform = 'scale(1)';
        });
    });
    
    // Add stunning modal animations
    const modals = document.querySelectorAll('.modal');
    modals.forEach(modal => {
        modal.addEventListener('show.bs.modal', function() {
            const modalContent = this.querySelector('.modal-content');
            modalContent.style.transform = 'scale(0.7)';
            modalContent.style.opacity = '0';
            
            setTimeout(() => {
                modalContent.style.transform = 'scale(1)';
                modalContent.style.opacity = '1';
            }, 10);
        });
    });
    
    // Add beautiful notification system
    function showNotification(message, type = 'info', duration = 4000) {
        const notification = document.createElement('div');
        notification.className = `notification notification-${type}`;
        notification.innerHTML = `
            <div class="notification-content">
                <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'}"></i>
                <span>${message}</span>
                <button class="notification-close">&times;</button>
            </div>
        `;
        
        // Add styles for notification
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            padding: 15px 20px;
            transform: translateX(100%);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            z-index: 10000;
            max-width: 350px;
            border-left: 4px solid ${type === 'success' ? '#51cf66' : type === 'error' ? '#ff6b6b' : '#339af0'};
        `;
        
        document.body.appendChild(notification);
        
        // Animate in
        setTimeout(() => {
            notification.style.transform = 'translateX(0)';
        }, 10);
        
        // Close button functionality
        const closeBtn = notification.querySelector('.notification-close');
        closeBtn.addEventListener('click', () => {
            notification.style.transform = 'translateX(100%)';
            notification.style.opacity = '0';
            setTimeout(() => {
                notification.remove();
            }, 300);
        });
        
        // Auto-remove after duration
        setTimeout(() => {
            if (notification.parentNode) {
                notification.style.transform = 'translateX(100%)';
                notification.style.opacity = '0';
                setTimeout(() => {
                    notification.remove();
                }, 300);
            }
        }, duration);
    }
    
    // Make notification function globally available
    // window.showNotification = showNotification;
    
    // Add beautiful keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        // Ctrl/Cmd + N for new sale
        // if ((e.ctrlKey || e.metaKey) && e.key === 'n') {
        //     e.preventDefault();
        //     showNotification('Opening new sale...', 'info');
        //     setTimeout(() => {
        //         window.location.href = 'index.php?page=sales';
        //     }, 500);
        // }
        
        // Ctrl/Cmd + P for products
        // if ((e.ctrlKey || e.metaKey) && e.key === 'p') {
        //     e.preventDefault();
        //     showNotification('Opening products...', 'info');
        //     setTimeout(() => {
        //         window.location.href = 'index.php?page=products';
        //     }, 500);
        // }
        
        // Ctrl/Cmd + C for customers
        // if ((e.ctrlKey || e.metaKey) && e.key === 'c') {
        //     e.preventDefault();
        //     showNotification('Opening customers...', 'info');
        //     setTimeout(() => {
        //         window.location.href = 'index.php?page=customers';
        //     }, 500);
        // }
        
        // Escape to close modals
        if (e.key === 'Escape') {
            const openModal = document.querySelector('.modal.show');
            if (openModal) {
                const modal = bootstrap.Modal.getInstance(openModal);
                if (modal) {
                    modal.hide();
                }
            }
        }
    });
    
    // Add beautiful page transition effects
    const links = document.querySelectorAll('a[href*="index.php"]');
    links.forEach(link => {
        link.addEventListener('click', function(e) {
            if (!this.getAttribute('href').includes('#')) {
                e.preventDefault();
                document.body.style.opacity = '0.7';
                document.body.style.transform = 'scale(0.98)';
                
                setTimeout(() => {
                    window.location.href = this.getAttribute('href');
                }, 200);
            }
        });
    });
    
    // Add beautiful search highlight effect
    const searchInputs = document.querySelectorAll('input[type="search"], input[placeholder*="search"]');
    searchInputs.forEach(input => {
        input.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const tableRows = document.querySelectorAll('tbody tr');
            
            tableRows.forEach(row => {
                const text = row.textContent.toLowerCase();
                if (text.includes(searchTerm)) {
                    row.style.display = '';
                    row.style.backgroundColor = searchTerm ? 'rgba(102, 126, 234, 0.1)' : '';
                    row.style.transform = searchTerm ? 'scale(1.02)' : 'scale(1)';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    });
    
    // Add beautiful counter animation for dashboard stats
    function animateCounters() {
        const counters = document.querySelectorAll('.stats-number');
        counters.forEach((counter, index) => {
            const target = parseInt(counter.textContent.replace(/[^0-9]/g, ''));
            if (!isNaN(target)) {
                let current = 0;
                const increment = target / 50;
                const timer = setInterval(() => {
                    current += increment;
                    if (current >= target) {
                        current = target;
                        clearInterval(timer);
                    }
                    counter.textContent = Math.floor(current).toLocaleString();
                }, 20);
            }
        });
    }
    
    // Run counter animation when page loads
    setTimeout(animateCounters, 1000);
    
    // Add mobile menu toggle functionality
    function addMobileMenuToggle() {
        const navbar = document.querySelector('.navbar');
        if (navbar) {
            const toggleBtn = document.createElement('button');
            toggleBtn.className = 'btn btn-primary d-md-none';
            toggleBtn.innerHTML = '<i class="fas fa-bars"></i>';
            toggleBtn.style.cssText = `
                position: fixed;
                top: 20px;
                left: 20px;
                z-index: 1001;
                width: 50px;
                height: 50px;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                box-shadow: var(--shadow-medium);
            `;
            
            toggleBtn.addEventListener('click', function() {
                const sidebar = document.querySelector('.sidebar');
                if (sidebar) {
                    sidebar.classList.toggle('show');
                    this.innerHTML = sidebar.classList.contains('show') ? 
                        '<i class="fas fa-times"></i>' : '<i class="fas fa-bars"></i>';
                }
            });
            
            document.body.appendChild(toggleBtn);
        }
    }
    
    // Add sidebar close button functionality
    function addSidebarCloseButton() {
        const closeBtn = document.getElementById('sidebarClose');
        if (closeBtn) {
            closeBtn.addEventListener('click', function() {
                const sidebar = document.querySelector('.sidebar');
                if (sidebar) {
                    sidebar.classList.remove('show');
                }
            });
        }
    }
    
    // Add mobile menu toggle on mobile devices
    if (window.innerWidth <= 768) {
        addMobileMenuToggle();
        addSidebarCloseButton();
    }
    
    // Add beautiful floating action button for quick actions
    const fab = document.createElement('div');
    fab.className = 'floating-action-btn';
    fab.innerHTML = '<i class="fas fa-plus"></i>';
    fab.style.cssText = `
        position: fixed;
        bottom: 30px;
        right: 30px;
        width: 60px;
        height: 60px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 24px;
        cursor: pointer;
        box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        z-index: 1000;
        animation: float 3s ease-in-out infinite;
    `;
    
    fab.addEventListener('click', function() {
        showNotification('Quick action menu coming soon!', 'info');
    });
    
    fab.addEventListener('mouseenter', function() {
        this.style.transform = 'scale(1.1)';
        this.style.boxShadow = '0 8px 25px rgba(102, 126, 234, 0.6)';
    });
    
    fab.addEventListener('mouseleave', function() {
        this.style.transform = 'scale(1)';
        this.style.boxShadow = '0 5px 15px rgba(102, 126, 234, 0.4)';
    });
    
    // Only add FAB on dashboard
    if (window.location.href.includes('page=dashboard') || window.location.href === window.location.origin + '/index.php') {
        document.body.appendChild(fab);
    }
    
    // Add beautiful particle effects to login page
    if (document.querySelector('.login-container')) {
        createParticles();
    }
    
    function createParticles() {
        const particleCount = 50;
        const container = document.querySelector('.login-container');
        
        for (let i = 0; i < particleCount; i++) {
            const particle = document.createElement('div');
            particle.style.cssText = `
                position: absolute;
                width: ${Math.random() * 4 + 2}px;
                height: ${Math.random() * 4 + 2}px;
                background: rgba(255, 255, 255, ${Math.random() * 0.3 + 0.1});
                border-radius: 50%;
                left: ${Math.random() * 100}%;
                top: ${Math.random() * 100}%;
                animation: float ${Math.random() * 10 + 5}s ease-in-out infinite;
                pointer-events: none;
            `;
            container.appendChild(particle);
        }
    }
    
    // Add beautiful loading screen
    function showLoadingScreen() {
        const loadingScreen = document.createElement('div');
        loadingScreen.id = 'loading-screen';
        loadingScreen.innerHTML = `
            <div class="loading-content">
                <div class="loading-spinner"></div>
                <h3>Loading SmartPOS...</h3>
                <p>Please wait while we prepare your dashboard</p>
            </div>
        `;
        loadingScreen.style.cssText = `
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            color: white;
            text-align: center;
        `;
        
        document.body.appendChild(loadingScreen);
        
        // Remove loading screen after 2 seconds
        setTimeout(() => {
            loadingScreen.style.opacity = '0';
            loadingScreen.style.transform = 'scale(0.9)';
            setTimeout(() => {
                loadingScreen.remove();
            }, 500);
        }, 2000);
    }
    
    // Show loading screen on page load
    if (!document.querySelector('.login-container')) {
        showLoadingScreen();
    }
});

// Add beautiful CSS for additional animations
const additionalStyles = `
    .shake {
        animation: shake 0.5s cubic-bezier(0.36, 0.07, 0.19, 0.97) both;
    }
    
    @keyframes shake {
        10%, 90% { transform: translate3d(-1px, 0, 0); }
        20%, 80% { transform: translate3d(2px, 0, 0); }
        30%, 50%, 70% { transform: translate3d(-4px, 0, 0); }
        40%, 60% { transform: translate3d(4px, 0, 0); }
    }
    
    .ripple {
        position: absolute;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.6);
        transform: scale(0);
        animation: ripple-animation 0.6s linear;
        pointer-events: none;
    }
    
    @keyframes ripple-animation {
        to {
            transform: scale(4);
            opacity: 0;
        }
    }
    
    .notification-content {
        display: flex;
        align-items: center;
        gap: 12px;
    }
    
    .notification-close {
        background: none;
        border: none;
        color: #64748b;
        font-size: 18px;
        cursor: pointer;
        margin-left: auto;
        transition: color 0.3s ease;
    }
    
    .notification-close:hover {
        color: #1e293b;
    }
    
    .btn-loading {
        pointer-events: none;
        opacity: 0.7;
    }
    
    .loading {
        display: inline-block;
        width: 16px;
        height: 16px;
        border: 2px solid rgba(255, 255, 255, 0.3);
        border-radius: 50%;
        border-top-color: #fff;
        animation: spin 1s ease-in-out infinite;
        margin-right: 8px;
    }
    
    .loading-spinner {
        width: 50px;
        height: 50px;
        border: 4px solid rgba(255, 255, 255, 0.3);
        border-radius: 50%;
        border-top-color: #fff;
        animation: spin 1s ease-in-out infinite;
        margin: 0 auto 20px;
    }
    
    @keyframes spin {
        to { transform: rotate(360deg); }
    }
    
    .loading-content h3 {
        font-size: 1.5rem;
        font-weight: 600;
        margin-bottom: 10px;
    }
    
    .loading-content p {
        opacity: 0.8;
        font-size: 0.9rem;
    }
    
    /* Beautiful focus effects */
    .form-control:focus,
    .form-select:focus {
        box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
        border-color: #667eea;
        transform: translateY(-2px);
    }
    
    /* Beautiful hover effects */
    .btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 25px rgba(102, 126, 234, 0.3);
    }
    
    .card:hover {
        transform: translateY(-8px);
        box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15);
    }
    
    /* Beautiful transitions */
    * {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    /* Beautiful scrollbar */
    ::-webkit-scrollbar {
        width: 10px;
    }
    
    ::-webkit-scrollbar-track {
        background: #f1f5f9;
        border-radius: 10px;
    }
    
    ::-webkit-scrollbar-thumb {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 10px;
        border: 2px solid #f1f5f9;
    }
    
    ::-webkit-scrollbar-thumb:hover {
        background: linear-gradient(135deg, #5a6fd8 0%, #6a4190 100%);
    }
`;

// Inject additional styles
const styleSheet = document.createElement('style');
styleSheet.textContent = additionalStyles;
document.head.appendChild(styleSheet); 