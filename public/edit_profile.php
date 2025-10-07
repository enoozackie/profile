<?php
header("Expires: Tue, 01 Jan 2000 00:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

session_start();
if (!isset($_SESSION['id']) || $_SESSION['role'] !== 'user') {
    header("Location: login.php");
    exit;
}

require __DIR__ . '/../vendor/autoload.php';
use Lourdian\BasicStudent\Model\User;

 $userModel = new User();
 $currentUser = $userModel->getById($_SESSION['id']);
 $success = '';
 $error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Map form values to DB values
    $sexMap = [
        'M' => 'Male',
        'F' => 'Female',
        'O' => 'Other'
    ];

    $data = [
        'fullname' => $_POST['fullname'] ?? $currentUser['fullname'],
        'contact'  => $_POST['contact'] ?? $currentUser['contact'],
        'address'  => $_POST['address'] ?? $currentUser['address'],
        'birthday' => $_POST['birthday'] ?? $currentUser['birthday'],
        'sex'      => $sexMap[$_POST['sex']] ?? $currentUser['sex']
    ];

    if ($userModel->updateProfileOnly($_SESSION['id'], $data)) {
        $success = "Profile updated successfully.";
        $currentUser = $userModel->getById($_SESSION['id']); // refresh
    } else {
        $error = "Failed to update profile.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Edit Profile - Student</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
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
    padding: 20px 0;
    position: relative;
    overflow-x: hidden;
}

/* Animated background elements */
.bg-animation {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    overflow: hidden;
    z-index: -1;
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

.edit-container {
    max-width: 900px;
    margin: 0 auto;
    position: relative;
    z-index: 1;
}

.page-header {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
    border-radius: 20px;
    box-shadow: var(--shadow-xl);
    padding: 40px;
    margin-bottom: 30px;
    text-align: center;
    position: relative;
    overflow: hidden;
    animation: fadeInUp 0.8s ease-out;
}

.page-header::before {
    content: "";
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 120px;
    background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
    z-index: 0;
}

.page-header::after {
    content: "";
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
    animation: rotate 30s linear infinite;
    z-index: 0;
}

@keyframes rotate {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

.header-content {
    position: relative;
    z-index: 1;
}

.header-icon {
    width: 90px;
    height: 90px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.2);
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 20px;
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
    position: relative;
    z-index: 1;
}

.header-icon i {
    font-size: 40px;
    color: white;
}

.page-header h2 {
    margin: 0;
    color: white;
    font-weight: 600;
    font-size: 28px;
    position: relative;
    z-index: 1;
}

.page-header p {
    margin: 8px 0 0;
    color: rgba(255, 255, 255, 0.9);
    font-size: 16px;
    position: relative;
    z-index: 1;
}

.form-card {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
    border-radius: 20px;
    box-shadow: var(--shadow-xl);
    padding: 40px;
    margin-bottom: 30px;
    animation: fadeInUp 0.8s ease-out 0.2s both;
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

.form-section {
    margin-bottom: 35px;
}

.form-section-title {
    font-size: 18px;
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: 20px;
    padding-bottom: 10px;
    border-bottom: 2px solid var(--border-color);
    display: flex;
    align-items: center;
}

.form-section-title i {
    margin-right: 10px;
    color: var(--primary-color);
}

.form-group-custom {
    margin-bottom: 25px;
    position: relative;
}

.form-label-custom {
    font-weight: 500;
    color: var(--text-primary);
    margin-bottom: 8px;
    display: flex;
    align-items: center;
    font-size: 15px;
}

.form-label-custom i {
    margin-right: 8px;
    color: var(--primary-color);
    font-size: 18px;
}

.form-control-custom {
    border: 2px solid var(--border-color);
    border-radius: 12px;
    padding: 14px 16px;
    font-size: 16px;
    transition: var(--transition);
    width: 100%;
    background-color: var(--bg-secondary);
}

.form-control-custom:focus {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
    outline: none;
    background-color: var(--bg-primary);
}

select.form-control-custom {
    cursor: pointer;
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%236366f1' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M2 5l6 6 6-6'/%3e%3c/svg%3e");
    background-repeat: no-repeat;
    background-position: right 16px center;
    background-size: 16px 12px;
    padding-right: 45px;
    appearance: none;
}

.alert-custom {
    border-radius: 12px;
    padding: 16px 20px;
    margin-bottom: 25px;
    border: none;
    display: flex;
    align-items: center;
    animation: slideDown 0.5s ease;
}

.alert-success-custom {
    background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
    color: #065f46;
}

.alert-danger-custom {
    background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
    color: #991b1b;
}

.alert-custom i {
    margin-right: 12px;
    font-size: 20px;
}

.action-buttons {
    display: flex;
    gap: 15px;
    flex-wrap: wrap;
    margin-top: 30px;
    justify-content: center;
}

.btn-custom {
    padding: 14px 28px;
    border-radius: 50px;
    font-weight: 600;
    font-size: 16px;
    transition: var(--transition);
    border: none;
    display: inline-flex;
    align-items: center;
    text-decoration: none;
    position: relative;
    overflow: hidden;
}

.btn-custom i {
    margin-right: 8px;
    font-size: 18px;
}

.btn-primary-custom {
    background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
    color: white;
    box-shadow: 0 4px 14px rgba(99, 102, 241, 0.3);
}

.btn-primary-custom:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 20px rgba(99, 102, 241, 0.4);
    color: white;
}

.btn-primary-custom::before {
    content: "";
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
    transition: left 0.5s;
}

.btn-primary-custom:hover::before {
    left: 100%;
}

.btn-warning-custom {
    background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    color: white;
    box-shadow: 0 4px 14px rgba(245, 158, 11, 0.3);
}

.btn-warning-custom:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 20px rgba(245, 158, 11, 0.4);
    color: white;
}

.btn-secondary-custom {
    background: linear-gradient(135deg, #6b7280 0%, #4b5563 100%);
    color: white;
    box-shadow: 0 4px 14px rgba(107, 114, 128, 0.3);
}

.btn-secondary-custom:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 20px rgba(107, 114, 128, 0.4);
    color: white;
}

.form-row {
    display: flex;
    gap: 20px;
}

.form-col {
    flex: 1;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .page-header, .form-card {
        padding: 30px 20px;
    }
    
    .form-row {
        flex-direction: column;
        gap: 0;
    }
    
    .action-buttons {
        flex-direction: column;
    }
    
    .btn-custom {
        width: 100%;
        justify-content: center;
    }
    
    .page-header h2 {
        font-size: 24px;
    }
}

/* Form validation styles */
.form-control-custom.is-valid {
    border-color: var(--success-color);
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='%2310b981' d='M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0z'/%3e%3c/svg%3e");
    background-repeat: no-repeat;
    background-position: right 16px center;
    background-size: 16px 16px;
    padding-right: 45px;
}

.form-control-custom.is-invalid {
    border-color: var(--error-color);
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='%23ef4444' d='M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z'/%3e%3cpath fill='%23ef4444' d='M7.002 11a1 1 0 1 1 2 0 1 1 0 0 1-2 0zM7.1 4.995a.905.905 0 1 1 1.8 0l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 4.995z'/%3e%3c/svg%3e");
    background-repeat: no-repeat;
    background-position: right 16px center;
    background-size: 16px 16px;
    padding-right: 45px;
}

/* Loading spinner */
.spinner-border-sm {
    width: 1rem;
    height: 1rem;
    border-width: 0.2em;
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

<div class="container edit-container">
    <div class="page-header">
        <div class="header-content">
            <div class="header-icon">
                <i class="bi bi-pencil-square"></i>
            </div>
            <h2>Edit Profile</h2>
            <p>Update your personal information</p>
        </div>
    </div>

    <?php if ($success): ?>
        <div class="alert alert-custom alert-success-custom">
            <i class="bi bi-check-circle-fill"></i>
            <span><?= htmlspecialchars($success) ?></span>
        </div>
    <?php elseif ($error): ?>
        <div class="alert alert-custom alert-danger-custom">
            <i class="bi bi-exclamation-triangle-fill"></i>
            <span><?= htmlspecialchars($error) ?></span>
        </div>
    <?php endif; ?>

    <div class="form-card">
        <form method="POST" id="editProfileForm">
            <div class="form-section">
                <h3 class="form-section-title">
                    <i class="bi bi-person-badge"></i> Personal Information
                </h3>
                
                <div class="form-group-custom">
                    <label class="form-label-custom">
                        <i class="bi bi-person"></i> Full Name
                    </label>
                    <input type="text" name="fullname" class="form-control-custom" value="<?= htmlspecialchars($currentUser['fullname']) ?>" required>
                </div>

                <div class="form-group-custom">
                    <label class="form-label-custom">
                        <i class="bi bi-envelope"></i> Contact Information
                    </label>
                    <input type="text" name="contact" class="form-control-custom" value="<?= htmlspecialchars($currentUser['contact']) ?>" placeholder="Email or phone number">
                </div>

                <div class="form-group-custom">
                    <label class="form-label-custom">
                        <i class="bi bi-geo-alt"></i> Address
                    </label>
                    <input type="text" name="address" class="form-control-custom" value="<?= htmlspecialchars($currentUser['address']) ?>" placeholder="Your address">
                </div>
            </div>

            <div class="form-section">
                <h3 class="form-section-title">
                    <i class="bi bi-info-circle"></i> Additional Details
                </h3>
                
                <div class="form-row">
                    <div class="form-col">
                        <div class="form-group-custom">
                            <label class="form-label-custom">
                                <i class="bi bi-calendar"></i> Birthday
                            </label>
                            <input type="date" name="birthday" class="form-control-custom" value="<?= htmlspecialchars($currentUser['birthday']) ?>">
                        </div>
                    </div>

                    <div class="form-col">
                        <div class="form-group-custom">
                            <label class="form-label-custom">
                                <i class="bi bi-gender-ambiguous"></i> Sex
                            </label>
                            <select name="sex" class="form-control-custom">
                                <option value="M" <?= $currentUser['sex'] === 'Male' ? 'selected' : '' ?>>Male</option>
                                <option value="F" <?= $currentUser['sex'] === 'Female' ? 'selected' : '' ?>>Female</option>
                                <option value="O" <?= $currentUser['sex'] === 'Other' ? 'selected' : '' ?>>Other</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div class="action-buttons">
                <button type="submit" class="btn btn-custom btn-primary-custom" id="updateBtn">
                    <i class="bi bi-check-lg"></i> Update Profile
                </button>
                <a href="change_password.php" class="btn btn-custom btn-warning-custom">
                    <i class="bi bi-key"></i> Change Password
                </a>
                <a href="dashboard.php" class="btn btn-custom btn-secondary-custom">
                    <i class="bi bi-speedometer2"></i> Dashboard
                </a>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('editProfileForm');
    const inputs = form.querySelectorAll('.form-control-custom');
    const updateBtn = document.getElementById('updateBtn');

    // Form validation and visual feedback
    inputs.forEach(input => {
        input.addEventListener('blur', function() {
            if (this.hasAttribute('required') && !this.value) {
                this.classList.remove('is-valid');
                this.classList.add('is-invalid');
            } else if (this.value) {
                this.classList.remove('is-invalid');
                this.classList.add('is-valid');
            }
        });
        
        input.addEventListener('focus', function() {
            this.classList.remove('is-valid', 'is-invalid');
        });
    });

    // Form submission with loading state
    form.addEventListener('submit', function(e) {
        // Add loading state
        updateBtn.disabled = true;
        updateBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Updating...';
        
        // Re-enable after 3 seconds as a fallback
        setTimeout(() => {
            updateBtn.disabled = false;
            updateBtn.innerHTML = '<i class="bi bi-check-lg"></i> Update Profile';
        }, 3000);
    });

    // Add ripple effect to buttons
    const buttons = document.querySelectorAll('.btn-custom');
    
    buttons.forEach(button => {
        button.addEventListener('click', function(e) {
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
    
    .btn-custom {
        position: relative;
        overflow: hidden;
    }
`;
document.head.appendChild(style);
</script>
</body>
</html>