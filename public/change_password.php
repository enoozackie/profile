<?php
// Prevent caching
header("Expires: Tue, 01 Jan 2000 00:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// Start session and check user
session_start();
if (!isset($_SESSION['id']) || $_SESSION['role'] !== 'user') {
    header("Location: login.php");
    exit;
}

require __DIR__ . '/../vendor/autoload.php';
use Lourdian\BasicStudent\Model\User;

 $userModel = new User();
 $success = '';
 $error = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $currentPassword = $_POST['current_password'] ?? '';
    $newPassword = $_POST['new_password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    $user = $userModel->getById($_SESSION['id']);

    if (!$user) {
        $error = "User not found.";
    } elseif (!password_verify($currentPassword, $user['password'])) {
        $error = "Current password is incorrect.";
    } elseif ($newPassword !== $confirmPassword) {
        $error = "New password and confirmation do not match.";
    } else {
        // Update password using dedicated method
        if ($userModel->updatePassword($_SESSION['id'], $newPassword)) {
            // Refresh session data
            $updatedUser = $userModel->getById($_SESSION['id']);
            $_SESSION['fullname'] = $updatedUser['fullname'];
            $_SESSION['username'] = $updatedUser['username'];
            $_SESSION['role'] = $updatedUser['role'];

            // Redirect to dashboard with success
            $_SESSION['message'] = "Password changed successfully.";
            header("Location: dashboard.php");
            exit;
        } else {
            $error = "Failed to change password.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Change Password - Student</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
<style>
    :root {
        --primary-color: #4361ee;
        --secondary-color: #3f37c9;
        --accent-color: #f72585;
        --success-color: #06ffa5;
        --error-color: #ff006e;
        --warning-color: #ffbe0b;
        --light-bg: #f8f9fa;
        --card-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    }
    
    body {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        min-height: 100vh;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        padding-top: 20px;
        padding-bottom: 20px;
    }
    
    .password-container {
        max-width: 600px;
        margin: 0 auto;
    }
    
    .page-header {
        background: white;
        border-radius: 15px;
        padding: 30px;
        margin-bottom: 25px;
        box-shadow: var(--card-shadow);
        text-align: center;
        position: relative;
        overflow: hidden;
    }
    
    .page-header::before {
        content: "";
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 100px;
        background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
        z-index: 0;
    }
    
    .header-content {
        position: relative;
        z-index: 1;
    }
    
    .header-icon {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        background: white;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 15px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }
    
    .header-icon i {
        font-size: 36px;
        color: var(--primary-color);
    }
    
    .page-header h2 {
        margin: 0;
        color: #333;
        font-weight: 600;
    }
    
    .page-header p {
        margin: 5px 0 0;
        color: #666;
    }
    
    .form-card {
        background: white;
        border-radius: 15px;
        padding: 30px;
        box-shadow: var(--card-shadow);
        margin-bottom: 25px;
    }
    
    .form-group-custom {
        margin-bottom: 25px;
        position: relative;
    }
    
    .form-label-custom {
        font-weight: 600;
        color: #555;
        margin-bottom: 8px;
        display: flex;
        align-items: center;
    }
    
    .form-label-custom i {
        margin-right: 8px;
        color: var(--primary-color);
    }
    
    .password-input-wrapper {
        position: relative;
    }
    
    .form-control-custom {
        border: 2px solid #e0e0e0;
        border-radius: 10px;
        padding: 12px 45px 12px 15px;
        font-size: 16px;
        transition: all 0.3s ease;
        width: 100%;
    }
    
    .form-control-custom:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 0.2rem rgba(67, 97, 238, 0.25);
        outline: none;
    }
    
    .password-toggle {
        position: absolute;
        right: 15px;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        color: #666;
        cursor: pointer;
        font-size: 18px;
    }
    
    .password-toggle:hover {
        color: var(--primary-color);
    }
    
    .password-strength {
        height: 5px;
        border-radius: 5px;
        margin-top: 8px;
        background-color: #e0e0e0;
        overflow: hidden;
    }
    
    .password-strength-bar {
        height: 100%;
        width: 0;
        transition: width 0.3s ease, background-color 0.3s ease;
    }
    
    .strength-weak {
        width: 33%;
        background-color: var(--error-color);
    }
    
    .strength-medium {
        width: 66%;
        background-color: var(--warning-color);
    }
    
    .strength-strong {
        width: 100%;
        background-color: var(--success-color);
    }
    
    .password-requirements {
        margin-top: 10px;
        font-size: 14px;
        color: #666;
    }
    
    .requirement {
        display: flex;
        align-items: center;
        margin-bottom: 5px;
    }
    
    .requirement i {
        margin-right: 5px;
        font-size: 14px;
    }
    
    .requirement.met {
        color: var(--success-color);
    }
    
    .requirement.unmet {
        color: #999;
    }
    
    .alert-custom {
        border-radius: 10px;
        padding: 15px 20px;
        margin-bottom: 25px;
        border: none;
        display: flex;
        align-items: center;
        animation: slideDown 0.3s ease;
    }
    
    .alert-success-custom {
        background: linear-gradient(135deg, #d4fc79 0%, #96e6a1 100%);
        color: #0a5f0a;
    }
    
    .alert-danger-custom {
        background: linear-gradient(135deg, #feb692 0%, #ea5455 100%);
        color: white;
    }
    
    .alert-custom i {
        margin-right: 10px;
        font-size: 20px;
    }
    
    .action-buttons {
        display: flex;
        gap: 15px;
        flex-wrap: wrap;
        margin-top: 30px;
    }
    
    .btn-custom {
        padding: 12px 30px;
        border-radius: 50px;
        font-weight: 500;
        transition: all 0.3s ease;
        border: none;
        display: inline-flex;
        align-items: center;
        text-decoration: none;
        font-size: 16px;
    }
    
    .btn-custom i {
        margin-right: 8px;
    }
    
    .btn-warning-custom {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        color: white;
    }
    
    .btn-warning-custom:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 20px rgba(245, 87, 108, 0.3);
        color: white;
    }
    
    .btn-secondary-custom {
        background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%);
        color: #555;
    }
    
    .btn-secondary-custom:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 20px rgba(168, 237, 234, 0.3);
        color: #555;
    }
    
    .security-tips {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border-radius: 10px;
        padding: 15px;
        margin-top: 20px;
    }
    
    .security-tips h5 {
        color: var(--primary-color);
        margin-bottom: 10px;
        display: flex;
        align-items: center;
    }
    
    .security-tips h5 i {
        margin-right: 8px;
    }
    
    .security-tips ul {
        margin-bottom: 0;
        padding-left: 20px;
    }
    
    .security-tips li {
        margin-bottom: 5px;
    }
    
    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    @media (max-width: 768px) {
        .action-buttons {
            flex-direction: column;
        }
        
        .btn-custom {
            width: 100%;
            justify-content: center;
        }
    }
</style>
</head>
<body>
<div class="container password-container">
    <div class="page-header">
        <div class="header-content">
            <div class="header-icon">
                <i class="bi bi-shield-lock"></i>
            </div>
            <h2>Change Password</h2>
            <p>Update your account security</p>
        </div>
    </div>

    <?php if ($success): ?>
        <div class="alert alert-custom alert-success-custom">
            <i class="bi bi-check-circle-fill"></i>
            <?= htmlspecialchars($success) ?>
        </div>
    <?php elseif ($error): ?>
        <div class="alert alert-custom alert-danger-custom">
            <i class="bi bi-exclamation-triangle-fill"></i>
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <div class="form-card">
        <form method="POST" id="changePasswordForm">
            <div class="form-group-custom">
                <label class="form-label-custom">
                    <i class="bi bi-key"></i> Current Password
                </label>
                <div class="password-input-wrapper">
                    <input type="password" name="current_password" class="form-control-custom" id="currentPassword" required>
                    <button type="button" class="password-toggle" data-target="currentPassword">
                        <i class="bi bi-eye"></i>
                    </button>
                </div>
            </div>
            
            <div class="form-group-custom">
                <label class="form-label-custom">
                    <i class="bi bi-lock"></i> New Password
                </label>
                <div class="password-input-wrapper">
                    <input type="password" name="new_password" class="form-control-custom" id="newPassword" required>
                    <button type="button" class="password-toggle" data-target="newPassword">
                        <i class="bi bi-eye"></i>
                    </button>
                </div>
                <div class="password-strength">
                    <div class="password-strength-bar" id="passwordStrength"></div>
                </div>
                <div class="password-requirements">
                    <div class="requirement unmet" id="lengthReq">
                        <i class="bi bi-circle"></i> At least 8 characters
                    </div>
                    <div class="requirement unmet" id="upperReq">
                        <i class="bi bi-circle"></i> One uppercase letter
                    </div>
                    <div class="requirement unmet" id="lowerReq">
                        <i class="bi bi-circle"></i> One lowercase letter
                    </div>
                    <div class="requirement unmet" id="numberReq">
                        <i class="bi bi-circle"></i> One number
                    </div>
                </div>
            </div>
            
            <div class="form-group-custom">
                <label class="form-label-custom">
                    <i class="bi bi-lock-fill"></i> Confirm New Password
                </label>
                <div class="password-input-wrapper">
                    <input type="password" name="confirm_password" class="form-control-custom" id="confirmPassword" required>
                    <button type="button" class="password-toggle" data-target="confirmPassword">
                        <i class="bi bi-eye"></i>
                    </button>
                </div>
            </div>
            
            <div class="action-buttons">
                <button type="submit" class="btn btn-custom btn-warning-custom">
                    <i class="bi bi-shield-check"></i> Change Password
                </button>
                <a href="dashboard.php" class="btn btn-custom btn-secondary-custom">
                    <i class="bi bi-arrow-left"></i> Back to Dashboard
                </a>
            </div>
        </form>
        
        <div class="security-tips">
            <h5><i class="bi bi-info-circle"></i> Password Security Tips</h5>
            <ul>
                <li>Use a combination of letters, numbers, and special characters</li>
                <li>Avoid using personal information like birthdays or names</li>
                <li>Don't reuse passwords across different accounts</li>
                <li>Consider using a password manager to generate strong passwords</li>
            </ul>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Password visibility toggle
    const toggleButtons = document.querySelectorAll('.password-toggle');
    toggleButtons.forEach(button => {
        button.addEventListener('click', function() {
            const targetId = this.getAttribute('data-target');
            const targetInput = document.getElementById(targetId);
            const icon = this.querySelector('i');
            
            if (targetInput.type === 'password') {
                targetInput.type = 'text';
                icon.classList.remove('bi-eye');
                icon.classList.add('bi-eye-slash');
            } else {
                targetInput.type = 'password';
                icon.classList.remove('bi-eye-slash');
                icon.classList.add('bi-eye');
            }
        });
    });
    
    // Password strength checker
    const newPasswordInput = document.getElementById('newPassword');
    const passwordStrengthBar = document.getElementById('passwordStrength');
    const lengthReq = document.getElementById('lengthReq');
    const upperReq = document.getElementById('upperReq');
    const lowerReq = document.getElementById('lowerReq');
    const numberReq = document.getElementById('numberReq');
    
    newPasswordInput.addEventListener('input', function() {
        const password = this.value;
        
        // Check requirements
        const hasLength = password.length >= 8;
        const hasUpper = /[A-Z]/.test(password);
        const hasLower = /[a-z]/.test(password);
        const hasNumber = /[0-9]/.test(password);
        
        // Update requirement indicators
        updateRequirement(lengthReq, hasLength);
        updateRequirement(upperReq, hasUpper);
        updateRequirement(lowerReq, hasLower);
        updateRequirement(numberReq, hasNumber);
        
        // Calculate strength
        let strength = 0;
        if (hasLength) strength++;
        if (hasUpper) strength++;
        if (hasLower) strength++;
        if (hasNumber) strength++;
        
        // Update strength bar
        passwordStrengthBar.className = 'password-strength-bar';
        if (password.length > 0) {
            if (strength <= 2) {
                passwordStrengthBar.classList.add('strength-weak');
            } else if (strength === 3) {
                passwordStrengthBar.classList.add('strength-medium');
            } else {
                passwordStrengthBar.classList.add('strength-strong');
            }
        }
    });
    
    function updateRequirement(element, isMet) {
        const icon = element.querySelector('i');
        if (isMet) {
            element.classList.remove('unmet');
            element.classList.add('met');
            icon.classList.remove('bi-circle');
            icon.classList.add('bi-check-circle-fill');
        } else {
            element.classList.remove('met');
            element.classList.add('unmet');
            icon.classList.remove('bi-check-circle-fill');
            icon.classList.add('bi-circle');
        }
    }
    
    // Form validation
    const form = document.getElementById('changePasswordForm');
    const confirmPasswordInput = document.getElementById('confirmPassword');
    
    form.addEventListener('submit', function(e) {
        const newPassword = newPasswordInput.value;
        const confirmPassword = confirmPasswordInput.value;
        
        if (newPassword !== confirmPassword) {
            e.preventDefault();
            
            // Create error message if it doesn't exist
            let errorMsg = document.querySelector('.password-mismatch');
            if (!errorMsg) {
                errorMsg = document.createElement('div');
                errorMsg.className = 'alert alert-custom alert-danger-custom password-mismatch';
                errorMsg.innerHTML = '<i class="bi bi-exclamation-triangle-fill"></i> New password and confirmation do not match.';
                confirmPasswordInput.parentNode.parentNode.appendChild(errorMsg);
            }
            
            // Scroll to error
            errorMsg.scrollIntoView({ behavior: 'smooth', block: 'center' });
            
            // Highlight fields
            newPasswordInput.style.borderColor = 'var(--error-color)';
            confirmPasswordInput.style.borderColor = 'var(--error-color)';
            
            // Remove error when user starts typing again
            newPasswordInput.addEventListener('input', function() {
                if (this.value === confirmPasswordInput.value) {
                    if (errorMsg) errorMsg.remove();
                    this.style.borderColor = '';
                    confirmPasswordInput.style.borderColor = '';
                }
            });
            
            confirmPasswordInput.addEventListener('input', function() {
                if (this.value === newPasswordInput.value) {
                    if (errorMsg) errorMsg.remove();
                    this.style.borderColor = '';
                    newPasswordInput.style.borderColor = '';
                }
            });
        }
    });
});
</script>
</body>
</html>