<?php
session_start();
require_once 'config/database.php';

// If already logged in, redirect to dashboard
if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitizeInput($_POST['username']);
    $password = $_POST['password'];
    
    if (empty($username) || empty($password)) {
        $error = 'Please enter both username and password.';
    } else {
        $sql = "SELECT id, username, password, full_name, role FROM users WHERE username = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            
            if (password_verify($password, $user['password'])) {
                // Set session variables
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['full_name'] = $user['full_name'];
                $_SESSION['user_role'] = $user['role'];
                
                header('Location: index.php');
                exit();
            } else {
                $error = 'Invalid username or password.';
            }
        } else {
            $error = 'Invalid username or password.';
        }
    }
}

function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
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
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        html, body {
            height: 100%;
            overflow: hidden;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            position: relative;
            min-height: 100vh;
        }
        
        /* Animated background with particles */
        .particles {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 1;
        }
        
        .particle {
            position: absolute;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            animation: float-particle 20s infinite linear;
        }
        
        .particle:nth-child(1) { width: 4px; height: 4px; left: 10%; animation-delay: 0s; }
        .particle:nth-child(2) { width: 6px; height: 6px; left: 20%; animation-delay: 2s; }
        .particle:nth-child(3) { width: 3px; height: 3px; left: 30%; animation-delay: 4s; }
        .particle:nth-child(4) { width: 5px; height: 5px; left: 40%; animation-delay: 6s; }
        .particle:nth-child(5) { width: 4px; height: 4px; left: 50%; animation-delay: 8s; }
        .particle:nth-child(6) { width: 6px; height: 6px; left: 60%; animation-delay: 10s; }
        .particle:nth-child(7) { width: 3px; height: 3px; left: 70%; animation-delay: 12s; }
        .particle:nth-child(8) { width: 5px; height: 5px; left: 80%; animation-delay: 14s; }
        .particle:nth-child(9) { width: 4px; height: 4px; left: 90%; animation-delay: 16s; }
        .particle:nth-child(10) { width: 6px; height: 6px; left: 95%; animation-delay: 18s; }
        
        @keyframes float-particle {
            0% { transform: translateY(100vh) rotate(0deg); opacity: 0; }
            10% { opacity: 1; }
            90% { opacity: 1; }
            100% { transform: translateY(-100px) rotate(360deg); opacity: 0; }
        }
        
        /* Animated gradient background */
        .gradient-bg {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(-45deg, #667eea, #764ba2, #f093fb, #f5576c);
            background-size: 400% 400%;
            animation: gradient-shift 15s ease infinite;
            z-index: 0;
        }
        
        @keyframes gradient-shift {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        
        /* Glassmorphic card with enhanced effects */
        .login-container {
            position: relative;
            z-index: 10;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 20px;
        }
        
        .login-glass {
            background: rgba(255, 255, 255, 0);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-radius: 24px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 
                0 8px 32px 0 rgba(31, 38, 135, 0.37),
                inset 0 1px 0 rgba(255, 255, 255, 0.2);
            padding: 40px;
            max-width: 400px;
            width: 100%;
            animation: slideInUp 1s cubic-bezier(0.68, -0.55, 0.265, 1.55);
            position: relative;
            overflow: hidden;
        }
        
        .login-glass::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.1), transparent);
            animation: shimmer 3s infinite;
        }
        
        @keyframes shimmer {
            0% { left: -100%; }
            100% { left: 100%; }
        }
        
        @keyframes slideInUp {
            0% {
                opacity: 0;
                transform: translateY(60px) scale(0.9);
            }
            100% {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }
        
        /* Logo section with enhanced animations */
        .login-logo {
            text-align: center;
            margin-bottom: 30px;
            animation: fadeInDown 1.2s ease-out;
        }
        
        @keyframes fadeInDown {
            0% {
                opacity: 0;
                transform: translateY(-30px);
            }
            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .logo-container {
            position: relative;
            display: inline-block;
            margin-bottom: 15px;
        }
        
        .logo-container img {
            width: 80px;
            height: 80px;
            border-radius: 20px;
            box-shadow: 
                0 8px 32px rgba(0, 0, 0, 0.3),
                0 0 0 1px rgba(255, 255, 255, 0.1);
            animation: logoBounce 2s ease-in-out infinite;
            transition: transform 0.3s ease;
        }
        
        .logo-container:hover img {
            transform: scale(1.1) rotate(5deg);
        }
        
        @keyframes logoBounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }
        
        .brand-name {
            font-size: 2rem;
            font-weight: 700;
            background: linear-gradient(135deg, #667eea, #764ba2);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 5px;
            letter-spacing: 2px;
        }
        
        .brand-subtitle {
            color: rgba(255, 255, 255, 0.8);
            font-size: 0.9rem;
            font-weight: 300;
            letter-spacing: 1px;
        }
        
        /* Enhanced form styling */
        .login-form {
            animation: fadeInUp 1.4s ease-out;
        }
        
        @keyframes fadeInUp {
            0% {
                opacity: 0;
                transform: translateY(30px);
            }
            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .input-group {
            position: relative;
            margin-bottom: 25px;
        }
        
        .input-group input {
            width: 100%;
            padding: 15px 20px 15px 50px;
            background: rgba(255, 255, 255, 0.1);
            border: 2px solid rgba(255, 255, 255, 0.2);
            border-radius: 15px;
            color: white;
            font-size: 1rem;
            font-weight: 400;
            outline: none;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
        }
        
        .input-group input::placeholder {
            color: rgba(255, 255, 255, 0.6);
        }
        
        .input-group input:focus {
            border-color: rgba(255, 255, 255, 0.5);
            box-shadow: 0 0 20px rgba(255, 255, 255, 0.2);
            transform: translateY(-2px);
        }
        
        .input-group i {
            position: absolute;
            left: 18px;
            top: 50%;
            transform: translateY(-50%);
            color: rgba(255, 255, 255, 0.7);
            font-size: 1.2rem;
            transition: all 0.3s ease;
        }
        
        .input-group input:focus + i {
            color: white;
            transform: translateY(-50%) scale(1.1);
        }
        
        /* Enhanced button styling */
        .login-btn {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 15px;
            color: white;
            font-size: 1.1rem;
            font-weight: 600;
            letter-spacing: 1px;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
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
            box-shadow: 0 15px 35px rgba(102, 126, 234, 0.4);
        }
        
        .login-btn:active {
            transform: translateY(-1px);
        }
        
        /* Footer styling */
        .login-footer {
            text-align: center;
            margin-top: 25px;
            animation: fadeIn 2s ease-out;
        }
        
        @keyframes fadeIn {
            0% { opacity: 0; }
            100% { opacity: 1; }
        }
        
        .login-footer small {
            color: rgba(255, 255, 255, 0.7);
            font-size: 0.85rem;
            font-weight: 300;
        }
        
        /* Responsive design */
        @media (max-width: 480px) {
            .login-glass {
                padding: 30px 20px;
                margin: 10px;
            }
            
            .brand-name {
                font-size: 1.6rem;
            }
            
            .logo-container img {
                width: 60px;
                height: 60px;
            }
        }
        
        /* Loading animation for button */
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

        /* Enhanced Geometric Spin Animated Background */
        .geometric-bg {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            z-index: 1;
            overflow: hidden;
            pointer-events: none;
        }
        .geo-shape {
            position: absolute;
            opacity: 0.16;
            filter: blur(0.5px) drop-shadow(0 0 12px rgba(102,126,234,0.18));
            box-shadow: 0 0 24px 4px rgba(102,126,234,0.12);
            transition: opacity 0.3s;
        }
        .circle {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea, #f093fb);
            animation: spin 12s linear infinite;
        }
        .square {
            width: 90px;
            height: 90px;
            background: linear-gradient(135deg, #f5576c, #764ba2);
            border-radius: 18px;
            animation: spin-rev 16s linear infinite;
        }
        .triangle {
            width: 0;
            height: 0;
            border-left: 60px solid transparent;
            border-right: 60px solid transparent;
            border-bottom: 100px solid #f093fb;
            animation: spin 18s linear infinite;
        }
        .hexagon {
            width: 100px;
            height: 55px;
            background: linear-gradient(135deg, #764ba2, #667eea);
            clip-path: polygon(25% 0%, 75% 0%, 100% 50%, 75% 100%, 25% 100%, 0% 50%);
            animation: spin-rev 22s linear infinite;
        }
        .ellipse {
            width: 110px;
            height: 60px;
            border-radius: 50% 50% / 40% 60%;
            background: linear-gradient(135deg, #f093fb, #667eea);
            animation: spin 19s linear infinite;
        }
        .pentagon {
            width: 70px;
            height: 70px;
            background: linear-gradient(135deg, #f5576c, #f093fb);
            clip-path: polygon(50% 0%, 100% 38%, 82% 100%, 18% 100%, 0% 38%);
            animation: spin-rev 17s linear infinite;
        }
        .star {
            width: 70px;
            height: 70px;
            background: linear-gradient(135deg, #764ba2, #f5576c);
            clip-path: polygon(50% 0%, 61% 35%, 98% 35%, 68% 57%, 79% 91%, 50% 70%, 21% 91%, 32% 57%, 2% 35%, 39% 35%);
            animation: spin 21s linear infinite;
        }
        .circle.small {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: linear-gradient(135deg, #f5576c, #f093fb);
            animation: spin 10s linear infinite;
            opacity: 0.13;
        }
        .square.small {
            width: 35px;
            height: 35px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            border-radius: 8px;
            animation: spin-rev 14s linear infinite;
            opacity: 0.13;
        }
        .triangle.small {
            width: 0;
            height: 0;
            border-left: 20px solid transparent;
            border-right: 20px solid transparent;
            border-bottom: 35px solid #764ba2;
            animation: spin 20s linear infinite;
            opacity: 0.13;
        }
        .circle.tiny {
            width: 22px;
            height: 22px;
            border-radius: 50%;
            background: linear-gradient(135deg, #f093fb, #764ba2);
            animation: spin 13s linear infinite;
            opacity: 0.10;
        }
        .square.tiny {
            width: 15px;
            height: 15px;
            background: linear-gradient(135deg, #667eea, #f5576c);
            border-radius: 4px;
            animation: spin-rev 11s linear infinite;
            opacity: 0.10;
        }
        .triangle.tiny {
            width: 0;
            height: 0;
            border-left: 8px solid transparent;
            border-right: 8px solid transparent;
            border-bottom: 14px solid #f093fb;
            animation: spin 15s linear infinite;
            opacity: 0.10;
        }
        @keyframes spin {
            0% { transform: rotate(0deg) scale(1) translateY(0); }
            50% { transform: rotate(180deg) scale(1.1) translateY(20px); }
            100% { transform: rotate(360deg) scale(1) translateY(0); }
        }
        @keyframes spin-rev {
            0% { transform: rotate(0deg) scale(1) translateX(0); }
            50% { transform: rotate(-180deg) scale(1.08) translateX(20px); }
            100% { transform: rotate(-360deg) scale(1) translateX(0); }
        }

        /* Aurora Flow Animated Background */
        .aurora-bg {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            z-index: 1;
            overflow: hidden;
            pointer-events: none;
            background: linear-gradient(120deg, #232946 0%, #3e497a 100%);
        }
        .aurora-bg::before, .aurora-bg::after {
            content: '';
            position: absolute;
            width: 120vw;
            height: 120vh;
            left: -10vw;
            top: -10vh;
            z-index: 2;
            pointer-events: none;
            opacity: 0.7;
            filter: blur(60px);
            background: radial-gradient(circle at 30% 40%, #a0e9ff 0%, transparent 70%),
                        radial-gradient(circle at 70% 60%, #f093fb 0%, transparent 70%),
                        radial-gradient(circle at 60% 20%, #f5576c 0%, transparent 70%),
                        radial-gradient(circle at 80% 80%, #667eea 0%, transparent 70%);
            animation: aurora-move 18s ease-in-out infinite alternate;
        }
        .aurora-bg::after {
            opacity: 0.5;
            filter: blur(90px);
            background: radial-gradient(circle at 60% 70%, #f093fb 0%, transparent 70%),
                        radial-gradient(circle at 20% 30%, #a0e9ff 0%, transparent 70%),
                        radial-gradient(circle at 80% 30%, #f5576c 0%, transparent 70%),
                        radial-gradient(circle at 40% 80%, #667eea 0%, transparent 70%);
            animation: aurora-move2 22s ease-in-out infinite alternate;
        }
        @keyframes aurora-move {
            0% { transform: translateY(0) scale(1) rotate(0deg); }
            50% { transform: translateY(-40px) scale(1.05) rotate(2deg); }
            100% { transform: translateY(40px) scale(1) rotate(-2deg); }
        }
        @keyframes aurora-move2 {
            0% { transform: translateX(0) scale(1) rotate(0deg); }
            50% { transform: translateX(40px) scale(1.08) rotate(-2deg); }
            100% { transform: translateX(-40px) scale(1) rotate(2deg); }
        }
        @media (max-width: 600px) {
            .aurora-bg::before, .aurora-bg::after {
                filter: blur(40px);
                opacity: 0.5;
            }
        }

        /* Flying Dots Grid Overlay */
        .dots-grid {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            z-index: 2;
            pointer-events: none;
            display: grid;
            grid-template-columns: repeat(10, 1fr);
            grid-template-rows: repeat(6, 1fr);
            gap: 0;
        }
        .dot {
            width: 14px;
            height: 14px;
            border-radius: 50%;
            background: radial-gradient(circle at 40% 60%, #fff 70%, #a0e9ff 100%);
            box-shadow:
                0 0 12px 2px #a0e9ff,
                0 0 24px 6px #f093fb,
                0 0 36px 12px #667eea;
            margin: auto;
            opacity: 0.85;
            animation: dot-float 6s ease-in-out infinite, dot-color 8s linear infinite;
            animation-delay: calc(var(--i) * 0.13s);
            will-change: transform, opacity, background;
            transition: background 0.5s, box-shadow 0.5s;
        }
        .dot:nth-child(3n) {
            width: 11px;
            height: 11px;
            box-shadow:
                0 0 8px 1px #f093fb,
                0 0 18px 4px #a0e9ff,
                0 0 28px 8px #f5576c;
            opacity: 0.7;
        }
        .dot:nth-child(4n) {
            width: 17px;
            height: 17px;
            box-shadow:
                0 0 16px 3px #667eea,
                0 0 32px 10px #a0e9ff,
                0 0 48px 16px #f093fb;
            opacity: 0.95;
        }
        .dot:nth-child(5n) {
            background: radial-gradient(circle at 60% 40%, #fff 60%, #f5576c 100%);
        }
        @keyframes dot-float {
            0% { transform: translateY(0) scale(1); opacity: 0.85; }
            20% { transform: translateY(-10px) scale(1.1); opacity: 1; }
            50% { transform: translateY(10px) scale(0.95); opacity: 0.7; }
            80% { transform: translateY(-10px) scale(1.05); opacity: 0.9; }
            100% { transform: translateY(0) scale(1); opacity: 0.85; }
        }
        @keyframes dot-color {
            0% { filter: hue-rotate(0deg) brightness(1); }
            25% { filter: hue-rotate(30deg) brightness(1.1); }
            50% { filter: hue-rotate(60deg) brightness(1.2); }
            75% { filter: hue-rotate(30deg) brightness(1.1); }
            100% { filter: hue-rotate(0deg) brightness(1); }
        }
        @media (max-width: 600px) {
            .dots-grid {
                grid-template-columns: repeat(6, 1fr);
                grid-template-rows: repeat(4, 1fr);
            }
            .dot {
                width: 8px;
                height: 8px;
            }
            .dot:nth-child(3n) { width: 6px; height: 6px; }
            .dot:nth-child(4n) { width: 10px; height: 10px; }
        }

        /* Beautiful Loading Overlay */
        .loading-overlay {
            position: fixed;
            top: 0; left: 0;
            width: 100vw; height: 100vh;
            z-index: 1000;
            background: rgba(30, 34, 54, 0.45);
            backdrop-filter: blur(6px);
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.4s;
        }
        .loading-overlay.active {
            opacity: 1;
            pointer-events: all;
        }
        .aurora-loader {
            position: relative;
            width: 90px;
            height: 90px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .loader-dot {
            position: absolute;
            width: 18px;
            height: 18px;
            border-radius: 50%;
            background: radial-gradient(circle at 60% 40%, #a0e9ff 60%, #f093fb 100%);
            box-shadow: 0 0 16px 4px #a0e9ff, 0 0 32px 8px #f093fb;
            opacity: 0.85;
            animation: loader-dot-float 1.6s cubic-bezier(0.68,-0.55,0.27,1.55) infinite;
            animation-delay: calc(var(--d) * 0.18s);
            filter: blur(0.5px) drop-shadow(0 0 8px #f093fb);
        }
        @keyframes loader-dot-float {
            0%, 100% { transform: scale(1) translateY(0); opacity: 0.85; }
            30% { transform: scale(1.2) translateY(-18px); opacity: 1; }
            60% { transform: scale(0.9) translateY(8px); opacity: 0.7; }
        }
        .loader-center {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: conic-gradient(from 0deg, #a0e9ff, #f093fb, #f5576c, #667eea, #a0e9ff);
            box-shadow: 0 0 32px 8px #a0e9ff, 0 0 48px 16px #f093fb;
            animation: loader-center-spin 1.8s linear infinite;
            filter: blur(0.5px);
            opacity: 0.92;
        }
        @keyframes loader-center-spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        .password-group {
            position: relative;
        }
        .toggle-password, .toggle-password i { display: none !important; }
        .toggle-password-modern {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: rgba(255,255,255,0.08);
            border: none;
            outline: none;
            cursor: pointer;
            z-index: 2;
            color: #fff;
            font-size: 1.2rem;
            padding: 0;
            width: 34px;
            height: 34px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 8px 0 rgba(102,126,234,0.10);
            transition: background 0.2s, box-shadow 0.2s;
        }
        .toggle-password-modern:focus, .toggle-password-modern:hover {
            background: rgba(160,233,255,0.18);
            box-shadow: 0 4px 16px 0 rgba(160,233,255,0.18);
        }
        .toggle-password-modern .icon-eye {
            position: absolute;
            left: 0; right: 0; top: 0; bottom: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.25s;
        }
        .toggle-password-modern .eye-visible { opacity: 1; }
        .toggle-password-modern.showing .eye-visible { opacity: 0; }
        .toggle-password-modern .eye-hidden { opacity: 0; }
        .toggle-password-modern.showing .eye-hidden { opacity: 1; }
        .custom-tooltip {
            position: absolute;
            bottom: 120%;
            left: 50%;
            transform: translateX(-50%) scale(0.95);
            background: rgba(30,34,54,0.92);
            color: #fff;
            padding: 5px 12px;
            border-radius: 8px;
            font-size: 0.85rem;
            white-space: nowrap;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.18s, transform 0.18s;
            box-shadow: 0 2px 8px 0 rgba(102,126,234,0.10);
            z-index: 10;
        }
        .toggle-password-modern:focus .custom-tooltip,
        .toggle-password-modern:hover .custom-tooltip {
            opacity: 1;
            transform: translateX(-50%) scale(1);
        }
    </style>
</head>
<body>
    <!-- Aurora Flow Animated Background -->
    <div class="aurora-bg"></div>
    <!-- Flying Dots Grid Overlay -->
    <div class="dots-grid">
        <!-- 10x6 grid of dots (60 dots) -->
        <div class="dot" style="--i:0;"></div><div class="dot" style="--i:1;"></div><div class="dot" style="--i:2;"></div><div class="dot" style="--i:3;"></div><div class="dot" style="--i:4;"></div><div class="dot" style="--i:5;"></div><div class="dot" style="--i:6;"></div><div class="dot" style="--i:7;"></div><div class="dot" style="--i:8;"></div><div class="dot" style="--i:9;"></div>
        <div class="dot" style="--i:10;"></div><div class="dot" style="--i:11;"></div><div class="dot" style="--i:12;"></div><div class="dot" style="--i:13;"></div><div class="dot" style="--i:14;"></div><div class="dot" style="--i:15;"></div><div class="dot" style="--i:16;"></div><div class="dot" style="--i:17;"></div><div class="dot" style="--i:18;"></div><div class="dot" style="--i:19;"></div>
        <div class="dot" style="--i:20;"></div><div class="dot" style="--i:21;"></div><div class="dot" style="--i:22;"></div><div class="dot" style="--i:23;"></div><div class="dot" style="--i:24;"></div><div class="dot" style="--i:25;"></div><div class="dot" style="--i:26;"></div><div class="dot" style="--i:27;"></div><div class="dot" style="--i:28;"></div><div class="dot" style="--i:29;"></div>
        <div class="dot" style="--i:30;"></div><div class="dot" style="--i:31;"></div><div class="dot" style="--i:32;"></div><div class="dot" style="--i:33;"></div><div class="dot" style="--i:34;"></div><div class="dot" style="--i:35;"></div><div class="dot" style="--i:36;"></div><div class="dot" style="--i:37;"></div><div class="dot" style="--i:38;"></div><div class="dot" style="--i:39;"></div>
        <div class="dot" style="--i:40;"></div><div class="dot" style="--i:41;"></div><div class="dot" style="--i:42;"></div><div class="dot" style="--i:43;"></div><div class="dot" style="--i:44;"></div><div class="dot" style="--i:45;"></div><div class="dot" style="--i:46;"></div><div class="dot" style="--i:47;"></div><div class="dot" style="--i:48;"></div><div class="dot" style="--i:49;"></div>
        <div class="dot" style="--i:50;"></div><div class="dot" style="--i:51;"></div><div class="dot" style="--i:52;"></div><div class="dot" style="--i:53;"></div><div class="dot" style="--i:54;"></div><div class="dot" style="--i:55;"></div><div class="dot" style="--i:56;"></div><div class="dot" style="--i:57;"></div><div class="dot" style="--i:58;"></div><div class="dot" style="--i:59;"></div>
    </div>
    <!-- Beautiful Loading Overlay -->
    <div class="loading-overlay" id="loadingOverlay" aria-hidden="true">
        <div class="aurora-loader">
            <span class="loader-dot" style="--d:0;"></span>
            <span class="loader-dot" style="--d:1;"></span>
            <span class="loader-dot" style="--d:2;"></span>
            <span class="loader-dot" style="--d:3;"></span>
            <span class="loader-dot" style="--d:4;"></span>
            <span class="loader-dot" style="--d:5;"></span>
            <div class="loader-center"></div>
        </div>
    </div>
    
    <div class="login-container">
        <div class="login-glass">
            <div class="login-logo">
                <div class="logo-container">
                    <img src="pos_logo.png" alt="SmartPOS Logo">
                </div>
                <div class="brand-name">SmartPOS</div>
                <div class="brand-subtitle">Point of Sale System</div>
            </div>
            
            <form class="login-form" method="POST" autocomplete="off">
                <div class="input-group">
                    <input type="text" id="username" name="username" placeholder="Enter your username" 
                           value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>" 
                            autocomplete="username" autofocus>
                    <i class="fas fa-user"></i>
                </div>
                
                <div class="input-group password-group">
                    <input type="password" id="password" name="password" placeholder="Enter your password" autocomplete="current-password">
                    <i class="fas fa-lock"></i>
                    <button type="button" class="toggle-password-modern" tabindex="0" aria-label="Show password">
                        <span class="icon-eye eye-visible"><i class="fas fa-eye"></i></span>
                        <span class="icon-eye eye-hidden"><i class="fas fa-eye-slash"></i></span>
                        <span class="custom-tooltip" role="tooltip">Show Password</span>
                    </button>
                </div>
                
                <button type="submit" class="login-btn">
                    <i class="fas fa-sign-in-alt"></i> Sign In
                </button>
            </form>
            
            <div class="login-footer">
                <small><strong>Default Admin:</strong> admin / admin123</small>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <?php if ($error): ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                title: '⚠️ Login Error',
                text: '<?php echo addslashes($error); ?>',
                icon: 'error',
                confirmButtonText: 'Try Again',
                confirmButtonColor: '#dc3545',
                background: 'rgba(255, 255, 255, 0.1)',
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
    
    <script>
        // Enhanced form validation with SweetAlert
        document.querySelector('form').addEventListener('submit', function(e) {
            const username = document.getElementById('username').value.trim();
            const password = document.getElementById('password').value.trim();
            const submitBtn = document.querySelector('.login-btn');
            
            if (!username || !password) {
                e.preventDefault();
                Swal.fire({
                    title: '⚠️ Missing Information',
                    text: 'Please enter both username and password.',
                    icon: 'warning',
                    confirmButtonText: 'Got it!',
                    confirmButtonColor: '#ffc107',
                    background: 'rgba(37, 126, 242, 0.81)',
                    backdrop: 'rgba(0, 0, 0, 0.4)',
                    customClass: {
                        popup: 'animated fadeInUp',
                        title: 'text-white',
                        content: 'text-white'
                    }
                });
                return false;
            }
            
            // Add loading state to button
            submitBtn.classList.add('loading');
            submitBtn.innerHTML = '';
            
            Swal.fire({
                title: '🔐 Authenticating...',
                text: 'Please wait while we verify your credentials.',
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                },
                background: 'rgba(255, 255, 255, 0.1)',
                backdrop: 'rgba(0, 0, 0, 0.4)',
                customClass: {
                    popup: 'animated fadeInUp',
                    title: 'text-white',
                    content: 'text-white'
                }
            });
        });
        
        // Enhanced welcome message
        document.addEventListener('DOMContentLoaded', function() {
            if (!localStorage.getItem('loginWelcomeShown')) {
                setTimeout(() => {
                    Swal.fire({
                        title: '👋 Welcome to SmartPOS!',
                        text: 'Please sign in to access your dashboard.',
                        icon: 'info',
                        confirmButtonText: 'Let\'s Go!',
                        confirmButtonColor: '#667eea',
                        background: 'rgba(255, 255, 255, 0.1)',
                        backdrop: 'rgba(0, 0, 0, 0.4)',
                        customClass: {
                            popup: 'animated fadeInUp',
                            title: 'text-white',
                            content: 'text-white'
                        }
                    });
                    localStorage.setItem('loginWelcomeShown', 'true');
                }, 800);
            }
        });
        
        // Add input focus animations
        document.querySelectorAll('input').forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.style.transform = 'scale(1.02)';
            });
            
            input.addEventListener('blur', function() {
                this.parentElement.style.transform = 'scale(1)';
            });
        });

        // Show beautiful loading overlay on form submit
        const loginForm = document.querySelector('form');
        const loadingOverlay = document.getElementById('loadingOverlay');
        if (loginForm && loadingOverlay) {
            loginForm.addEventListener('submit', function(e) {
                // Only show if form is valid (not blocked by JS validation)
                setTimeout(() => {
                    loadingOverlay.classList.add('active');
                    loadingOverlay.setAttribute('aria-hidden', 'false');
                }, 10);
            });
        }
        // Hide overlay on page load (in case of back navigation)
        window.addEventListener('pageshow', function() {
            if (loadingOverlay) {
                loadingOverlay.classList.remove('active');
                loadingOverlay.setAttribute('aria-hidden', 'true');
            }
        });

        // Modern show/hide password toggle with custom tooltip and smooth icon transition
        const togglePasswordModern = document.querySelector('.toggle-password-modern');
        const passwordInputModern = document.getElementById('password');
        if (togglePasswordModern && passwordInputModern) {
            togglePasswordModern.addEventListener('click', function() {
                const isPassword = passwordInputModern.type === 'password';
                passwordInputModern.type = isPassword ? 'text' : 'password';
                this.classList.toggle('showing', isPassword);
                // Update tooltip text
                const tooltip = this.querySelector('.custom-tooltip');
                tooltip.textContent = isPassword ? 'Hide Password' : 'Show Password';
                this.setAttribute('aria-label', isPassword ? 'Hide password' : 'Show password');
            });
            // Keyboard accessibility
            togglePasswordModern.addEventListener('keydown', function(e) {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    this.click();
                }
            });
        }
    </script>
</body>
</html> 