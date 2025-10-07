<?php
session_start();
require_once '../vendor/autoload.php';

use Lourdian\BasicStudent\Model\Admin;

$admin = new Admin();
$message = '';
$userId = null;

// ✅ Get user from URL
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $userId = (int) $_GET['id'];
    $student = $admin->getStudentById($userId);
    if (!$student) {
        $message = "❌ Invalid or missing user.";
        $userId = null;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 🧠 Use hidden field
    $userId = (int)($_POST['user_id'] ?? 0);
    $newPassword = trim($_POST['new_password'] ?? '');

    if ($userId && $admin->resetUserPassword($userId, $newPassword)) {
        $message = "✅ Password reset successfully for user ID #$userId!";
    } else {
        $message = "❌ Failed to reset password. User not found or error occurred.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Reset User Password</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    /* ✨ your full existing CSS — unchanged */
    * {margin:0;padding:0;box-sizing:border-box;}
    body {
      font-family:'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background:linear-gradient(135deg,#6a11cb 0%,#2575fc 100%);
      min-height:100vh;display:flex;justify-content:center;align-items:center;padding:20px;
    }
    .container {max-width:450px;width:100%;background:rgba(255,255,255,0.95);border-radius:20px;
      box-shadow:0 20px 40px rgba(0,0,0,0.2);padding:40px;backdrop-filter:blur(10px);
      transition:transform 0.3s ease, box-shadow 0.3s ease;animation:fadeIn 0.5s ease-out;}
    .container:hover {transform:translateY(-5px);box-shadow:0 25px 50px rgba(0,0,0,0.25);}
    @keyframes fadeIn {from{opacity:0;transform:translateY(20px);}to{opacity:1;transform:translateY(0);}}
    h2 {text-align:center;color:#333;margin-bottom:30px;font-size:28px;display:flex;align-items:center;justify-content:center;gap:10px;}
    h2 i {color:#6a11cb;}
    .form-group {margin-bottom:25px;}
    label {display:block;margin-bottom:8px;color:#555;font-weight:600;font-size:14px;}
    .input-container {position:relative;}
    .input-container i {position:absolute;left:15px;top:50%;transform:translateY(-50%);color:#6a11cb;}
    input {width:100%;padding:15px 15px 15px 45px;border:2px solid #e0e0e0;border-radius:10px;font-size:16px;
      transition:all 0.3s ease;background-color:#f9f9f9;}
    input:focus {outline:none;border-color:#6a11cb;background-color:#fff;box-shadow:0 0 0 3px rgba(106,17,203,0.1);}
    button {width:100%;padding:15px;background:linear-gradient(135deg,#6a11cb 0%,#2575fc 100%);color:white;
      border:none;border-radius:10px;font-size:16px;font-weight:600;cursor:pointer;transition:all 0.3s ease;margin-top:10px;
      display:flex;align-items:center;justify-content:center;gap:10px;}
    button:hover {transform:translateY(-2px);box-shadow:0 10px 20px rgba(106,17,203,0.3);}
    .message {padding:15px;margin-bottom:25px;border-radius:10px;text-align:center;font-weight:500;animation:slideDown 0.3s ease-out;}
    @keyframes slideDown {from{opacity:0;transform:translateY(-10px);}to{opacity:1;transform:translateY(0);}}
    .success {background:linear-gradient(135deg,#d4edda 0%,#c3e6cb 100%);color:#155724;border:1px solid #c3e6cb;}
    .error {background:linear-gradient(135deg,#f8d7da 0%,#f5c6cb 100%);color:#721c24;border:1px solid #f5c6cb;}
    .back-link {text-align:center;margin-top:25px;}
    .back-link a {color:#6a11cb;text-decoration:none;font-weight:600;display:inline-flex;align-items:center;gap:8px;}
  </style>
</head>
<body>
  <div class="container">
    <h2><i class="fas fa-key"></i> Reset User Password</h2>

    <?php if ($message): ?>
      <div class="message <?= strpos($message, '✅') !== false ? 'success' : 'error' ?>">
        <?= htmlspecialchars($message) ?>
      </div>
    <?php endif; ?>

    <?php if ($userId): ?>
      <form method="POST" id="resetForm">
          <input type="hidden" name="user_id" value="<?= htmlspecialchars($userId) ?>">

          <div class="form-group">
              <label for="new_password">New Password:</label>
              <div class="input-container">
                  <i class="fas fa-lock"></i>
                  <input type="password" id="new_password" name="new_password" required placeholder="Enter new password">
              </div>
          </div>

          <button type="submit">
              <i class="fas fa-sync-alt"></i>
              Reset Password
          </button>
      </form>
    <?php else: ?>
      <p style="text-align:center;">❌ Invalid user. <a href="dashboard.php">Back to Dashboard</a></p>
    <?php endif; ?>

    <div class="back-link">
      <a href="dashboard.php"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>
    </div>
  </div>
</body>
</html>
.