<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);?>
<?php
session_start(); // must be top
require 'include/db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") { 
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
                            <input class="form-control" name="password" type="password" placeholder="Password" required />
                            <label for="inputPassword">Password</label>
                            </div>


                            <div class="d-flex align-items-center justify-content-between mt-4 mb-0">
                            <a class="small" href="#.php">Forgot Password?</a>
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
    </body>
    <?php require 'include/footer.php';?>
</html>
<style>
     body {
          margin: 0;
        padding: 0;
        
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
 
  z-index: -1;
    }
     
    </style>