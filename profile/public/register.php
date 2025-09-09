<?php
require __DIR__ . "/../vendor/autoload.php";

use Lourdian\BasicStudent\Model\User;

session_start();

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
<html>
<head>
    <title>Sign Up</title>
</head>
<body>
<h2>Sign Up</h2>

<form method="post">
    <input type="text" name="username" placeholder="Username" required><br><br>
    <input type="password" name="password" placeholder="Password" required><br><br>
    <input type="text" name="fullname" placeholder="Full Name"><br><br>
    <input type="text" name="address" placeholder="Address"><br><br>
    <input type="date" name="birthday"><br><br>
    <input type="text" name="contact" placeholder="Contact"><br><br>
    <select name="sex">
        <option value="">Select Sex</option>
        <option value="Male">Male</option>
        <option value="Female">Female</option>
    </select><br><br>
    <button type="submit">Register</button>
</form>

<?php if (!empty($error)) echo "<p style='color:red;'>$error</p>"; ?>

<p>Already have an account? <a href="login.php">Login</a></p>
</body>
</html>
