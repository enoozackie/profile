<?php
use Lourdian\BasicStudent\Model\Auth;
use Lourdian\BasicStudent\Model\User;
require __DIR__ . "/../vendor/autoload.php";

session_start();

header("Expires: Tue, 01 Jan 2000 00:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
$error = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user = new User();

    $data = [
        "username" => $_POST['username'],
        "password" => $_POST['password'],
        "fullname" => $_POST['fullname'],
        "address"  => $_POST['address'],
        "birthday" => $_POST['birthday'],
        "contact"  => $_POST['contact'],
        "sex"      => $_POST['sex']
    ];

    if ($user->create($data)) {
        header("Location: login.php");
        exit;
    } else {
        $error = "Registration failed!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - Student Portal</title>
     <script>
        // Detect if page was loaded from bfcache (back/forward navigation)
        window.addEventListener("pageshow", function(event) {
            if (event.persisted) { 
                // Force reload from server if loaded from cache
                window.location.reload();
            }
        });

        // Also detect old browsers navigation type
        if (performance.getEntriesByType &&
            performance.getEntriesByType("navigation")[0] &&
            performance.getEntriesByType("navigation")[0].type === "back_forward") {
            location.reload();
        } else if (performance.navigation && performance.navigation.type === 2) {
            location.reload();
        }
    </script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
:root {
    --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    --secondary-gradient: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    --success-gradient: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
    --danger-gradient: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
    
    --primary-color: #667eea;
    --secondary-color: #764ba2;
    --light-bg: #f8fafc;
    --card-bg: #ffffff;
    --text-primary: #2d3748;
    --text-secondary: #718096;
    --border-light: #e2e8f0;
    --shadow-sm: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
    --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    --error-color: #f56565;
    --success-color: #48bb78;
    --border-radius: 12px;
    --border-radius-lg: 16px;
    --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    background: linear-gradient(135deg, #f7fafc 0%, #edf2f7 25%, #e2e8f0 50%, #cbd5e0 75%, #a0aec0 100%);
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 20px;
    position: relative;
    overflow-x: hidden;
}

body::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: 
        radial-gradient(circle at 20% 80%, rgba(120, 119, 198, 0.3) 0%, transparent 50%),
        radial-gradient(circle at 80% 20%, rgba(255, 119, 198, 0.3) 0%, transparent 50%),
        radial-gradient(circle at 40% 40%, rgba(120, 219, 255, 0.2) 0%, transparent 50%);
    pointer-events: none;
    z-index: -1;
}

.signup-container {
    width: 100%;
    max-width: 500px;
    position: relative;
    z-index: 1;
}

.signup-card {
    background: var(--card-bg);
    border-radius: var(--border-radius-lg);
    box-shadow: var(--shadow-xl);
    border: 1px solid rgba(255, 255, 255, 0.2);
    overflow: hidden;
    backdrop-filter: blur(10px);
    animation: slideInUp 0.6s ease-out;
}

@keyframes slideInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.signup-header {
    background: var(--primary-gradient);
    color: white;
    padding: 2.5rem 2rem;
    text-align: center;
    position: relative;
    overflow: hidden;
}

.signup-header::before {
    content: '';
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
    animation: float 6s ease-in-out infinite;
}

@keyframes float {
    0%, 100% { transform: translateY(0px) rotate(0deg); }
    50% { transform: translateY(-20px) rotate(180deg); }
}

.signup-header h2 {
    font-weight: 700;
    margin-bottom: 0.5rem;
    font-size: 1.875rem;
    position: relative;
    z-index: 1;
}

.signup-header p {
    opacity: 0.95;
    margin-bottom: 0;
    font-size: 1rem;
    font-weight: 400;
    position: relative;
    z-index: 1;
}

.signup-body {
    padding: 2.5rem 2rem;
}

.form-group {
    margin-bottom: 1.5rem;
}

.form-group.row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
    margin-bottom: 1.5rem;
}

.form-label {
    display: block;
    margin-bottom: 0.5rem;
    color: var(--text-primary);
    font-weight: 500;
    font-size: 0.875rem;
}

.form-label.required::after {
    content: ' *';
    color: var(--error-color);
    font-weight: 600;
}

.input-group {
    position: relative;
    display: flex;
    align-items: stretch;
}

.input-group-text {
    background: linear-gradient(145deg, #f7fafc, #edf2f7);
    border: 2px solid var(--border-light);
    border-right: none;
    border-radius: var(--border-radius) 0 0 var(--border-radius);
    color: var(--text-secondary);
    display: flex;
    align-items: center;
    justify-content: center;
    width: 50px;
    transition: var(--transition);
}

.form-control {
    border: 2px solid var(--border-light);
    border-left: none;
    border-radius: 0 var(--border-radius) var(--border-radius) 0;
    padding: 0.875rem 1rem;
    font-size: 1rem;
    font-weight: 400;
    background: #fff;
    transition: var(--transition);
    color: var(--text-primary);
    flex: 1;
}

.form-control::placeholder {
    color: var(--text-secondary);
    font-weight: 400;
}

.input-group:focus-within .input-group-text {
    border-color: var(--primary-color);
    background: linear-gradient(145deg, rgba(102, 126, 234, 0.1), rgba(118, 75, 162, 0.05));
    color: var(--primary-color);
    transform: translateY(-1px);
}

.form-control:focus {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    background: #fff;
    transform: translateY(-1px);
}

.gender-group {
    display: flex;
    gap: 2rem;
    margin-top: 0.5rem;
}

.form-check {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 1.25rem;
    background: linear-gradient(145deg, #f7fafc, #edf2f7);
    border: 2px solid var(--border-light);
    border-radius: var(--border-radius);
    transition: var(--transition);
    cursor: pointer;
    flex: 1;
}

.form-check:hover {
    border-color: var(--primary-color);
    background: linear-gradient(145deg, rgba(102, 126, 234, 0.1), rgba(118, 75, 162, 0.05));
    transform: translateY(-1px);
}

.form-check input[type="radio"]:checked + label {
    color: var(--primary-color);
    font-weight: 600;
}

.form-check input[type="radio"] {
    accent-color: var(--primary-color);
    margin: 0;
    width: 18px;
    height: 18px;
}

.form-check-label {
    color: var(--text-primary);
    font-weight: 500;
    cursor: pointer;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.875rem;
}

.btn-signup {
    background: var(--primary-gradient);
    border: none;
    border-radius: var(--border-radius);
    padding: 1rem 1.5rem;
    font-weight: 600;
    font-size: 1rem;
    color: white;
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    transition: var(--transition);
    position: relative;
    overflow: hidden;
    margin-bottom: 2rem;
}

.btn-signup::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
    transition: left 0.5s;
}

.btn-signup:hover::before {
    left: 100%;
}

.btn-signup:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px rgba(102, 126, 234, 0.4);
    color: white;
}

.btn-signup:active {
    transform: translateY(0);
}

.btn-signup:disabled {
    background: linear-gradient(135deg, #a0aec0 0%, #718096 100%);
    cursor: not-allowed;
    transform: none;
}

.btn-signup:disabled:hover {
    transform: none;
    box-shadow: none;
}

.error-message {
    color: var(--error-color);
    background: linear-gradient(145deg, #fed7d7, #feb2b2);
    border: 1px solid #fc8181;
    border-radius: var(--border-radius);
    padding: 1rem 1.25rem;
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
    gap: 0.75rem;
    font-size: 0.875rem;
    font-weight: 500;
    animation: shake 0.5s ease-in-out;
}

@keyframes shake {
    0%, 100% { transform: translateX(0); }
    25% { transform: translateX(-5px); }
    75% { transform: translateX(5px); }
}

.divider {
    display: flex;
    align-items: center;
    margin: 2rem 0;
    color: var(--text-secondary);
    font-size: 0.875rem;
    font-weight: 500;
}

.divider::before,
.divider::after {
    content: '';
    flex: 1;
    height: 2px;
    background: linear-gradient(90deg, transparent, var(--border-light), transparent);
}

.divider span {
    padding: 0 1.5rem;
    background: var(--card-bg);
    position: relative;
}

.login-link {
    text-align: center;
    color: var(--text-secondary);
    font-size: 0.95rem;
}

.login-link a {
    color: var(--primary-color);
    text-decoration: none;
    font-weight: 600;
    transition: var(--transition);
}

.login-link a:hover {
    color: var(--secondary-color);
    text-decoration: underline;
}

/* Form Validation Styles */
.form-control:invalid:not(:placeholder-shown) {
    border-color: var(--error-color);
    background: #fff5f5;
}

.form-control:valid:not(:placeholder-shown) {
    border-color: var(--success-color);
    background: #f0fff4;
}

.form-control:invalid:not(:placeholder-shown) + .invalid-feedback {
    display: block;
}

.invalid-feedback {
    display: none;
    color: var(--error-color);
    font-size: 0.8rem;
    margin-top: 0.25rem;
}

.valid-feedback {
    display: none;
    color: var(--success-color);
    font-size: 0.8rem;
    margin-top: 0.25rem;
}

.form-control:valid:not(:placeholder-shown) + .valid-feedback {
    display: block;
}

/* Responsive Design */
@media (max-width: 576px) {
    body {
        padding: 10px;
    }
    
    .signup-header {
        padding: 2rem 1.5rem;
    }
    
    .signup-header h2 {
        font-size: 1.5rem;
    }
    
    .signup-body {
        padding: 2rem 1.5rem;
    }
    
    .form-group.row {
        grid-template-columns: 1fr;
        gap: 1rem;
    }
    
    .gender-group {
        flex-direction: column;
        gap: 1rem;
    }
    
    .btn-signup {
        padding: 0.875rem 1rem;
        font-size: 0.95rem;
    }
}

@media (max-width: 360px) {
    .signup-container {
        max-width: 100%;
    }
    
    .signup-header {
        padding: 1.5rem 1rem;
    }
    
    .signup-body {
        padding: 1.5rem 1rem;
    }
}

/* Enhanced animations */
.signup-card:hover {
    transform: translateY(-2px);
    box-shadow: 
        0 25px 50px -12px rgba(0, 0, 0, 0.25),
        0 0 0 1px rgba(255, 255, 255, 0.05);
    transition: var(--transition);
}

/* Progress indicator */
.progress-indicator {
    display: flex;
    justify-content: space-between;
    margin-bottom: 2rem;
    padding: 0 1rem;
}

.progress-step {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: var(--border-light);
    transition: var(--transition);
}

.progress-step.active {
    background: var(--primary-gradient);
    transform: scale(1.2);
}

/* Loading spinner */
.spinner-border-sm {
    width: 1rem;
    height: 1rem;
}

/* Focus indicators for accessibility */
.btn-signup:focus-visible,
.form-control:focus-visible,
.form-check input:focus-visible {
    outline: 2px solid var(--primary-color);
    outline-offset: 2px;
}
</style>
</head>
<body>
    <div class="signup-container">
        <div class="card signup-card">
            <div class="signup-header">
                <h2><i class="bi bi-person-plus-fill me-2"></i>Create Account</h2>
                <p>Join our student portal community</p>
            </div>
            <div class="signup-body">
                <?php if (!empty($error)): ?>
                    <div class="error-message">
                        <i class="bi bi-exclamation-circle-fill"></i>
                        <span><?= htmlspecialchars($error) ?></span>
                    </div>
                <?php endif; ?>

                <div class="progress-indicator">
                    <div class="progress-step active"></div>
                    <div class="progress-step"></div>
                    <div class="progress-step"></div>
                    <div class="progress-step"></div>
                </div>
                
                <form method="post" id="signupForm" novalidate>
                    <div class="form-group">
                        <label for="username" class="form-label required">Username</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-person-fill"></i></span>
                            <input type="text" class="form-control" id="username" name="username" 
                                   placeholder="Enter your username" required minlength="3">
                        </div>
                        <div class="invalid-feedback">Username must be at least 3 characters long</div>
                        <div class="valid-feedback">Username looks good!</div>
                    </div>
                    
                    <div class="form-group">
                        <label for="password" class="form-label required">Password</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                            <input type="password" class="form-control" id="password" name="password" 
                                   placeholder="Create a strong password" required minlength="6">
                        </div>
                        <div class="invalid-feedback">Password must be at least 6 characters long</div>
                        <div class="valid-feedback">Password strength looks good!</div>
                    </div>
                    
                    <div class="form-group">
                        <label for="fullname" class="form-label">Full Name</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-person-badge-fill"></i></span>
                            <input type="text" class="form-control" id="fullname" name="fullname" 
                                   placeholder="Enter your full name">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="address" class="form-label">Address</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-geo-alt-fill"></i></span>
                            <input type="text" class="form-control" id="address" name="address" 
                                   placeholder="Enter your address">
                        </div>
                    </div>
                    
                    <div class="form-group row">
                        <div>
                            <label for="birthday" class="form-label">Date of Birth</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-calendar-event-fill"></i></span>
                                <input type="date" class="form-control" id="birthday" name="birthday">
                            </div>
                        </div>
                        
                        <div>
                            <label for="contact" class="form-label">Contact Number</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-telephone-fill"></i></span>
                                <input type="tel" class="form-control" id="contact" name="contact" 
                                       placeholder="Your phone number">
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label required">Gender</label>
                        <div class="gender-group">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="sex" id="male" value="Male" checked>
                                <label class="form-check-label" for="male">
                                    <i class="bi bi-person-standing"></i> Male
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="sex" id="female" value="Female">
                                <label class="form-check-label" for="female">
                                    <i class="bi bi-person-standing-dress"></i> Female
                                </label>
                            </div>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-signup" id="signupBtn">
                        <i class="bi bi-person-plus-fill"></i> Create Account
                    </button>
                </form>
                
                <div class="divider"><span>OR</span></div>
                
                <div class="login-link">
                    Already have an account? 
                    <a href="login.php">
                        <i class="bi bi-box-arrow-in-right me-1"></i>Sign In
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Enhanced form validation and interactions
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('signupForm');
            const signupBtn = document.getElementById('signupBtn');
            const progressSteps = document.querySelectorAll('.progress-step');
            
            // Progress indicator animation
            const inputs = form.querySelectorAll('input[required]');
            inputs.forEach((input, index) => {
                input.addEventListener('input', function() {
                    const filledInputs = Array.from(inputs).filter(inp => inp.value.trim() !== '').length;
                    const progress = Math.min(filledInputs, progressSteps.length);
                    
                    progressSteps.forEach((step, idx) => {
                        if (idx < progress) {
                            step.classList.add('active');
                        } else {
                            step.classList.remove('active');
                        }
                    });
                });
            });
            
            // Real-time validation
            inputs.forEach(input => {
                input.addEventListener('input', function() {
                    validateField(this);
                });
                
                input.addEventListener('blur', function() {
                    validateField(this);
                });
            });
            
            function validateField(field) {
                const value = field.value.trim();
                const fieldName = field.name;
                
                // Remove previous validation classes
                field.classList.remove('is-valid', 'is-invalid');
                
                // Validation logic
                let isValid = true;
                
                if (field.hasAttribute('required') && value === '') {
                    isValid = false;
                } else if (fieldName === 'username' && value.length < 3) {
                    isValid = false;
                } else if (fieldName === 'password' && value.length < 6) {
                    isValid = false;
                }
                
                // Apply validation classes
                if (value !== '') {
                    field.classList.add(isValid ? 'is-valid' : 'is-invalid');
                }
            }
            
            // Form submission
            form.addEventListener('submit', function(event) {
                event.preventDefault();
                
                const username = document.getElementById('username').value.trim();
                const password = document.getElementById('password').value;
                
                let isFormValid = true;
                let errors = [];
                
                // Validate username
                if (username.length < 3) {
                    isFormValid = false;
                    errors.push('Username must be at least 3 characters long.');
                }
                
                // Validate password
                if (password.length < 6) {
                    isFormValid = false;
                    errors.push('Password must be at least 6 characters long.');
                }
                
                if (!isFormValid) {
                    // Show errors with animation
                    const errorDiv = document.createElement('div');
                    errorDiv.className = 'error-message';
                    errorDiv.innerHTML = `
                        <i class="bi bi-exclamation-circle-fill"></i>
                        <span>${errors.join(' ')}</span>
                    `;
                    
                    const existingError = document.querySelector('.error-message');
                    if (existingError) {
                        existingError.remove();
                    }
                    
                    form.parentNode.insertBefore(errorDiv, form);
                    
                    // Shake animation
                    form.style.animation = 'shake 0.5s ease-in-out';
                    setTimeout(() => {
                        form.style.animation = '';
                    }, 500);
                    
                    return;
                }
                
                // Show loading state
                signupBtn.disabled = true;
                signupBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status"></span> Creating Account...';
                
                // Submit form
                setTimeout(() => {
                    form.submit();
                }, 1000);
            });
        });
        
        // Enhanced input focus effects
        document.querySelectorAll('.form-control').forEach(input => {
            input.addEventListener('focus', function() {
                this.closest('.input-group').style.transform = 'translateY(-2px)';
            });
            
            input.addEventListener('blur', function() {
                this.closest('.input-group').style.transform = 'translateY(0)';
            });
        });
        
        // Gender selection animation
        document.querySelectorAll('input[name="sex"]').forEach(radio => {
            radio.addEventListener('change', function() {
                document.querySelectorAll('.form-check').forEach(check => {
                    check.style.transform = 'scale(1)';
                    check.style.background = 'linear-gradient(145deg, #f7fafc, #edf2f7)';
                });
                
                const parentCheck = this.closest('.form-check');
                parentCheck.style.transform = 'scale(1.05)';
                parentCheck.style.background = 'linear-gradient(145deg, rgba(102, 126, 234, 0.1), rgba(118, 75, 162, 0.05))';
            });
        });
        
        // Prevent back navigation
        window.history.forward();
        function disableButtons() {
            window.history.forward();
        }
        window.onload = disableButtons;
        window.onpageshow = function(evt) {
            if (evt.persisted) disableButtons();
        };
        window.onunload = function () { null };
        
        // Enhanced page load animation
        window.addEventListener('load', function() {
            document.querySelector('.signup-card').style.opacity = '0';
            document.querySelector('.signup-card').style.transform = 'translateY(30px)';
            
            setTimeout(() => {
                document.querySelector('.signup-card').style.transition = 'all 0.6s cubic-bezier(0.4, 0, 0.2, 1)';
                document.querySelector('.signup-card').style.opacity = '1';
                document.querySelector('.signup-card').style.transform = 'translateY(0)';
            }, 100);
        });
    </script>
</body>
</html>
