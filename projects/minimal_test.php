<?php
session_start();
$_SESSION['user_id'] = 1;
$_SESSION['full_name'] = 'Test User';
$_SESSION['user_role'] = 'admin';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Minimal Dropdown Test</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        .navbar { background: #f8f9fa; padding: 10px; border: 1px solid #ddd; }
        .dropdown { position: relative; display: inline-block; }
        .dropdown-toggle { 
            background: #007bff; 
            color: white; 
            padding: 10px 15px; 
            border: none; 
            cursor: pointer; 
            border-radius: 4px;
        }
        .dropdown-menu { 
            display: none; 
            position: absolute; 
            top: 100%; 
            right: 0; 
            background: white; 
            border: 1px solid #ddd; 
            border-radius: 4px; 
            box-shadow: 0 2px 10px rgba(0,0,0,0.1); 
            min-width: 200px; 
            z-index: 1000;
        }
        .dropdown-menu.show { display: block; }
        .dropdown-item { 
            display: block; 
            padding: 10px 15px; 
            text-decoration: none; 
            color: #333; 
            border-bottom: 1px solid #eee;
        }
        .dropdown-item:hover { background: #f8f9fa; }
        .status { margin-top: 20px; padding: 10px; background: #e9ecef; border-radius: 4px; }
    </style>
</head>
<body>
    <h1>Minimal Dropdown Test</h1>
    
    <div class="navbar">
        <div class="dropdown" id="userDropdown">
            <button class="dropdown-toggle" id="navbarDropdown">
                <i class="fas fa-user"></i> 
                <?php echo htmlspecialchars($_SESSION['full_name']); ?>
            </button>
            <div class="dropdown-menu" id="userDropdownMenu">
                <a class="dropdown-item" href="#">My Profile</a>
                <a class="dropdown-item" href="#">Manage Users</a>
                <a class="dropdown-item" href="#" onclick="testLogout(event)">Logout</a>
            </div>
        </div>
    </div>
    
    <div class="status">
        <h3>Status: <span id="status">Initializing...</span></h3>
        <p>Click the button above to test the dropdown.</p>
    </div>

    <script>
        function testLogout(event) {
            event.preventDefault();
            alert('Logout clicked!');
        }
        
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Minimal test loaded');
            document.getElementById('status').textContent = 'Page loaded';
            
            const dropdownToggle = document.getElementById('navbarDropdown');
            const dropdownMenu = document.getElementById('userDropdownMenu');
            
            if (dropdownToggle && dropdownMenu) {
                console.log('Elements found');
                document.getElementById('status').textContent = 'Elements found - click to test';
                
                dropdownToggle.addEventListener('click', function(e) {
                    e.preventDefault();
                    console.log('Button clicked');
                    
                    const isVisible = dropdownMenu.classList.contains('show');
                    
                    if (!isVisible) {
                        dropdownMenu.classList.add('show');
                        console.log('Dropdown opened');
                        document.getElementById('status').textContent = 'Dropdown opened';
                        
                        // Close on outside click
                        setTimeout(() => {
                            document.addEventListener('click', function closeDropdown(e) {
                                if (!dropdownToggle.contains(e.target) && !dropdownMenu.contains(e.target)) {
                                    dropdownMenu.classList.remove('show');
                                    document.removeEventListener('click', closeDropdown);
                                    console.log('Closed by outside click');
                                    document.getElementById('status').textContent = 'Dropdown closed';
                                }
                            });
                        }, 100);
                    } else {
                        dropdownMenu.classList.remove('show');
                        console.log('Dropdown closed');
                        document.getElementById('status').textContent = 'Dropdown closed';
                    }
                });
            } else {
                console.error('Elements not found');
                document.getElementById('status').textContent = 'ERROR: Elements not found';
            }
        });
    </script>
</body>
</html> 