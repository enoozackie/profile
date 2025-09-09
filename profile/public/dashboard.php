<?php
require __DIR__ . "/../vendor/autoload.php";

use Lourdian\BasicStudent\Model\User;

session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user = new User();
$currentUser = $user->getById($_SESSION['user_id']);

// Compute age from birthday
$age = "N/A";
if (!empty($currentUser['birthday'])) {
    $dob = new DateTime($currentUser['birthday']);
    $today = new DateTime();
    $age = $dob->diff($today)->y;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
</head>
<body>
<h2>Welcome, <?= htmlspecialchars($_SESSION['username']); ?> 👋</h2>
<a href="logout.php">Logout</a>

<h3>My Profile</h3>
<p><strong>Name:</strong> <?= htmlspecialchars($currentUser['fullname'] ?? 'N/A') ?></p>
<p><strong>Address:</strong> <?= htmlspecialchars($currentUser['address'] ?? 'N/A') ?></p>
<p><strong>Birthday:</strong> <?= htmlspecialchars($currentUser['birthday'] ?? 'N/A') ?></p>
<p><strong>Age:</strong> <?= $age ?></p>
<p><strong>Contact:</strong> <?= htmlspecialchars($currentUser['contact'] ?? 'N/A') ?></p>
<p><strong>Sex:</strong> <?= htmlspecialchars($currentUser['sex'] ?? 'N/A') ?></p>
</body>
</html>
