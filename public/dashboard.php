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

    if (!isset($_SESSION['id']) || $_SESSION['role'] !== 'user') {
        header("Location: login.php");
        exit;
    }

    require __DIR__ . '/../vendor/autoload.php';
    use Lourdian\BasicStudent\Model\User;

    // Fetch user info
    $userModel = new User();
    $currentUser = $userModel->getById($_SESSION['id']);
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
    :root {
        --primary-color: #4361ee;
        --secondary-color: #3f37c9;
        --accent-color: #f72585;
        --success-color: #06ffa5;
        --warning-color: #ffbe0b;
        --danger-color: #ff006e;
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
    
    .dashboard-container {
        max-width: 1200px;
        margin: 0 auto;
    }
    
    .dashboard-card {
        background: rgba(255,255,255,0.95);
        border-radius: 15px;
        box-shadow: var(--card-shadow);
        overflow: hidden;
        margin-bottom: 25px;
    }
    
    .dashboard-header {
        background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
        padding: 30px;
        position: relative;
        overflow: hidden;
    }
    
    .dashboard-header::before {
        content: "";
        position: absolute;
        top: -50%;
        right: -10%;
        width: 50%;
        height: 200%;
        background: rgba(255, 255, 255, 0.1);
        transform: rotate(35deg);
    }
    
    .user-avatar {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        background: white;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 20px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        position: relative;
        z-index: 1;
    }
    
    .user-avatar i {
        font-size: 48px;
        color: var(--primary-color);
    }
    
    .welcome-text {
        color: white;
        text-align: center;
        position: relative;
        z-index: 1;
    }
    
    .welcome-text h1 {
        font-weight: 600;
        margin-bottom: 10px;
    }
    
    .welcome-text p {
        opacity: 0.9;
        margin-bottom: 0;
    }
    
    .dashboard-body {
        padding: 30px;
    }
    
    .action-cards {
        display: flex;
        gap: 20px;
        margin-bottom: 30px;
        flex-wrap: wrap;
    }
    
    .action-card {
        flex: 1;
        min-width: 200px;
        background: white;
        border-radius: 15px;
        padding: 25px;
        text-align: center;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        transition: all 0.3s ease;
        border: 1px solid #f0f0f0;
    }
    
    .action-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
    }
    
    .action-card-icon {
        width: 70px;
        height: 70px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 15px;
    }
    
    .action-card-icon.profile {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }
    
    .action-card-icon.edit {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    }
    
    .action-card-icon.password {
        background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    }
    
    .action-card-icon i {
        font-size: 32px;
        color: white;
    }
    
    .action-card h5 {
        margin-bottom: 15px;
        color: #333;
        font-weight: 600;
    }
    
    .btn-custom {
        padding: 10px 20px;
        border-radius: 50px;
        font-weight: 500;
        transition: all 0.3s ease;
        border: none;
        display: inline-flex;
        align-items: center;
        text-decoration: none;
    }
    
    .btn-custom i {
        margin-right: 5px;
    }
    
    .btn-outline-primary-custom {
        background: transparent;
        color: var(--primary-color);
        border: 2px solid var(--primary-color);
    }
    
    .btn-outline-primary-custom:hover {
        background: var(--primary-color);
        color: white;
        transform: translateY(-2px);
    }
    
    .btn-outline-success-custom {
        background: transparent;
        color: #f5576c;
        border: 2px solid #f5576c;
    }
    
    .btn-outline-success-custom:hover {
        background: #f5576c;
        color: white;
        transform: translateY(-2px);
    }
    
    .btn-outline-warning-custom {
        background: transparent;
        color: #00f2fe;
        border: 2px solid #00f2fe;
    }
    
    .btn-outline-warning-custom:hover {
        background: #00f2fe;
        color: white;
        transform: translateY(-2px);
    }
    
    .members-section {
        margin-top: 30px;
    }
    
    .section-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }
    
    .section-title {
        color: white;
        font-weight: 600;
        margin: 0;
        display: flex;
        align-items: center;
    }
    
    .section-title i {
        margin-right: 10px;
    }
    
    .member-count {
        background: rgba(255, 255, 255, 0.2);
        color: white;
        padding: 5px 15px;
        border-radius: 50px;
        font-size: 14px;
    }
    
    .table-container {
        background: white;
        border-radius: 15px;
        overflow: hidden;
        box-shadow: var(--card-shadow);
    }
    
    .table-custom {
        margin-bottom: 0;
    }
    
    .table-custom thead th {
        background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
        color: white;
        border: none;
        padding: 15px;
        font-weight: 500;
        text-transform: uppercase;
        font-size: 14px;
        letter-spacing: 0.5px;
    }
    
    .table-custom tbody tr {
        transition: all 0.2s ease;
    }
    
    .table-custom tbody tr:hover {
        background-color: #f8f9fa;
        transform: scale(1.01);
    }
    
    .table-custom tbody td {
        padding: 15px;
        vertical-align: middle;
        border-color: #f0f0f0;
    }
    
    .user-badge {
        display: inline-flex;
        align-items: center;
        padding: 5px 10px;
        border-radius: 50px;
        font-size: 12px;
        font-weight: 500;
    }
    
    .badge-student {
        background: rgba(67, 97, 238, 0.1);
        color: var(--primary-color);
    }
    
    .badge-admin {
        background: rgba(247, 37, 133, 0.1);
        color: var(--accent-color);
    }
    
    .logout-section {
        text-align: center;
        margin-top: 30px;
    }
    
    .btn-logout {
        background: linear-gradient(135deg, #ff006e 0%, #8338ec 100%);
        color: white;
        padding: 12px 30px;
        border-radius: 50px;
        font-weight: 500;
        transition: all 0.3s ease;
        border: none;
        display: inline-flex;
        align-items: center;
        text-decoration: none;
    }
    
    .btn-logout:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 20px rgba(255, 0, 110, 0.3);
        color: white;
    }
    
    .table-responsive {
        border-radius: 15px;
        overflow: hidden;
    }
    
    @media (max-width: 768px) {
        .action-cards {
            flex-direction: column;
        }
        
        .section-header {
            flex-direction: column;
            gap: 10px;
            text-align: center;
        }
        
        .table-custom {
            font-size: 14px;
        }
        
        .table-custom thead th, 
        .table-custom tbody td {
            padding: 10px 5px;
        }
    }
</style>
</head>
<body>
<div class="container dashboard-container">
    <div class="dashboard-card">
        <div class="dashboard-header">
            <div class="user-avatar">
                <i class="bi bi-person-circle"></i>
            </div>
            <div class="welcome-text">
                <h1>Welcome, <?= htmlspecialchars($currentUser['fullname'] ?? 'User') ?></h1>
                <p>You are logged in as a student.</p>
            </div>
        </div>
        
        <div class="dashboard-body">
            <div class="action-cards">
                <div class="action-card">
                    <div class="action-card-icon profile">
                        <i class="bi bi-person-badge"></i>
                    </div>
                    <h5>Profile</h5>
                    <a href="profile.php" class="btn-custom btn-outline-primary-custom">
                        <i class="bi bi-eye"></i> View Profile
                    </a>
                </div>
                
                <div class="action-card">
                    <div class="action-card-icon edit">
                        <i class="bi bi-pencil-square"></i>
                    </div>
                    <h5>Edit Profile</h5>
                    <a href="edit_profile.php" class="btn-custom btn-outline-success-custom">
                        <i class="bi bi-pencil"></i> Edit Profile
                    </a>
                </div>
                
                <div class="action-card">
                    <div class="action-card-icon password">
                        <i class="bi bi-key"></i>
                    </div>
                    <h5>Password</h5>
                    <a href="change_password.php" class="btn-custom btn-outline-warning-custom">
                        <i class="bi bi-lock"></i> Change Password
                    </a>
                </div>
            </div>
            
        
            </div>
            
            <div class="logout-section">
                <a href="logout.php" class="btn-logout">
                    <i class="bi bi-box-arrow-right me-2"></i>Logout
                </a>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add animation to action cards on scroll
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -100px 0px'
    };
    
    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, observerOptions);
    
    document.querySelectorAll('.action-card').forEach(card => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        card.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
        observer.observe(card);
    });
    
    // Add search functionality to the table
    const tableRows = document.querySelectorAll('.table-custom tbody tr');
    const memberCount = document.querySelector('.member-count');
    
    // Create search input
    const searchContainer = document.createElement('div');
    searchContainer.className = 'mb-3';
    searchContainer.innerHTML = `
        <div class="input-group">
            <span class="input-group-text"><i class="bi bi-search"></i></span>
            <input type="text" class="form-control" placeholder="Search members..." id="memberSearch">
        </div>
    `;
    
    // Insert search input before the table
    document.querySelector('.table-container').parentNode.insertBefore(searchContainer, document.querySelector('.table-container'));
    
    // Add search functionality
    document.getElementById('memberSearch').addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        let visibleCount = 0;
        
        tableRows.forEach(row => {
            const text = row.textContent.toLowerCase();
            if (text.includes(searchTerm)) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });
        
        memberCount.textContent = `${visibleCount} Members`;
    });
});
</script>
</body>
</html>