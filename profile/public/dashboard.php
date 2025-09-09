<?php
require __DIR__ . "/../vendor/autoload.php";
use Lourdian\BasicStudent\Model\User;

// -------------------------------
// Prevent caching to block Back button after logout
// -------------------------------
header("Expires: Tue, 01 Jan 2000 00:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// -------------------------------
// Start session and check login
// -------------------------------
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// -------------------------------
// Fetch current user
// -------------------------------
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
