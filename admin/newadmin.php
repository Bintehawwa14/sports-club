<?php
session_start();
include_once('../include/db_connect.php');
   if(isset($_POST['submit']))
{
    
    $fname = mysqli_real_escape_string($con, $_POST['fname']);
    $lname = mysqli_real_escape_string($con, $_POST['lname']);
    $email = mysqli_real_escape_string($con, $_POST['email']);
    $password = $_POST['password'];
    $contact = mysqli_real_escape_string($con, $_POST['contact']);
  
   

    // Hash the password before storing
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
      $role = 'newadmin';

    // Check if email already exists
    $sql = mysqli_query($con, "SELECT id FROM users WHERE email='$email'");
    $row = mysqli_num_rows($sql);

    if ($row > 0) {
        echo "<script>alert('Email already exists! Try another email.');</script>";
    } else {
        // Insert user into database
        $stmt = $con->prepare("INSERT INTO users (fname, lname, email, password, contactno, role, is_approved) VALUES (?, ?, ?, ?, ?, ?, 1)");
$stmt->bind_param("ssssss", $fname, $lname, $email, $hashed_password, $contact, $role);


        if ($stmt->execute()) {
            echo "<script>alert('New Admin successfully Added!');</script>";
            echo "<script>window.location.href = 'admin/dashboard.php';</script>";
        } else {
            echo "<script>alert('Registration failed. Try again!');</script>";
        }

        $stmt->close();
    }

    $con->close();
}
?>


<!-- HTML Form -->
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="" />
        <meta name="author" content="" />
        <title>Add new admin</title>
        <link href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css" rel="stylesheet" />
        <link href="../css/styles.css" rel="stylesheet" />
        <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/js/all.min.js" crossorigin="anonymous"></script>
        <script>
            // Client-Side Validation
            function validateForm() {
                let fname = document.getElementById("fname").value;
                let lname = document.getElementById("lname").value;
                let email = document.getElementById("email").value;
                let contact = document.getElementById("contact").value;
                let password = document.getElementById("pswd").value;
                let valid = true;

                // Clear previous error messages
                document.getElementById("fname-error").innerHTML = "";
                document.getElementById("lname-error").innerHTML = "";
                document.getElementById("email-error").innerHTML = "";
                document.getElementById("contact-error").innerHTML = "";
                document.getElementById("password-error").innerHTML = "";

                // First Name Validation (Only Letters)
                if (!/^[a-zA-Z]+$/.test(fname)) {
                    document.getElementById("fname-error").innerHTML = "First name should only contain letters.";
                    valid = false;
                }

                // Last Name Validation (Only Letters)
                if (!/^[a-zA-Z]+$/.test(lname)) {
                    document.getElementById("lname-error").innerHTML = "Last name should only contain letters.";
                    valid = false;
                }

                // Email Validation (Gmail Only)
                if (!/^[a-zA-Z0-9._%+-]+@gmail\.com$/.test(email)) {
                    document.getElementById("email-error").innerHTML = "Please enter a valid Gmail address.";
                    valid = false;
                }

                // Contact Number Validation (10-15 Digits)
                if (!/^\d{10,15}$/.test(contact)) {
                    document.getElementById("contact-error").innerHTML = "Please enter a valid contact number (10-15 digits).";
                    valid = false;
                }

                // Password Validation (Not empty)
                if (password.trim() === "") {
                    document.getElementById("password-error").innerHTML = "Password cannot be empty.";
                    valid = false;
                }

                return valid;
            }
        </script>
</head>
<body class="sb-nav-fixed">
<?php include_once('includes/navbar.php'); ?>
    <h2> Admin Dashboard</h2>
    <div id="layoutSidenav">
        <div id="layoutSidenav_nav">
            <?php include('includes/sidebar.php'); ?>
           
        <div id="layoutSidenav_content">
            <main>
            <div class="container-fluid px-4">
            <h1 class="text-center my-4">Add a new admin</h1>

                    <div class="card mb-4">
                        <div class="card-body">
                        <?php if (isset($message)) echo "<p><strong>$message</strong></p>"; ?>

                        <form method="POST" action="" class="custom-form" onsubmit="return validateForm()">
    <div class="form-group">
        <label for="fname">First Name</label>
        <input type="text" class="form-control" id="fname" name="fname" placeholder="Enter first name" required>
        <small id="fname-error" style="color:red;"></small>
    </div>

    <div class="form-group">
        <label for="lname">Last Name</label>
        <input type="text" class="form-control" id="lname" name="lname" placeholder="Enter last name" required>
        <small id="lname-error" style="color:red;"></small>
    </div>

    <div class="form-group">
        <label for="email">Email</label>
        <input type="email" class="form-control" id="email" name="email" placeholder="e.g. admin@example.com" required>
        <small id="email-error" style="color:red;"></small>
    </div>

    <div class="form-group">
        <label for="contact">Contact Number</label>
        <input type="tel" class="form-control" id="contact" name="contact" placeholder="e.g. 03001234567" required>
        <small id="contact-error" style="color:red;"></small>
    </div>

    <div class="form-group">
        <label for="pswd">Password</label>
        <input type="password" class="form-control" id="pswd" name="password" placeholder="Create a password" required>
        <small id="password-error" style="color:red;"></small>
    </div>
    
    <div class="form-group text-end">
        <button type="submit" class="btn btn-primary custom-btn" name="submit">Add Admin</button>
    </div>
</form>

   
        </div>
    </div>
    <script src="../js/scripts.js"></script>
</body>
</html>
<style>
/* Custom Form Styling */
body{
margin: 0;
        padding: 0;
        background-image: url('../images/volleyballform.jpg');
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        font-family: Arial, sans-serif;}
        
.text-center {
    text-align: center;
}

.my-4 {
    margin-top: 1.5rem !important;
    margin-bottom: 1.5rem !important;
}

.custom-form {
    background-color: #f8f9fa;
    padding: 30px;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    max-width: 600px;
    margin: 0 auto;
}

/* Form Inputs Styling */
.custom-form .form-group {
    margin-bottom: 20px;
}

.custom-form label {
    font-weight: 600;
    font-size: 16px;
    color: #333;
}

.custom-form input {
    font-size: 14px;
    padding: 12px;
    border: 2px solid #ddd;
    border-radius: 8px;
    width: 100%;
    transition: border 0.3s ease;
}

.custom-form input:focus {
    border: 2px solid #007bff;
    box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
    outline: none;
}

/* Button Styling */
.custom-btn {
    padding: 10px 30px;
    border-radius: 30px;
    font-size: 16px;
    transition: background-color 0.3s ease, transform 0.3s ease;
}

.custom-btn:hover {
    background-color: #0056b3;
    transform: translateY(-3px);
}

.custom-btn:active {
    transform: translateY(1px);
}

/* Validation Error Styling */
small {
    font-size: 12px;
    font-style: italic;
}
</style>