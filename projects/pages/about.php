<?php
// pages/about.php
$page = 'about';
?>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;700&display=swap" rel="stylesheet">

<div class="container py-5 fade-in">
    <div class="row justify-content-center mb-4">
        <div class="col-lg-8 text-center">
            <a href="./index.php"  rel="noopener">
                <img src="pos_logo.png" alt="POS System Logo" class="about-logo mb-3 animated-logo">
            </a>
        </div>
    </div>
    <div class="row justify-content-center mb-5">
        <div class="col-lg-8 text-center">
            <h1 class="mb-4 display-4 fw-bold animated-title">About <span class="text-primary">POS System</span></h1>
            <p class="lead mb-4 animated-fadein">
                Our POS (Point of Sale) System is designed to streamline your business operations, making sales, inventory, and customer management effortless and efficient. Built with modern technologies, it offers a user-friendly interface, robust reporting, and secure data handling.
            </p>
            <hr class="my-4 about-hr animated-hr">
            <h3 class="mb-3 animated-fadein">Key Features</h3>
        </div>
    </div>
    <div class="row justify-content-center mb-5 animated-fadein">
        <div class="col-lg-10">
            <div class="features-section">
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="feature-group">
                            <h5 class="feature-category"><i class="fas fa-chart-bar"></i> Analytics & Reporting</h5>
                            <ul class="features-list animated-list">
                                <li data-bs-toggle="tooltip" title="See your business at a glance with real-time data."><span class="feature-icon bg-primary"><i class="fas fa-tachometer-alt"></i></span> Intuitive Dashboard & Real-time Analytics</li>
                                <li data-bs-toggle="tooltip" title="Generate and customize detailed sales reports."><span class="feature-icon bg-info"><i class="fas fa-file-alt"></i></span> Customizable Reports</li>
                                <li data-bs-toggle="tooltip" title="Track sales trends and performance."><span class="feature-icon bg-success"><i class="fas fa-chart-line"></i></span> Sales Tracking & Performance</li>
                                <li data-bs-toggle="tooltip" title="Export your data for further analysis."><span class="feature-icon bg-warning"><i class="fas fa-file-export"></i></span> Data Export (CSV, PDF)</li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="feature-group">
                            <h5 class="feature-category"><i class="fas fa-cubes"></i> Inventory & Products</h5>
                            <ul class="features-list animated-list">
                                <li data-bs-toggle="tooltip" title="Easily manage your products and stock levels."><span class="feature-icon bg-secondary"><i class="fas fa-box"></i></span> Product & Inventory Management</li>
                                <li data-bs-toggle="tooltip" title="Get notified when stock is low."><span class="feature-icon bg-danger"><i class="fas fa-exclamation-triangle"></i></span> Low Stock Alerts</li>
                                <li data-bs-toggle="tooltip" title="Scan barcodes for fast checkout."><span class="feature-icon bg-dark"><i class="fas fa-barcode"></i></span> Fast Barcode Scanning</li>
                                <li data-bs-toggle="tooltip" title="Organize products by categories."><span class="feature-icon bg-primary"><i class="fas fa-tags"></i></span> Category Management</li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="feature-group">
                            <h5 class="feature-category"><i class="fas fa-users"></i> Customers & Users</h5>
                            <ul class="features-list animated-list">
                                <li data-bs-toggle="tooltip" title="Manage customer profiles and history."><span class="feature-icon bg-info"><i class="fas fa-user-friends"></i></span> Customer Management</li>
                                <li data-bs-toggle="tooltip" title="Assign roles and permissions."><span class="feature-icon bg-success"><i class="fas fa-users-cog"></i></span> User & Role Management</li>
                                <li data-bs-toggle="tooltip" title="Secure login and data protection."><span class="feature-icon bg-warning"><i class="fas fa-lock"></i></span> Secure Login & Data Protection</li>
                                <li data-bs-toggle="tooltip" title="Multi-language support for global users."><span class="feature-icon bg-primary"><i class="fas fa-language"></i></span> Multi-language Support</li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="feature-group">
                            <h5 class="feature-category"><i class="fas fa-cogs"></i> Advanced & Integrations</h5>
                            <ul class="features-list animated-list">
                                <li data-bs-toggle="tooltip" title="Access your data anywhere, anytime."><span class="feature-icon bg-info"><i class="fas fa-cloud-upload-alt"></i></span> Cloud Backup</li>
                                <li data-bs-toggle="tooltip" title="Works perfectly on mobile devices."><span class="feature-icon bg-success"><i class="fas fa-mobile-alt"></i></span> Mobile Friendly</li>
                                <li data-bs-toggle="tooltip" title="Integrate with other business tools."><span class="feature-icon bg-dark"><i class="fas fa-plug"></i></span> 3rd Party Integrations</li>
                                <li data-bs-toggle="tooltip" title="Customizable to fit your business needs."><span class="feature-icon bg-secondary"><i class="fas fa-sliders-h"></i></span> Highly Customizable</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row justify-content-center mb-5 animated-fadein">
        <div class="col-md-5 mb-4">
            <div class="profile-card glass teacher-card animate-profile animate-delay-1">
                <div class="profile-bg-accent"></div>
                <div class="profile-img-wrapper">
                    <img src="./teacher.jpg" class="profile-img" alt="Teacher Photo">
                </div>
                <div class="profile-body text-center">
                    <span class="role-badge teacher-badge">Course Teacher</span>
                    <p class="profile-name fw-bold">Eng. Mohamed Abdulahi</p>
                    <p ></p>
                </div>
            </div>
        </div>
        <div class="col-md-5 mb-4">
            <div class="profile-card glass developer-card animate-profile animate-delay-2">
                <div class="profile-bg-accent"></div>
                <div class="profile-img-wrapper">
                    <img src="./Muscab.jpg" class="profile-img" alt="Developer Photo">
                </div>
                <div class="profile-body text-center">
                    <span class="role-badge developer-badge">System Developer</span>
                    <p class="profile-name fw-bold">Muscab Axmed Maxamud</p>
                    <a href="https://github.com/ENG-Qaarey" class="profile-link" target="_blank" rel="noopener"><i class="fas fa-link"></i> ENG-Qaarey</a>
                </div>
            </div>
        </div>
    </div>
    <div class="row justify-content-center">
        <div class="col-lg-8 text-center">
            <hr class="my-4 about-hr animated-hr">
            <h3 class="mb-3 animated-fadein">Contact</h3>
            <p class="mb-4 animated-fadein">For inquiries or support, please contact us below.</p>
            <a href="https://wa.me/252614463895" target="_blank" class="btn btn-primary btn-lg animated-fadein">Contact Support</a>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

<!-- Beautiful About Page Animations and Styles -->
<style>
body, .lead, .profile-name, .profile-title, .role-badge, .feature-category, .features-list li {
    font-family: 'Poppins', 'Montserrat', Arial, sans-serif !important;
}
.lead {
    font-family: 'Poppins', 'Montserrat', Arial, sans-serif !important;
    font-size: 1.25rem;
    font-weight: 500;
    color: #3b3663;
    letter-spacing: 0.01em;
    line-height: 1.7;
    text-shadow: 0 2px 8px rgba(99,102,241,0.06);
    background: linear-gradient(90deg, #f8fafc 60%, #e0e7ff 100%);
    border-radius: 1.2rem;
    padding: 1.1rem 1.5rem;
    margin-bottom: 2rem;
    display: inline-block;
    box-shadow: 0 2px 12px rgba(99,102,241,0.07);
    animation: fadeInSection 1.2s ease-in-out 0.8s backwards;
}
.features-section {
    background: linear-gradient(120deg, #f8fafc 60%, #e0e7ff 100%);
    border-radius: 2rem;
    box-shadow: 0 8px 40px rgba(78, 84, 200, 0.10);
    padding: 2.5rem 2rem 2rem 2rem;
    margin-bottom: 2.5rem;
    position: relative;
    overflow: hidden;
}
.feature-category {
    font-size: 1.2rem;
    font-weight: 700;
    margin-bottom: 1.1rem;
    color: #6366f1;
    letter-spacing: 0.5px;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}
.features-list {
    list-style: none;
    padding: 0;
    margin: 0 0 1.5rem 0;
    text-align: left;
}
.features-list li {
    background: #fff;
    margin-bottom: 16px;
    padding: 16px 22px;
    border-radius: 1.2rem;
    font-size: 1.08rem;
    font-weight: 500;
    display: flex;
    align-items: center;
    box-shadow: 0 2px 12px rgba(78, 84, 200, 0.07);
    transition: transform 0.2s, box-shadow 0.2s, background 0.2s;
    position: relative;
    overflow: hidden;
    cursor: pointer;
    border-left: 6px solid #6366f1;
    opacity: 0;
    transform: translateX(-40px) scale(0.98);
    animation: fadeInList 0.7s cubic-bezier(.68,-0.55,.27,1.55) forwards;
}
.features-list li:hover {
    transform: translateY(-4px) scale(1.04);
    box-shadow: 0 6px 24px rgba(78, 84, 200, 0.13);
    background: linear-gradient(90deg, #e0e7ff 0%, #f8fafc 100%);
    border-left: 6px solid #f59e42;
}
.features-list li .feature-icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 38px;
    height: 38px;
    border-radius: 50%;
    color: #fff;
    font-size: 1.3rem;
    margin-right: 18px;
    box-shadow: 0 2px 8px rgba(99, 102, 241, 0.13);
    flex-shrink: 0;
    background: linear-gradient(135deg, #6366f1 60%, #818cf8 100%);
    transition: background 0.3s;
}
.features-list li .feature-icon.bg-primary { background: linear-gradient(135deg, #6366f1 60%, #818cf8 100%); }
.features-list li .feature-icon.bg-info { background: linear-gradient(135deg, #38bdf8 60%, #818cf8 100%); }
.features-list li .feature-icon.bg-success { background: linear-gradient(135deg, #22c55e 60%, #818cf8 100%); }
.features-list li .feature-icon.bg-warning { background: linear-gradient(135deg, #fbbf24 60%, #818cf8 100%); }
.features-list li .feature-icon.bg-secondary { background: linear-gradient(135deg, #a1a1aa 60%, #818cf8 100%); }
.features-list li .feature-icon.bg-danger { background: linear-gradient(135deg, #ef4444 60%, #818cf8 100%); }
.features-list li .feature-icon.bg-dark { background: linear-gradient(135deg, #334155 60%, #818cf8 100%); }

.features-list li[data-bs-toggle="tooltip"]:hover::after {
    content: attr(title);
    position: absolute;
    left: 60px;
    top: 100%;
    background: #6366f1;
    color: #fff;
    padding: 7px 16px;
    border-radius: 0.7rem;
    font-size: 0.98rem;
    white-space: nowrap;
    margin-top: 8px;
    box-shadow: 0 2px 8px rgba(99, 102, 241, 0.13);
    z-index: 10;
    opacity: 0.97;
    pointer-events: none;
    animation: tooltipFadeIn 0.3s;
}
@keyframes tooltipFadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 0.97; transform: translateY(0); }
}
.animated-list li:nth-child(1) { animation-delay: 0.2s; }
.animated-list li:nth-child(2) { animation-delay: 0.4s; }
.animated-list li:nth-child(3) { animation-delay: 0.6s; }
.animated-list li:nth-child(4) { animation-delay: 0.8s; }
.animated-list li:nth-child(5) { animation-delay: 1.0s; }
.animated-list li:nth-child(6) { animation-delay: 1.2s; }
.animated-list li:nth-child(7) { animation-delay: 1.4s; }
.animated-list li:nth-child(8) { animation-delay: 1.6s; }
.animated-list li:nth-child(9) { animation-delay: 1.8s; }
.animated-list li:nth-child(10) { animation-delay: 2.0s; }
@keyframes fadeInList {
    from { opacity: 0; transform: translateX(-40px) scale(0.98); }
    to { opacity: 1; transform: translateX(0) scale(1); }
}
.fade-in {
    opacity: 0;
    animation: fadeInAbout 1.2s ease-in-out 0.2s forwards;
}
.animated-title {
    opacity: 0;
    animation: fadeInTitle 1.2s cubic-bezier(.68,-0.55,.27,1.55) 0.4s forwards;
}
.animated-fadein {
    opacity: 0;
    animation: fadeInSection 1.2s ease-in-out 0.8s forwards;
}
@keyframes fadeInAbout {
    from { opacity: 0; transform: translateY(40px); }
    to { opacity: 1; transform: translateY(0); }
}
@keyframes fadeInTitle {
    from { opacity: 0; transform: scale(0.95) translateY(-30px); }
    to { opacity: 1; transform: scale(1) translateY(0); }
}
@keyframes fadeInSection {
    from { opacity: 0; transform: translateY(30px); }
    to { opacity: 1; transform: translateY(0); }
}
/* --- Team Cards Enhanced --- */
.profile-card.glass {
    background: rgba(255,255,255,0.18);
    border-radius: 2rem;
    box-shadow: 0 8px 40px 0 rgba(99,102,241,0.13), 0 1.5px 8px 0 rgba(0,0,0,0.04);
    backdrop-filter: blur(8px);
    border: 2.5px solid rgba(99,102,241,0.13);
    position: relative;
    overflow: visible;
    padding: 2.5rem 1.5rem 1.5rem 1.5rem;
    min-height: 340px;
    transition: box-shadow 0.4s, transform 0.4s;
    z-index: 1;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: flex-end;
}
.profile-card:hover {
    box-shadow: 0 16px 48px 0 rgba(99,102,241,0.22), 0 1.5px 8px 0 rgba(0,0,0,0.08);
    transform: translateY(-8px) scale(1.04) rotate(-1deg);
}
.profile-img-wrapper {
    position: relative;
    margin-top: -80px;
    margin-bottom: 1.2rem;
    z-index: 2;
}
.profile-img {
    width: 140px;
    height: 140px;
    object-fit: cover;
    border-radius: 50%;
    border: 6px solid #fff;
    box-shadow: 0 0 0 8px #6366f1, 0 8px 32px rgba(78, 84, 200, 0.13);
    background: #fff;
    transition: box-shadow 0.4s, transform 0.4s;
    animation: floatInProfile 1.2s cubic-bezier(.68,-0.55,.27,1.55) 0.7s backwards;
}
.profile-card:hover .profile-img {
    box-shadow: 0 0 0 14px #818cf8, 0 16px 48px rgba(99,102,241,0.18);
    transform: scale(1.08) rotate(2deg);
}
.teacher-card .profile-img {
    box-shadow: 0 0 0 8px #f59e42, 0 8px 32px rgba(245, 158, 66, 0.13);
}
.teacher-card:hover .profile-img {
    box-shadow: 0 0 0 14px #fbbf24, 0 16px 48px rgba(245, 158, 66, 0.18);
}
.developer-card .profile-img {
    box-shadow: 0 0 0 8px #6366f1, 0 8px 32px rgba(99,102,241,0.13);
}
.developer-card:hover .profile-img {
    box-shadow: 0 0 0 14px #818cf8, 0 16px 48px rgba(99,102,241,0.18);
}
.profile-title {
    font-size: 1.2rem;
    font-weight: 700;
    color: #6366f1;
    letter-spacing: 0.5px;
    margin-bottom: 0.2rem;
}
.profile-name {
    font-size: 1.1rem;
    color: #22223b;
    letter-spacing: 0.2px;
}
.profile-bg-accent {
    position: absolute;
    top: -40px;
    left: 50%;
    transform: translateX(-50%);
    width: 180px;
    height: 180px;
    background: radial-gradient(circle at 60% 40%, #6366f1 0%, #818cf8 100%, transparent 80%);
    opacity: 0.18;
    filter: blur(12px);
    z-index: 0;
    pointer-events: none;
    animation: accentPulse 3.5s infinite alternate;
}
.teacher-card .profile-bg-accent {
    background: radial-gradient(circle at 60% 40%, #f59e42 0%, #fbbf24 100%, transparent 80%);
}
@keyframes accentPulse {
    from { opacity: 0.18; transform: scale(1) translateX(-50%); }
    to { opacity: 0.32; transform: scale(1.08) translateX(-50%); }
}
@keyframes floatInProfile {
    from { opacity: 0; transform: translateY(60px) scale(0.8); }
    to { opacity: 1; transform: translateY(0) scale(1); }
}
.animate-profile {
    opacity: 0;
    animation: fadeInSection 1.2s ease-in-out 1.2s forwards;
}
.animate-delay-1 { animation-delay: 0.2s !important; }
.animate-delay-2 { animation-delay: 0.6s !important; }
.role-badge {
    display: inline-block;
    padding: 0.35em 1.1em;
    font-size: 1.02rem;
    font-weight: 600;
    border-radius: 1.2em;
    margin-bottom: 0.7em;
    letter-spacing: 0.5px;
    background: linear-gradient(90deg, #6366f1 0%, #818cf8 100%);
    color: #fff;
    box-shadow: 0 2px 8px rgba(99,102,241,0.13);
    animation: badgeFadeIn 1.2s cubic-bezier(.68,-0.55,.27,1.55) 1.2s backwards;
}
.teacher-badge {
    background: linear-gradient(90deg, #f59e42 0%, #fbbf24 100%);
    color: #fff;
}
.developer-badge {
    background: linear-gradient(90deg, #6366f1 0%, #818cf8 100%);
    color: #fff;
}
.profile-name {
    font-size: 1.18rem;
    color: #22223b;
    letter-spacing: 0.2px;
    margin-bottom: 0.2em;
    animation: nameFadeIn 1.2s cubic-bezier(.68,-0.55,.27,1.55) 1.4s backwards;
}
.profile-link {
    display: inline-block;
    font-size: 1.05rem;
    color: #6366f1;
    font-weight: 600;
    text-decoration: none;
    margin-top: 0.2em;
    transition: color 0.2s;
    animation: nameFadeIn 1.2s cubic-bezier(.68,-0.55,.27,1.55) 1.6s backwards;
}
.profile-link:hover {
    color: #f59e42;
    text-decoration: underline;
}
.profile-link i {
    margin-right: 0.4em;
    font-size: 1.1em;
    vertical-align: middle;
    transition: color 0.2s;
}
.profile-link:hover i {
    color: #f59e42;
}
@keyframes badgeFadeIn {
    from { opacity: 0; transform: translateY(-20px) scale(0.95); }
    to { opacity: 1; transform: translateY(0) scale(1); }
}
@keyframes nameFadeIn {
    from { opacity: 0; transform: translateY(20px) scale(0.95); }
    to { opacity: 1; transform: translateY(0) scale(1); }
}
@media (max-width: 768px) {
    .profile-img {
        width: 90px;
        height: 90px;
        margin-top: -40px;
    }
    .profile-card.glass {
        min-height: 260px;
        padding: 1.2rem 0.5rem 1.2rem 0.5rem;
    }
    .profile-bg-accent {
        width: 110px;
        height: 110px;
        top: -20px;
    }
}
.about-logo {
    width: 90px;
    height: 90px;
    object-fit: contain;
    border-radius: 1.2rem;
    box-shadow: 0 4px 24px rgba(99,102,241,0.10);
    background: #fff;
    padding: 0.5rem;
    animation: logoPop 1.2s cubic-bezier(.68,-0.55,.27,1.55) 0.2s backwards;
}
@keyframes logoPop {
    from { opacity: 0; transform: scale(0.7) rotate(-10deg); }
    to { opacity: 1; transform: scale(1) rotate(0); }
}
.about-hr {
    border: none;
    height: 6px;
    width: 100%;
    border-radius: 3px;
    background: linear-gradient(90deg, #6366f1 0%, #818cf8 40%, #fbbf24 80%, #f59e42 100%);
    box-shadow: 0 4px 24px 0 rgba(99,102,241,0.18), 0 1.5px 8px 0 rgba(245, 158, 66, 0.10);
    margin: 2.7rem 0 2.7rem 0;
    position: relative;
    overflow: hidden;
    opacity: 0;
    animation: hrReveal 1.5s cubic-bezier(.68,-0.55,.27,1.55) 0.5s forwards;
}
.about-hr::after {
    content: '';
    position: absolute;
    left: -60px;
    top: 0;
    width: 80px;
    height: 100%;
    background: linear-gradient(120deg, rgba(255,255,255,0.0) 0%, rgba(255,255,255,0.7) 50%, rgba(255,255,255,0.0) 100%);
    filter: blur(1.5px);
    opacity: 0.7;
    animation: hrShimmer 2.8s infinite linear 1.2s;
}
@keyframes hrReveal {
    from { width: 0; opacity: 0; box-shadow: none; }
    60%  { width: 100%; opacity: 1; box-shadow: 0 4px 24px 0 rgba(99,102,241,0.18); }
    to   { width: 100%; opacity: 1; box-shadow: 0 4px 24px 0 rgba(99,102,241,0.18); }
}
@keyframes hrShimmer {
    0% { left: -60px; }
    100% { left: 100%; }
}
</style>
<script>
document.addEventListener('DOMContentLoaded', function() {
  var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
  tooltipTriggerList.forEach(function (el) {
    el.addEventListener('mouseenter', function() {
      // Bootstrap tooltips are handled by CSS in this case
    });
  });
});
</script> 