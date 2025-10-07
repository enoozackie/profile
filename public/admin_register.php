<?php
require __DIR__ . '/../vendor/autoload.php';
use Lourdian\BasicStudent\Model\Auth;
  header("Expires: Tue, 01 Jan 2000 00:00:00 GMT");
    header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
    header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
    header("Cache-Control: post-check=0, pre-check=0", false);
    header("Pragma: no-cache");
session_start();
$auth = new Auth();
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $res = $auth->registerAdmin($_POST['username'], $_POST['password'], $_POST['fullname']);
    if ($res) $success = "Admin registered successfully!";
    else $error = "Username already exists.";
}
?>
<form method="POST">
    <input type="text" name="username" placeholder="Admin username" required>
    <input type="password" name="password" placeholder="Password" required>
    <input type="text" name="fullname" placeholder="Fullname" required>
    <button type="submit">Register Admin</button>
</form>
<?php if($error) echo $error; ?>
<?php if($success) echo $success; ?>
