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
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Profile - Student</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
<style>
    :root {
        --primary-color: #4361ee;
        --secondary-color: #3f37c9;
        --accent-color: #f72585;
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
    
    .profile-container {
        max-width: 900px;
        margin: 0 auto;
    }
    
    .profile-header {
        background: white;
        border-radius: 15px;
        padding: 30px;
        margin-bottom: 25px;
        box-shadow: var(--card-shadow);
        position: relative;
        overflow: hidden;
    }
    
    .profile-header::before {
        content: "";
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 120px;
        background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
        z-index: 0;
    }
    
    .profile-avatar {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        background-color: white;
        border: 5px solid white;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 48px;
        color: var(--primary-color);
        margin: 0 auto;
        position: relative;
        z-index: 1;
    }
    
    .profile-name {
        text-align: center;
        margin-top: 15px;
        position: relative;
        z-index: 1;
    }
    
    .profile-name h2 {
        margin: 0;
        color: #333;
        font-weight: 600;
    }
    
    .profile-name p {
        margin: 5px 0 0;
        color: #666;
    }
    
    .profile-card {
        background: white;
        border-radius: 15px;
        padding: 30px;
        box-shadow: var(--card-shadow);
        margin-bottom: 25px;
    }
    
    .profile-card h3 {
        color: var(--primary-color);
        margin-bottom: 20px;
        font-weight: 600;
        display: flex;
        align-items: center;
    }
    
    .profile-card h3 i {
        margin-right: 10px;
    }
    
    .info-item {
        display: flex;
        padding: 12px 0;
        border-bottom: 1px solid #f0f0f0;
    }
    
    .info-item:last-child {
        border-bottom: none;
    }
    
    .info-label {
        font-weight: 600;
        color: #555;
        width: 150px;
        display: flex;
        align-items: center;
    }
    
    .info-label i {
        margin-right: 10px;
        color: var(--primary-color);
    }
    
    .info-value {
        color: #333;
        flex: 1;
    }
    
    .action-buttons {
        display: flex;
        gap: 15px;
        flex-wrap: wrap;
    }
    
    .btn-custom {
        padding: 12px 25px;
        border-radius: 50px;
        font-weight: 500;
        transition: all 0.3s ease;
        border: none;
        display: inline-flex;
        align-items: center;
    }
    
    .btn-custom i {
        margin-right: 8px;
    }
    
    .btn-primary-custom {
        background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
        color: white;
    }
    
    .btn-primary-custom:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 20px rgba(67, 97, 238, 0.3);
        color: white;
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
        background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        color: white;
    }
    
    .btn-secondary-custom:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 20px rgba(79, 172, 254, 0.3);
        color: white;
    }
    
    .profile-stats {
        display: flex;
        justify-content: space-around;
        margin-top: 20px;
    }
    
    .stat-item {
        text-align: center;
    }
    
    .stat-value {
        font-size: 24px;
        font-weight: 700;
        color: var(--primary-color);
    }
    
    .stat-label {
        font-size: 14px;
        color: #666;
    }
    
    @media (max-width: 768px) {
        .info-item {
            flex-direction: column;
        }
        
        .info-label {
            width: 100%;
            margin-bottom: 5px;
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
<div class="container profile-container">
    <div class="profile-header">
        <div class="profile-avatar">
            <i class="bi bi-person-fill"></i>
        </div>
        <div class="profile-name">
            <h2><?= htmlspecialchars($currentUser['fullname']) ?></h2>
            <p>Student ID: <?= htmlspecialchars($currentUser['username']) ?></p>
        </div>
        <div class="profile-stats">
            <div class="stat-item">
                <div class="stat-value"><?= date('Y') - date('Y', strtotime($currentUser['birthday'])) ?></div>
                <div class="stat-label">Age</div>
            </div>
            <div class="stat-item">
                <div class="stat-value"><?= htmlspecialchars($currentUser['sex']) ?></div>
                <div class="stat-label">Gender</div>
            </div>
            <div class="stat-item">
                <div class="stat-value">Active</div>
                <div class="stat-label">Status</div>
            </div>
        </div>
    </div>
    
    <div class="profile-card">
        <h3><i class="bi bi-person-badge"></i> Personal Information</h3>
        <div class="info-item">
            <div class="info-label"><i class="bi bi-person"></i> Full Name</div>
            <div class="info-value"><?= htmlspecialchars($currentUser['fullname']) ?></div>
        </div>
        <div class="info-item">
            <div class="info-label"><i class="bi bi-person-circle"></i> Username</div>
            <div class="info-value"><?= htmlspecialchars($currentUser['username']) ?></div>
        </div>
        <div class="info-item">
            <div class="info-label"><i class="bi bi-envelope"></i> Email</div>
            <div class="info-value"><?= htmlspecialchars($currentUser['contact']) ?></div>
        </div>
        <div class="info-item">
            <div class="info-label"><i class="bi bi-geo-alt"></i> Address</div>
            <div class="info-value"><?= htmlspecialchars($currentUser['address']) ?></div>
        </div>
        <div class="info-item">
            <div class="info-label"><i class="bi bi-calendar"></i> Birthday</div>
            <div class="info-value"><?= htmlspecialchars($currentUser['birthday']) ?></div>
        </div>
        <div class="info-item">
            <div class="info-label"><i class="bi bi-gender-ambiguous"></i> Sex</div>
            <div class="info-value"><?= htmlspecialchars($currentUser['sex']) ?></div>
        </div>
    </div>
    
    <div class="profile-card">
        <h3><i class="bi bi-gear"></i> Account Actions</h3>
        <div class="action-buttons">
            <a href="edit_profile.php" class="btn btn-custom btn-primary-custom">
                <i class="bi bi-pencil-square"></i> Edit Profile
            </a>
            <a href="change_password.php" class="btn btn-custom btn-warning-custom">
                <i class="bi bi-key"></i> Change Password
            </a>
            <a href="dashboard.php" class="btn btn-custom btn-secondary-custom">
                <i class="bi bi-arrow-left"></i> Back to Dashboard
            </a>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>