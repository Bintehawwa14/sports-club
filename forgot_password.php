<?php
session_start();

require 'include/db_connect.php';       // must define $con (mysqli)
require 'vendor/autoload.php';          // PHPMailer via Composer

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$alert = ['type' => '', 'text' => ''];

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['forgot_password'])) {
    $email = isset($_POST['forgot_email']) ? trim($_POST['forgot_email']) : '';

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $alert = ['type' => 'danger', 'text' => 'Please enter a valid email address.'];
    } else {
        // Check if email exists (optional to avoid enumeration; keep if you want)
        $stmt = $con->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $exists = $stmt->get_result()->num_rows > 0;
        $stmt->close();

        // Always respond with generic message, but only generate token if user exists
        $alert = ['type' => 'success', 'text' => 'If this email exists, a reset link has been sent to your inbox.'];

        if ($exists) {
            // Generate secure token + expiry
            $token   = bin2hex(random_bytes(32));
            $expires = date("Y-m-d H:i:s", strtotime("+1 hour"));

            // Remove any old tokens for this email
            $del = $con->prepare("DELETE FROM password_resets WHERE email = ?");
            $del->bind_param("s", $email);
            $del->execute();
            $del->close();

            // Insert new token in your password_resets (id, email, token, expires, created_at)
            $ins = $con->prepare("INSERT INTO password_resets (email, token, expires) VALUES (?, ?, ?)");
            $ins->bind_param("sss", $email, $token, $expires);
            $ok  = $ins->execute();
            $ins->close();

            if ($ok) {
                // Build absolute reset link
                $scheme  = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
                $baseUrl = $scheme . '://' . $_SERVER['HTTP_HOST'] . rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
                $resetLink = $baseUrl . '/reset_password.php?token=' . urlencode($token);

                // Send email
                try {
                    /** If you have a mailer.php that returns a configured PHPMailer object, you can do:
                     *  $mail = require 'mailer.php';
                     *  Otherwise configure here:
                     */
                    $mail = new PHPMailer(true);
                    $mail->isSMTP();
                    $mail->Host       = 'smtp.gmail.com';
                    $mail->SMTPAuth   = true;
                    $mail->Username   = 'thegamemaker@gmail.com';   // <-- your Gmail
                    $mail->Password   = 'tiyelnjqtvscwgjb';   // <-- your 16-char App Password
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port       = 587;

                    $mail->setFrom('thegamemaker@gmail.com', 'the game maker');
                    $mail->addAddress($email);

                    $mail->isHTML(true);
                    $mail->Subject = 'Password Reset Request';
                    $mail->Body    = "
                        <p>We received a request to reset your password.</p>
                        <p><a href='{$resetLink}'>Click here to reset your password</a></p>
                        <p>This link will expire in 1 hour. If you didn’t request this, you can ignore this email.</p>
                    ";

                    // Optional plain-text alternative
                    $mail->AltBody = "Reset your password: {$resetLink} (expires in 1 hour)";

                    $mail->send();
                    // Keep generic success message (already set)
                } catch (Exception $e) {
                    // If email fails, you can log it; keep UI generic to avoid enumeration
                    error_log('PHPMailer error: ' . $mail->ErrorInfo);
                    // Optionally show detailed error during development:
                    // $alert = ['type' => 'danger', 'text' => 'Email error: ' . htmlspecialchars($mail->ErrorInfo)];
                }
            } else {
                // Token insert failed (DB issue)
                $alert = ['type' => 'danger', 'text' => 'Could not start password reset. Please try again.'];
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Forgot Password</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body{
      min-height:100vh; display:flex; align-items:center; justify-content:center;
      background: radial-gradient(1200px 600px at 10% 10%, #f0f5ff, #e9ecef 60%, #dee2e6);
    }
    .card{ border:none; border-radius:16px; box-shadow:0 12px 30px rgba(0,0,0,.08); }
    .brand{ font-weight:700; letter-spacing:.3px; }
    .btn-primary{ border-radius:10px; padding:.75rem; font-weight:600; }
    .form-control{ border-radius:10px; padding:.7rem .9rem; }
  </style>
</head>
<body>
  <div class="container" style="max-width:460px;">
    <div class="card p-4 p-md-5">
      
      <h5 class="mb-3 text-center">Forgot your password?</h5>
      <p class="text-muted text-center mb-4">Enter your email and we’ll send you a reset link.</p>

      <?php if (!empty($alert['type'])): ?>
        <div class="alert alert-<?php echo htmlspecialchars($alert['type']); ?> text-center" role="alert">
          <?php echo $alert['text']; ?>
        </div>
      <?php endif; ?>

      <form method="POST" novalidate>
        <div class="mb-3">
          <label for="forgot_email" class="form-label">Email address</label>
          <input type="email" class="form-control" id="forgot_email" name="forgot_email" required>
        </div>
        <button class="btn btn-primary w-100" type="submit" name="forgot_password">Send reset link</button>
      </form>

      <div class="text-center mt-3">
        <a href="login.php" class="small">Back to login</a>
      </div>
    </div>
  </div>
</body>
</html>
