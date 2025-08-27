<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>
<?php
session_start(); // must be top
require 'include/db_connect.php';

// Forgot Password Handling
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['forgot_password'])) {
    $email = isset($_POST['forgot_email']) ? mysqli_real_escape_string($con, trim($_POST['forgot_email'])) : '';
    
    if (!empty($email)) {
        // Check if email exists
        $query = "SELECT id FROM users WHERE email = ?";
        $stmt = $con->prepare($query);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result && $result->num_rows > 0) {
            // Generate a unique token
            $token = bin2hex(random_bytes(16));
            $token_hash = hash("sha256", $token);
            $expiry = date("Y-m-d H:i:s", time() + 60 * 30); // 30 minutes from now
            
            // Update user record with token and expiry
            $update_query = "UPDATE users SET reset_token = ?, reset_token_expire = ? WHERE email = ?";
            $update_stmt = $con->prepare($update_query);
            $update_stmt->bind_param("sss", $token_hash, $expiry, $email);
            // After the token is generated and stored
require 'vendor/autoload.php';
$mail = require 'mailer.php';

$mail->setFrom("noreply@example.com", "The Game Maker");
$mail->addAddress($email);
$mail->Subject = "Password Reset";
$mail->Body = <<<END
Click <a href="$reset_link">here</a> to reset your password.
<br><br>
This link will expire in 30 minutes.
END;

try {
    $mail->send();
    $forgot_success = "Password reset link has been sent to your email.";
} catch (Exception $e) {
    $forgot_error = "Message could not be sent. Mailer error: {$mail->ErrorInfo}";
}
            
            if ($update_stmt->execute()) {
                // Send email with reset link
                $reset_link = "http://" . $_SERVER['HTTP_HOST'] . "/reset_password.php?token=" . $token;
                
                // For demonstration, we'll show the link in an alert
                // In production, you would send an actual email
                $forgot_success = "Password reset link has been generated. Check your email.";
                
                // In a real application, you would use a function like mail() or a library to send email
                // mail($email, "Password Reset Request", "Click here to reset your password: " . $reset_link);
            } else {
                $forgot_error = "Error generating reset token. Please try again.";
            }
        } else {
            // Don't reveal if email exists or not for security
            $forgot_success = "If this email exists in our system, a reset link will be sent.";
        }
    } else {
        $forgot_error = "Please enter your email address.";
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) { 
    $email = isset($_POST['email']) ? mysqli_real_escape_string($con, trim($_POST['email'])) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : ''; 
    $error = ""; 

    if (!empty($email) && !empty($password)) {
        // Fetch user by email
        $query = "SELECT * FROM users WHERE email = ?"; 
        $stmt = $con->prepare($query);
        $stmt->bind_param("s", $email); 
        $stmt->execute(); 
        $result = $stmt->get_result(); 

        if ($result && $result->num_rows > 0) { 
            $user = $result->fetch_assoc();

            // Verify password
            if (password_verify($password, $user['password'])) { 
                
                // Set session variables based on role
                $_SESSION['userid'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['email'] = $user['email'];
                 $_SESSION['user'] = $user; 

            // ✅ Role-based redirect
            if ($user['role'] === 'admin') {
                header("Location: admin/dashboard.php");
                exit();

            } elseif ($user['role'] === 'newadmin') {
                header("Location: newadmin/dashboard.php");
                exit();

            } elseif ($user['role'] === 'user') {
                header("Location: user/get_event.php");
                exit();

            } else {
                $error = "Invalid role detected.";
            }

        } else {
            $error = "Invalid password.";
        }

    } else {
        $error = "Invalid login credentials.";
    }

} else { 
    $error = "Please enter both email and password."; 
} 

// Show error and redirect
if (!empty($error)) { 
    echo "<script>alert('$error'); window.location.href='login.php';</script>"; 
}
}

?>
<!DOCTYPE html>
<html lang="en">
   
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="" />
        <meta name="author" content="" />
        <title>FG Kharian women sports club</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/js/all.min.js" crossorigin="anonymous"></script>
      
    </head>
      <?php require 'include/nav-bar.php'; ?>
    <body>
        <div id="layoutAuthentication">
            <div id="layoutAuthentication_content">
                <main>
                    <div class="container">
                        <div class="row justify-content-center">
                            <div class="col-lg-5">
                                <div class="card shadow-lg border-0 rounded-lg mt-5">

<div class="card-header">
<h2 align="center">Login</h2>
<hr />
    
                        <div class="card-body">
                                        
                            <form method="post">
                                            
                            <div class="form-floating mb-3">
                            <input class="form-control" name="email" type="email" placeholder="Email" required/>
                            <label for="inputEmail">Email address</label>
                            </div>
                                            
                            <div class="form-floating mb-3">
                                <div class="password-input-container">
                                    <input class="form-control" name="password" type="password" placeholder="Password" required id="passwordInput" />
                                    <span class="password-toggle" id="passwordToggle">
                                        <i class="fas fa-eye"></i>
                                    </span>
                                    <div class="timer-bar" id="timerBar"></div>
                                </div>
                              
                            </div>

                            <div class="d-flex align-items-center justify-content-between mt-4 mb-0">
                            <a class="small" href="forgot_password.php">Forgot Password?</a>
                            <button class="btn btn-primary" name="login" type="submit">Login</button>
                            </div>
                            </form>
                        </div>
                                  
                            <div class="card-footer text-center py-3">
                            <div class="small"><a href="signup.php">Need an account? Sign up!</a></div>
                            <div class="small"><a href="index.php">Back to Home</a></div>
                                        </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </main>
            </div>

        </div>
        
        

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
        <script src="js/scripts.js"></script>
        
        <style>
     body {
          margin: 0;
        padding: 0;
        background-image: url('images/tt.jpg');
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        font-family: Arial, sans-serif;
                
    }
    body::before {
        content: "";
        position: fixed;
        top: 0; left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0,0,0,0.5);
        z-index: -1;
    }
     
    .password-input-container {
        position: relative;
    }
    
    .password-toggle {
        position: absolute;
        right: 12px;
        top: 50%;
        transform: translateY(-50%);
        cursor: pointer;
        color: #6c757d;
        z-index: 10;
    }
    
    .password-toggle:hover {
        color: #0d6efd;
    }
    
    .timer-bar {
        position: absolute;
        bottom: 0;
        left: 0;
        height: 2px;
        width: 0;
        background-color: #0d6efd;
        transition: width 3s linear;
    }
    
    .form-floating > .form-control {
        padding-right: 40px;
    }
    
    /* Modal styling */
    .modal-content {
        border-radius: 10px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.3);
    }
    
    .modal-header {
        background: linear-gradient(45deg, #4a6baf, #2c3e50);
        color: white;
        border-radius: 10px 10px 0 0;
    }
    
    .btn-close {
        filter: invert(1);
    }
        </style>
        
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const passwordInput = document.getElementById('passwordInput');
                const passwordToggle = document.getElementById('passwordToggle');
                const timerBar = document.getElementById('timerBar');
                
                passwordToggle.addEventListener('click', function() {
                    if (passwordInput.type === 'password') {
                        // Show password
                        passwordInput.type = 'text';
                        passwordToggle.innerHTML = '<i class="fas fa-eye-slash"></i>';
                        
                    
                        
                        // Hide password after 3 seconds
                        setTimeout(function() {
                            passwordInput.type = 'password';
                            passwordToggle.innerHTML = '<i class="fas fa-eye"></i>';
                            timerBar.style.width = '0';
                        }, 3000);
                    } else {
                        // Hide password immediately if clicked again
                        passwordInput.type = 'password';
                        passwordToggle.innerHTML = '<i class="fas fa-eye"></i>';
                        timerBar.style.width = '0';
                    }
                });
                
                // Handle modal messages
                <?php if (isset($forgot_error) || isset($forgot_success)): ?>
                    var forgotModal = new bootstrap.Modal(document.getElementById('forgotPasswordModal'));
                    forgotModal.show();
                <?php endif; ?>
            });
        </script>
    </body>
    <?php require 'include/footer.php';?>
</html>