<?php
// Enhanced cache control
header("Expires: Tue, 01 Jan 2000 00:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Regenerate session ID for security
session_regenerate_id(true);

require __DIR__ . '/../vendor/autoload.php';
use Lourdian\BasicStudent\Model\Auth;

 $auth = new Auth();
 $error = '';

// Check if already logged in
if (isset($_SESSION['id'], $_SESSION['role'])) {
    if ($_SESSION['role'] === 'admin') {
        header("Location: admin_dashboard.php");
        exit;
    } else {
        header("Location: dashboard.php");
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    
    if (empty($username) || empty($password)) {
        $error = "Please enter both username and password.";
    } else {
        $user = $auth->login($username, $password);
        if ($user) {
            // Set session - FIXED: Consistent session variables
            $_SESSION['id'] = $user['id'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['fullname'] = $user['fullname'] ?? '';
            $_SESSION['username'] = $user['username'];
            
            // Regenerate session ID after login
            session_regenerate_id(true);

            // Redirect to correct dashboard
            if ($user['role'] === 'admin') {
                header("Location: admin_dashboard.php");
                exit;
            } else {
                header("Location: dashboard.php");
                exit;
            }
        } else {
            $error = "Invalid username or password.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login - Portal</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<style>
:root {
    --primary-color: #6366f1;
    --secondary-color: #8b5cf6;
    --accent-color: #ec4899;
    --success-color: #10b981;
    --error-color: #ef4444;
    --warning-color: #f59e0b;
    --text-primary: #1f2937;
    --text-secondary: #6b7280;
    --bg-primary: #ffffff;
    --bg-secondary: #f9fafb;
    --border-color: #e5e7eb;
    --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
    --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: 'Poppins', sans-serif;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 20px;
    position: relative;
    overflow: hidden;
}

/* Animated background elements */
.bg-animation {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    overflow: hidden;
    z-index: 0;
}

.bg-circle {
    position: absolute;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.1);
    animation: float 20s infinite ease-in-out;
}

.bg-circle:nth-child(1) {
    width: 80px;
    height: 80px;
    top: 20%;
    left: 10%;
    animation-delay: 0s;
    animation-duration: 25s;
}

.bg-circle:nth-child(2) {
    width: 120px;
    height: 120px;
    top: 60%;
    left: 80%;
    animation-delay: 2s;
    animation-duration: 20s;
}

.bg-circle:nth-child(3) {
    width: 60px;
    height: 60px;
    top: 80%;
    left: 20%;
    animation-delay: 4s;
    animation-duration: 15s;
}

.bg-circle:nth-child(4) {
    width: 100px;
    height: 100px;
    top: 10%;
    left: 60%;
    animation-delay: 6s;
    animation-duration: 30s;
}

@keyframes float {
    0%, 100% {
        transform: translateY(0) rotate(0deg);
        opacity: 0.5;
    }
    50% {
        transform: translateY(-20px) rotate(180deg);
        opacity: 0.8;
    }
}

.login-container {
    width: 100%;
    max-width: 450px;
    position: relative;
    z-index: 1;
}

.login-card {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
    border-radius: 24px;
    box-shadow: var(--shadow-xl);
    overflow: hidden;
    animation: fadeInUp 0.8s ease-out;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.login-header {
    background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
    padding: 40px 30px;
    text-align: center;
    position: relative;
    overflow: hidden;
}

.login-header::before {
    content: "";
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
    animation: rotate 30s linear infinite;
}

@keyframes rotate {
    from {
        transform: rotate(0deg);
    }
    to {
        transform: rotate(360deg);
    }
}

.login-icon {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.2);
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 20px;
    position: relative;
    z-index: 1;
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
}

.login-icon i {
    font-size: 36px;
    color: white;
}

.login-header h2 {
    color: white;
    font-weight: 600;
    margin-bottom: 8px;
    font-size: 24px;
    position: relative;
    z-index: 1;
}

.login-header p {
    color: rgba(255, 255, 255, 0.9);
    margin-bottom: 0;
    font-size: 16px;
    position: relative;
    z-index: 1;
}

.login-body {
    padding: 40px 30px;
}

.form-group {
    margin-bottom: 24px;
    position: relative;
}

.form-label {
    display: block;
    margin-bottom: 8px;
    font-weight: 500;
    color: var(--text-primary);
    font-size: 14px;
}

.form-control {
    width: 100%;
    padding: 14px 16px;
    border: 2px solid var(--border-color);
    border-radius: 12px;
    font-size: 16px;
    transition: var(--transition);
    background-color: var(--bg-secondary);
}

.form-control:focus {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
    outline: none;
    background-color: var(--bg-primary);
}

.input-icon {
    position: absolute;
    right: 16px;
    top: 42px;
    color: var(--text-secondary);
    font-size: 18px;
    pointer-events: none;
    transition: var(--transition);
}

.form-control:focus ~ .input-icon {
    color: var(--primary-color);
}

.btn-login {
    width: 100%;
    padding: 14px 20px;
    background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
    color: white;
    border: none;
    border-radius: 12px;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    transition: var(--transition);
    position: relative;
    overflow: hidden;
    margin-top: 10px;
}

.btn-login::before {
    content: "";
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
    transition: left 0.5s;
}

.btn-login:hover::before {
    left: 100%;
}

.btn-login:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 20px rgba(99, 102, 241, 0.3);
}

.btn-login:active {
    transform: translateY(0);
}

.btn-login:disabled {
    opacity: 0.7;
    cursor: not-allowed;
    transform: none;
}

.error-message {
    background-color: #fef2f2;
    border: 1px solid #fecaca;
    color: var(--error-color);
    padding: 12px 16px;
    border-radius: 12px;
    margin-bottom: 24px;
    display: flex;
    align-items: center;
    font-size: 14px;
    animation: shake 0.5s ease-in-out;
}

.error-message i {
    margin-right: 10px;
    font-size: 18px;
}

@keyframes shake {
    0%, 100% { transform: translateX(0); }
    25% { transform: translateX(-5px); }
    75% { transform: translateX(5px); }
}

.forgot-password {
    display: block;
    text-align: right;
    margin-top: -10px;
    margin-bottom: 20px;
    color: var(--primary-color);
    text-decoration: none;
    font-size: 14px;
    font-weight: 500;
    transition: var(--transition);
}

.forgot-password:hover {
    color: var(--secondary-color);
    text-decoration: underline;
}

.divider {
    display: flex;
    align-items: center;
    margin: 30px 0;
}

.divider::before,
.divider::after {
    content: "";
    flex: 1;
    height: 1px;
    background: var(--border-color);
}

.divider span {
    padding: 0 15px;
    color: var(--text-secondary);
    font-size: 14px;
    font-weight: 500;
}

.social-login {
    display: flex;
    gap: 15px;
    margin-bottom: 24px;
}

.social-btn {
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 12px;
    border-radius: 12px;
    border: 2px solid var(--border-color);
    background: var(--bg-primary);
    color: var(--text-primary);
    text-decoration: none;
    font-weight: 500;
    font-size: 14px;
    transition: var(--transition);
}

.social-btn:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-md);
    border-color: var(--primary-color);
    color: var(--primary-color);
}

.social-btn i {
    margin-right: 8px;
    font-size: 18px;
}

.register-link {
    display: block;
    text-align: center;
    color: var(--text-secondary);
    font-size: 14px;
    margin-top: 20px;
}

.register-link a {
    color: var(--primary-color);
    text-decoration: none;
    font-weight: 600;
    transition: var(--transition);
}

.register-link a:hover {
    color: var(--secondary-color);
    text-decoration: underline;
}

/* Loading spinner */
.spinner-border-sm {
    width: 1rem;
    height: 1rem;
    border-width: 0.2em;
}

/* Responsive adjustments */
@media (max-width: 576px) {
    .login-header {
        padding: 30px 20px;
    }
    
    .login-body {
        padding: 30px 20px;
    }
    
    .login-header h2 {
        font-size: 22px;
    }
    
    .login-header p {
        font-size: 14px;
    }
    
    .form-control {
        padding: 12px 14px;
        font-size: 15px;
    }
    
    .btn-login {
        padding: 12px 16px;
        font-size: 15px;
    }
}
</style>
</head>
<body>
<!-- Animated background elements -->
<div class="bg-animation">
    <div class="bg-circle"></div>
    <div class="bg-circle"></div>
    <div class="bg-circle"></div>
    <div class="bg-circle"></div>
</div>

<div class="login-container">
    <div class="login-card">
        <div class="login-header">
            <div class="login-icon">
                <i class="bi bi-shield-lock-fill"></i>
            </div>
            <h2>Portal Login</h2>
            <p>Welcome back! Please sign in to continue</p>
        </div>
        
        <div class="login-body">
            <?php if (!empty($error)): ?>
                <div class="error-message">
                    <i class="bi bi-exclamation-circle-fill"></i>
                    <span><?= htmlspecialchars($error) ?></span>
                </div>
            <?php endif; ?>
            
            <form method="POST" id="loginForm">
                <div class="form-group">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" class="form-control" id="username" name="username" placeholder="Enter your username" required value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">
                    <i class="bi bi-person-fill input-icon"></i>
                </div>
                
                <div class="form-group">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" name="password" placeholder="Enter your password" required>
                    <i class="bi bi-lock-fill input-icon"></i>
                </div>
                                
                <button type="submit" class="btn btn-login" id="loginBtn">
                    Sign In
                </button>
            </form>
            
            <div class="divider">
                <span>OR</span>
            </div>
            
            
            <div class="register-link">
                Don't have an account? <a href="register.php">Register</a>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Form submission handling
    const loginForm = document.getElementById('loginForm');
    const loginBtn = document.getElementById('loginBtn');
    
    loginForm.addEventListener('submit', function(e) {
        // Add loading state
        loginBtn.disabled = true;
        loginBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Signing in...';
        
        // Re-enable after 3 seconds as a fallback
        setTimeout(() => {
            loginBtn.disabled = false;
            loginBtn.innerHTML = 'Sign In';
        }, 3000);
    });
    
    // Add focus effects to form inputs
    const formControls = document.querySelectorAll('.form-control');
    
    formControls.forEach(control => {
        control.addEventListener('focus', function() {
            this.parentElement.classList.add('focused');
        });
        
        control.addEventListener('blur', function() {
            if (!this.value) {
                this.parentElement.classList.remove('focused');
            }
        });
        
        // Check if input has value on page load
        if (control.value) {
            this.parentElement.classList.add('focused');
        }
    });
    
    // Add ripple effect to button
    loginBtn.addEventListener('click', function(e) {
        const ripple = document.createElement('span');
        const rect = this.getBoundingClientRect();
        const size = Math.max(rect.width, rect.height);
        const x = e.clientX - rect.left - size / 2;
        const y = e.clientY - rect.top - size / 2;
        
        ripple.style.width = ripple.style.height = size + 'px';
        ripple.style.left = x + 'px';
        ripple.style.top = y + 'px';
        ripple.style.position = 'absolute';
        ripple.style.borderRadius = '50%';
        ripple.style.backgroundColor = 'rgba(255, 255, 255, 0.5)';
        ripple.style.transform = 'scale(0)';
        ripple.style.animation = 'ripple-animation 0.6s ease-out';
        ripple.style.pointerEvents = 'none';
        
        this.appendChild(ripple);
        
        setTimeout(() => {
            ripple.remove();
        }, 600);
    });
});

// Add ripple animation
const style = document.createElement('style');
style.textContent = `
    @keyframes ripple-animation {
        to {
            transform: scale(4);
            opacity: 0;
        }
    }
    
    .btn-login {
        position: relative;
        overflow: hidden;
    }
`;
document.head.appendChild(style);
</script>
</body>
</html>