<?php
session_start();
require 'include/db_connect.php';

$error = "";
$success = "";

// Check if token is provided
if (!isset($_GET['token']) || empty(trim($_GET['token']))) {
    $error = "Invalid reset link. No token provided.";
} else {
    $token = trim($_GET['token']);

    // Verify token
    $query = "SELECT email, expires FROM password_resets WHERE token = ? AND expires > NOW()";
    $stmt = $con->prepare($query);
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $email = $row['email'];

        // If form submitted
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['reset_password'])) {
            $new_password = $_POST['password'];
            $confirm_password = $_POST['confirm_password'];

            if ($new_password === $confirm_password) {
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

                $update = $con->prepare("UPDATE users SET password=? WHERE email=?");
                $update->bind_param("ss", $hashed_password, $email);

                if ($update->execute()) {
                    // Remove token
                    $del = $con->prepare("DELETE FROM password_resets WHERE email=?");
                    $del->bind_param("s", $email);
                    $del->execute();

                    $success = "✅ Password reset successful. <a href='login.php'>Login here</a>";
                } else {
                    $error = "❌ Error updating password.";
                }
            } else {
                $error = "❌ Passwords do not match.";
            }
        }
    } else {
        $error = "Invalid or expired reset link.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Reset Password</title>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/water.css@2/out/water.css">
</head>
<body>
    <h1>Reset Password</h1>

    <?php if (!empty($error)): ?>
        <p style="color:red;"><?php echo $error; ?></p>
    <?php elseif (!empty($success)): ?>
        <p style="color:green;"><?php echo $success; ?></p>
    <?php else: ?>
        <form method="post">
            <label for="password">New Password</label>
            <input type="password" name="password" required>

            <label for="confirm_password">Confirm Password</label>
            <input type="password" name="confirm_password" required>

            <button type="submit" name="reset_password">Reset Password</button>
        </form>
    <?php endif; ?>
</body>
</html>
