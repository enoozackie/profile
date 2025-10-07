<?php
// Attempt to load Composer autoload
require __DIR__ . '/../vendor/autoload.php';
use Lourdian\BasicStudent\Model\Admin;

// Enhanced cache control
header("Expires: Tue, 01 Jan 2000 00:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check admin login
if (!isset($_SESSION['id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

 $admin = new Admin();
 $errors = [];
 $message = '';
 $student = null;

// Get student info
if (isset($_GET['id'])) {
    $id = filter_var($_GET['id'], FILTER_VALIDATE_INT);
    if ($id === false || $id <= 0) {
        die("Invalid student ID.");
    }
    
    $student = $admin->getStudentById($id);
    if (!$student) {
        die("Student not found.");
    }
} else {
    die("No student ID provided.");
}

// CSRF token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $errors[] = 'Invalid CSRF token.';
    } else {
        $fullname = trim($_POST['fullname'] ?? '');
        $username = trim($_POST['username'] ?? '');
        $address = trim($_POST['address'] ?? '');
        $birthday = trim($_POST['birthday'] ?? '');
        $contact = trim($_POST['contact'] ?? '');
        $sex = trim($_POST['sex'] ?? '');

        if (empty($fullname)) $errors[] = 'Full name is required.';
        if (empty($username)) $errors[] = 'Username is required.';

        if (empty($errors)) {
            $updateData = [
                'fullname' => $fullname,
                'username' => $username,
                'address' => $address,
                'birthday' => $birthday,
                'contact' => $contact,
                'sex' => $sex
            ];
            
            if ($admin->updateStudent($student['id'], $updateData)) {
                $_SESSION['message'] = 'Student updated successfully.';
                header("Location: edit_student.php?id=" . $student['id']);
                exit;
            } else {
                $errors[] = 'Failed to update student.';
            }
        }
    }
}

if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    unset($_SESSION['message']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Student - Admin Portal</title>
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
            --info-color: #3b82f6;
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

        /* Dashboard Header */
        .dashboard-header {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            box-shadow: var(--shadow-lg);
            padding: 1rem 0;
            margin-bottom: 2rem;
        }

        .navbar-brand {
            font-weight: 600;
            font-size: 1.5rem;
            color: white !important;
        }

        .navbar-text {
            font-weight: 500;
            color: rgba(255, 255, 255, 0.9) !important;
        }

        /* Main Container */
        .main-container {
            max-width: 900px;
            margin: 0 auto;
            padding: 0 1rem;
        }

        /* Page Header */
        .page-header {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: var(--shadow-xl);
            padding: 2rem;
            margin-bottom: 2rem;
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
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
        }

        .header-icon i {
            font-size: 36px;
            color: white;
        }

        .page-header h1 {
            color: white;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .page-header p {
            color: rgba(255, 255, 255, 0.9);
            margin-bottom: 0;
        }

        /* Form Card */
        .form-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: var(--shadow-xl);
            padding: 2rem;
            margin-bottom: 2rem;
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

        /* Form Sections */
        .form-section {
            margin-bottom: 2rem;
        }

        .form-section-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 1.5rem;
            padding-bottom: 0.75rem;
            border-bottom: 2px solid var(--border-color);
            display: flex;
            align-items: center;
        }

        .form-section-title i {
            margin-right: 0.75rem;
            color: var(--primary-color);
        }

        /* Form Groups */
        .form-group {
            margin-bottom: 1.5rem;
            position: relative;
        }

        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: var(--text-primary);
            font-size: 0.95rem;
        }

        .form-control {
            width: 100%;
            padding: 0.875rem 1rem;
            border: 2px solid var(--border-color);
            border-radius: 12px;
            font-size: 1rem;
            transition: var(--transition);
            background-color: var(--bg-secondary);
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
            outline: none;
            background-color: var(--bg-primary);
        }

        select.form-control {
            cursor: pointer;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%236366f1' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M2 5l6 6 6-6'/%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 1rem center;
            background-size: 16px 12px;
            padding-right: 2.5rem;
            appearance: none;
        }

        /* Alert Messages */
        .alert-custom {
            border-radius: 12px;
            padding: 1rem 1.25rem;
            margin-bottom: 1.5rem;
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
            margin-right: 0.75rem;
            font-size: 1.25rem;
        }

        /* Action Buttons */
        .action-buttons {
            display: flex;
            gap: 1rem;
            justify-content: center;
            margin-top: 2rem;
        }

        .btn-custom {
            padding: 0.875rem 1.5rem;
            border-radius: 50px;
            font-weight: 600;
            font-size: 1rem;
            transition: var(--transition);
            border: none;
            display: inline-flex;
            align-items: center;
            text-decoration: none;
            position: relative;
            overflow: hidden;
        }

        .btn-custom i {
            margin-right: 0.5rem;
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

        /* Form Row */
        .form-row {
            display: flex;
            gap: 1.5rem;
        }

        .form-col {
            flex: 1;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .page-header, .form-card {
                padding: 1.5rem;
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

    <!-- Dashboard Header -->
    <nav class="navbar navbar-dark dashboard-header">
        <div class="container">
            <span class="navbar-brand mb-0 h1">
                <i class="bi bi-speedometer2 me-2"></i>Admin Portal
            </span>
            <div class="d-flex">
                <span class="navbar-text me-3">
                    Welcome, <?= htmlspecialchars($_SESSION['fullname'] ?? 'Admin') ?>
                </span>
                <a href="logout.php" class="btn btn-outline-light btn-sm">
                    <i class="bi bi-box-arrow-right me-1"></i>Logout
                </a>
            </div>
        </div>
    </nav>

    <!-- Main Container -->
    <div class="main-container">
        <!-- Page Header -->
        <div class="page-header">
            <div class="header-content">
                <div class="header-icon">
                    <i class="bi bi-person-gear"></i>
                </div>
                <h1>Edit Student</h1>
                <p>Update student information</p>
            </div>
        </div>

        <!-- Form Card -->
        <div class="form-card">
            <!-- Alert Messages -->
            <?php if (!empty($errors)): ?>
                <div class="alert alert-custom alert-danger-custom">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    <div>
                        <strong>Error:</strong>
                        <ul class="mb-0 mt-1">
                            <?php foreach ($errors as $err): ?>
                                <li><?= htmlspecialchars($err) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (!empty($message)): ?>
                <div class="alert alert-custom alert-success-custom">
                    <i class="bi bi-check-circle-fill"></i>
                    <span><?= htmlspecialchars($message) ?></span>
                </div>
            <?php endif; ?>

            <form method="post">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

                <!-- Personal Information Section -->
                <div class="form-section">
                    <h3 class="form-section-title">
                        <i class="bi bi-person-badge"></i>Personal Information
                    </h3>
                    
                    <div class="form-group">
                        <label for="fullname" class="form-label">Full Name</label>
                        <input type="text" class="form-control" id="fullname" name="fullname" value="<?= htmlspecialchars($student['fullname']) ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="username" name="username" value="<?= htmlspecialchars($student['username']) ?>" required>
                    </div>
                </div>

                <!-- Contact Information Section -->
                <div class="form-section">
                    <h3 class="form-section-title">
                        <i class="bi bi-telephone"></i>Contact Information
                    </h3>
                    
                    <div class="form-group">
                        <label for="contact" class="form-label">Contact Number</label>
                        <input type="text" class="form-control" id="contact" name="contact" value="<?= htmlspecialchars($student['contact']) ?>">
                    </div>

                    <div class="form-group">
                        <label for="address" class="form-label">Address</label>
                        <input type="text" class="form-control" id="address" name="address" value="<?= htmlspecialchars($student['address']) ?>">
                    </div>
                </div>

                <!-- Additional Information Section -->
                <div class="form-section">
                    <h3 class="form-section-title">
                        <i class="bi bi-info-circle"></i>Additional Information
                    </h3>
                    
                    <div class="form-row">
                        <div class="form-col">
                            <div class="form-group">
                                <label for="birthday" class="form-label">Birthday</label>
                                <input type="date" class="form-control" id="birthday" name="birthday" value="<?= htmlspecialchars($student['birthday']) ?>">
                            </div>
                        </div>

                        <div class="form-col">
                            <div class="form-group">
                                <label for="sex" class="form-label">Sex</label>
                                <select class="form-control" id="sex" name="sex">
                                    <option value="Male" <?= $student['sex'] === 'Male' ? 'selected' : '' ?>>Male</option>
                                    <option value="Female" <?= $student['sex'] === 'Female' ? 'selected' : '' ?>>Female</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="action-buttons">
                    <button type="submit" class="btn btn-custom btn-primary-custom">
                        <i class="bi bi-check-lg"></i>Update Student
                    </button>
                    <button type="button" class="btn btn-custom btn-secondary-custom" onclick="window.location.href='admin_dashboard.php'">
                        <i class="bi bi-speedometer2"></i>Dashboard
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
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

            // Form validation feedback
            const form = document.querySelector('form');
            const inputs = form.querySelectorAll('.form-control');
            
            inputs.forEach(input => {
                input.addEventListener('blur', function() {
                    if (this.hasAttribute('required') && !this.value) {
                        this.style.borderColor = 'var(--error-color)';
                    } else if (this.value) {
                        this.style.borderColor = 'var(--success-color)';
                    }
                });
                
                input.addEventListener('focus', function() {
                    this.style.borderColor = 'var(--primary-color)';
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