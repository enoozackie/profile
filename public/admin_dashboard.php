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

// Check admin session - FIXED: Consistent role checking
if (!isset($_SESSION['id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

require __DIR__ . '/../vendor/autoload.php';
use Lourdian\BasicStudent\Model\Admin;

 $admin = new Admin();

// Handle delete request with CSRF protection
if (isset($_GET['delete'])) {
    // Verify it's a valid integer
    $deleteId = filter_var($_GET['delete'], FILTER_VALIDATE_INT);
    if ($deleteId === false || $deleteId <= 0) {
        $_SESSION['error'] = "Invalid student ID";
        header("Location: admin_dashboard.php");
        exit;
    }
    
    // Confirm deletion
    if ($admin->deleteStudent($deleteId)) {
        $_SESSION['success'] = "Student deleted successfully";
    } else {
        $_SESSION['error'] = "Failed to delete student";
    }
    header("Location: admin_dashboard.php");
    exit;
}

// Fetch all registered users
 $students = $admin->getAllStudents();
 $studentCount = $admin->countStudents();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Dashboard - Student Portal</title>
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
    background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
    min-height: 100vh;
    color: var(--text-primary);
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
    background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
    box-shadow: var(--shadow-lg);
    padding: 1rem 0;
}

.navbar-brand {
    font-weight: 600;
    font-size: 1.5rem;
}

.navbar-text {
    font-weight: 500;
}

/* Main Container */
.main-container {
    max-width: 1400px;
    margin: 0 auto;
    padding: 2rem;
}

/* Statistics Cards */
.stats-container {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.stats-card {
    background: var(--bg-primary);
    border-radius: 16px;
    box-shadow: var(--shadow-md);
    padding: 1.5rem;
    transition: var(--transition);
    position: relative;
    overflow: hidden;
}

.stats-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 5px;
    background: linear-gradient(90deg, var(--primary-color), var(--secondary-color));
}

.stats-card:hover {
    transform: translateY(-5px);
    box-shadow: var(--shadow-xl);
}

.stats-card .card-icon {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 1rem;
}

.stats-card.primary .card-icon {
    background: rgba(99, 102, 241, 0.1);
    color: var(--primary-color);
}

.stats-card.success .card-icon {
    background: rgba(16, 185, 129, 0.1);
    color: var(--success-color);
}

.stats-card.info .card-icon {
    background: rgba(59, 130, 246, 0.1);
    color: var(--info-color);
}

.stats-card h3 {
    font-size: 2rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
}

.stats-card p {
    color: var(--text-secondary);
    margin-bottom: 0;
}

/* Students Table Card */
.table-card {
    background: var(--bg-primary);
    border-radius: 16px;
    box-shadow: var(--shadow-md);
    overflow: hidden;
}

.table-header {
    padding: 1.5rem;
    border-bottom: 1px solid var(--border-color);
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 1rem;
}

.table-title {
    font-size: 1.25rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    margin: 0;
}

.table-title i {
    margin-right: 0.5rem;
    color: var(--primary-color);
}

/* Search Bar */
.search-container {
    position: relative;
    width: 300px;
}

.search-input {
    width: 100%;
    padding: 0.75rem 1rem 0.75rem 2.5rem;
    border: 2px solid var(--border-color);
    border-radius: 50px;
    font-size: 0.9rem;
    transition: var(--transition);
    background: var(--bg-secondary);
}

.search-input:focus {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
    outline: none;
    background: var(--bg-primary);
}

.search-icon {
    position: absolute;
    left: 1rem;
    top: 50%;
    transform: translateY(-50%);
    color: var(--text-secondary);
}

/* Table Styles */
.table-container {
    padding: 1.5rem;
    overflow-x: auto;
}

.data-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
}

.data-table thead th {
    background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
    color: white;
    font-weight: 600;
    text-align: left;
    padding: 1rem;
    border: none;
    position: sticky;
    top: 0;
    z-index: 10;
}

.data-table thead th:first-child {
    border-top-left-radius: 8px;
}

.data-table thead th:last-child {
    border-top-right-radius: 8px;
}

.data-table tbody tr {
    transition: var(--transition);
}

.data-table tbody tr:hover {
    background-color: rgba(99, 102, 241, 0.05);
}

.data-table tbody td {
    padding: 1rem;
    border-bottom: 1px solid var(--border-color);
    vertical-align: middle;
}

.data-table tbody tr:last-child td {
    border-bottom: none;
}

/* Action Buttons */
.action-btn {
    padding: 0.5rem 0.75rem;
    border-radius: 8px;
    font-size: 0.875rem;
    font-weight: 500;
    display: inline-flex;
    align-items: center;
    text-decoration: none;
    transition: var(--transition);
    margin-right: 0.5rem;
}

.action-btn i {
    margin-right: 0.25rem;
}

.btn-edit {
    background: rgba(59, 130, 246, 0.1);
    color: var(--info-color);
}

.btn-edit:hover {
    background: var(--info-color);
    color: white;
}

.btn-delete {
    background: rgba(239, 68, 68, 0.1);
    color: var(--error-color);
}

.btn-delete:hover {
    background: var(--error-color);
    color: white;
}

.btn-add {
    background: var(--success-color);
    color: white;
    padding: 0.75rem 1.25rem;
    border-radius: 50px;
    font-weight: 500;
    display: inline-flex;
    align-items: center;
    text-decoration: none;
    transition: var(--transition);
}

.btn-add:hover {
    background: #059669;
    transform: translateY(-2px);
    box-shadow: var(--shadow-md);
    color: white;
}

.btn-add i {
    margin-right: 0.5rem;
}

/* Empty State */
.empty-state {
    text-align: center;
    padding: 3rem 1rem;
    color: var(--text-secondary);
}

.empty-state i {
    font-size: 3rem;
    margin-bottom: 1rem;
    opacity: 0.5;
}

/* Alert Messages */
.alert-custom {
    border-radius: 12px;
    padding: 1rem 1.5rem;
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

/* No Results Message */
.no-results {
    text-align: center;
    padding: 2rem;
    color: var(--text-secondary);
    background: var(--bg-secondary);
    border-radius: 8px;
    margin: 1rem 0;
}

.no-results i {
    font-size: 2rem;
    margin-bottom: 0.5rem;
    display: block;
}

/* Responsive Design */
@media (max-width: 768px) {
    .main-container {
        padding: 1rem;
    }
    
    .table-header {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .search-container {
        width: 100%;
    }
    
    .stats-container {
        grid-template-columns: 1fr;
    }
    
    .data-table {
        font-size: 0.875rem;
    }
    
    .data-table thead th,
    .data-table tbody td {
        padding: 0.75rem 0.5rem;
    }
    
    .action-btn {
        padding: 0.375rem 0.5rem;
        font-size: 0.75rem;
        margin-right: 0.25rem;
    }
}

/* Animations */
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
    <div class="container-fluid">
        <span class="navbar-brand mb-0 h1">
            <i class="bi bi-speedometer2 me-2"></i>Admin Dashboard
        </span>
        <div class="d-flex align-items-center">
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
    <!-- Flash Messages -->
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-custom alert-success-custom">
            <i class="bi bi-check-circle-fill"></i>
            <span><?= htmlspecialchars($_SESSION['success']) ?></span>
        </div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-custom alert-danger-custom">
            <i class="bi bi-exclamation-triangle-fill"></i>
            <span><?= htmlspecialchars($_SESSION['error']) ?></span>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <!-- Statistics Cards -->
    <div class="stats-container">
        <div class="stats-card primary">
            <div class="card-icon">
                <i class="bi bi-people-fill fs-2"></i>
            </div>
            <h3><?= $studentCount ?></h3>
            <p>Total Students</p>
        </div>
        <div class="stats-card success">
            <div class="card-icon">
                <i class="bi bi-person-check-fill fs-2"></i>
            </div>
            <h3><?= count($students) ?></h3>
            <p>Active Users</p>
        </div>
        <div class="stats-card info">
            <div class="card-icon">
                <i class="bi bi-gear-fill fs-2"></i>
            </div>
            <h3>Admin</h3>
            <p>Your Role</p>
        </div>
    </div>

    <!-- Students Table -->
    <div class="table-card">
        <div class="table-header">
            <h5 class="table-title">
                <i class="bi bi-list-ul"></i>Registered Students
            </h5>
            <div class="d-flex align-items-center gap-3">
                <div class="search-container">
                    <i class="bi bi-search search-icon"></i>
                    <input type="text" class="search-input" id="studentSearch" placeholder="Search students...">
                </div>
                <a href="admin_register.php" class="btn-add">
                    <i class="bi bi-person-plus-fill"></i>Add Admin
                </a>
            </div>
        </div>
        <div class="table-container">
            <div class="table-responsive">
                <table class="table data-table" id="studentsTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Fullname</th>
                            <th>Username</th>
                            <th>Address</th>
                            <th>Birthday</th>
                            <th>Contact</th>
                            <th>Sex</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($students)): ?>
                            <?php foreach ($students as $student): ?>
                                <tr>
                                    <td><?= $student['id'] ?></td>
                                    <td><?= htmlspecialchars($student['fullname']) ?></td>
                                    <td><?= htmlspecialchars($student['username']) ?></td>
                                    <td><?= htmlspecialchars($student['address']) ?></td>
                                    <td><?= htmlspecialchars($student['birthday']) ?></td>
                                    <td><?= htmlspecialchars($student['contact']) ?></td>
                                    <td><?= htmlspecialchars($student['sex']) ?></td>
                                    <td>
                                        <a class="action-btn btn-edit" href="edit_student.php?id=<?= $student['id'] ?>">
                                            <i class="bi bi-pencil-square"></i> Edit
                                        </a>
                                        <a class="action-btn btn-delete" href="?delete=<?= $student['id'] ?>" 
                                           onclick="return confirm('Are you sure you want to delete <?= htmlspecialchars(addslashes($student['fullname'])) ?>?')">
                                            <i class="bi bi-trash"></i> Delete
                                        </a>
                                        <a class="action-btn btn-edit" href="reset_user_password.php?id=<?= $student['id'] ?>">
    <i class="bi bi-key-fill"></i> Reset Password
</a>

                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="empty-state">
                                    <i class="bi bi-inbox"></i>
                                    <p>No registered students yet.</p>
                                </td>
                            </tr>   
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <div class="no-results d-none" id="noResults">
                <i class="bi bi-search"></i>
                <p>No students found matching your search.</p>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('studentSearch');
    const studentsTable = document.getElementById('studentsTable');
    const tableRows = studentsTable.querySelectorAll('tbody tr');
    const noResults = document.getElementById('noResults');
    
    // Function to filter table rows based on search input
    function filterTable() {
        const searchTerm = searchInput.value.toLowerCase();
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
        
        // Show/hide no results message
        if (visibleCount === 0 && searchTerm !== '') {
            noResults.classList.remove('d-none');
            studentsTable.style.display = 'none';
        } else {
            noResults.classList.add('d-none');
            studentsTable.style.display = '';
        }
    }
    
    // Add event listener to search input
    searchInput.addEventListener('input', filterTable);
    
    // Add animation to stats cards on scroll
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
    
    document.querySelectorAll('.stats-card').forEach(card => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        card.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
        observer.observe(card);
    });
});
</script>
</body>
</html>