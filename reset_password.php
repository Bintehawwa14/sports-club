<?php
date_default_timezone_set('Asia/Karachi');
session_start();
include 'includes/db.php';
require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);

    $stmt = $conn->prepare("SELECT id, name FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();

        $token = bin2hex(random_bytes(32));
        $createdAt = date('Y-m-d H:i:s');
        $expiresAt = date('Y-m-d H:i:s', strtotime('+1 hour'));

        // Delete existing reset tokens
        $stmt = $conn->prepare("DELETE FROM password_resets WHERE user_id = ?");
        $stmt->bind_param("i", $user['id']);
        $stmt->execute();

        // Store token
        $stmt = $conn->prepare("INSERT INTO password_resets (user_id, token, created_at, expires_at) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isss", $user['id'], $token, $createdAt, $expiresAt);

        if ($stmt->execute()) {
            $resetLink = "http://{$_SERVER['HTTP_HOST']}/reset_password.php?token=" . $token;

            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = ''; // Gmail
                $mail->Password = ''; // App Password
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;

                $mail->setFrom('', 'Sports Management System');
                $mail->addAddress($email, $user['name']);
                $mail->isHTML(true);
                $mail->Subject = 'Password Reset Request';
                $mail->Body = "
                <div style='background:#f9f9f9;padding:20px;font-family:Arial;color:#333;'>
                    <div style='background:#fff;max-width:600px;margin:auto;padding:20px;border-radius:8px;box-shadow:0 2px 8px rgba(0,0,0,0.1);'>
                        <h2 style='color:#8B5E3C;text-align:center;'>Password Reset Request</h2>
                        <p>Hello {$user['name']},</p>
                        <p>We received a request to reset your password. Click the button below to reset it:</p>
                        <div style='text-align:center;margin:30px 0;'>
                            <a href='{$resetLink}' style='background:#8B5E3C;color:#fff;text-decoration:none;padding:12px 25px;border-radius:5px;display:inline-block;'>Reset Password</a>
                        </div>
                        <p>This link will expire in <strong>1 hour</strong>.</p>
                        <p>If you didn’t request this, please ignore this email.</p>
                        <p style='margin-top:30px;'>Regards,<br><strong>Sports Management System</strong></p>
                    </div>
                </div>";

                $mail->send();
                $message = "Password reset instructions have been sent to your email.";
                $messageType = "success";
            } catch (Exception $e) {
                $message = "Error sending email: " . $mail->ErrorInfo;
                $messageType = "danger";
            }
        } else {
            $message = "Error generating reset token. Please try again.";
            $messageType = "danger";
        }
    } else {
        $message = "No account found with this email.";
        $messageType = "danger";
    }
}

$sql = "CREATE TABLE IF NOT EXISTS password_resets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    token VARCHAR(64) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    expires_at DATETIME NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_token (token)
)";
$conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <link rel="icon" href="uploads/assests/book.png">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: #f0f2f5;
            margin: 0; display: flex;
            justify-content: center; align-items: center;
            height: 100vh;
        }
        .auth-container {
            background: #fff;
            width: 400px;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            text-align: center;
        }
        .auth-header h1 {
            margin: 0;
            font-size: 24px;
            color: #3c608bff;
        }
        .auth-header p {
            color: #666;
            margin-bottom: 20px;
        }
        .form-group { margin-bottom: 20px; text-align: left; }
        .form-group label { font-weight: 600; }
        .form-group input {
            width: 100%; padding: 10px; margin-top: 5px;
            border: 1px solid #ddd; border-radius: 6px; font-size: 14px;
        }
        .btn-auth {
            background: #194f5eff; color: white; border: none;
            padding: 10px 20px; border-radius: 6px; cursor: pointer;
            font-size: 16px; width: 100%; margin-top: 10px;
        }
        .btn-auth:hover { background: #2c636fff; }
        .btn-link-auth {
            display: inline-block; margin-top: 15px; text-decoration: none;
            color: #3c8b80ff; font-size: 14px;
        }
        .alert {
            padding: 10px; margin-bottom: 15px; border-radius: 6px; font-size: 14px;
        }
        .alert-success { background: #d4edda; color: #155724; }
        .alert-danger { background: #f8d7da; color: #721c24; }
        .auth-footer { margin-top: 15px; font-size: 12px; color: #999; }
    </style>
</head>
<body>
    <div class="auth-container">
        <div class="auth-header">
            <h1>Forgot Password</h1>
            <p>Reset your account password</p>
        </div>

        <?php if (!empty($message)): ?>
            <div class="alert alert-<?php echo $messageType; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" placeholder="Enter your registered email" required>
            </div>
            <button type="submit" class="btn-auth"><i class="fas fa-paper-plane"></i> Send Reset Instructions</button>
            <a href="index.php" class="btn-link-auth"><i class="fas fa-arrow-left"></i> Back to Login</a>
        </form>

        <div class="auth-footer">&copy; 2025 Sports Management System. All rights reserved.</div>
    </div>
</body>
</html>