<?php 
include 'include/nav-bar.php';
session_start();
require 'include/db_connect.php';

if (isset($_POST['contactmo'])) {
    $contactno = $_POST['contactno'];
    $sql = "SELECT * FROM users WHERE contactno = '$contactno'";
    $result = mysqli_query($con, $sql);

    if (mysqli_num_rows($result) > 0) {
        echo "exists";
    } else {
        echo "available";
    }
}
if(isset($_POST['submit']))
{
    $fname = mysqli_real_escape_string($con, $_POST['fname']);
    $lname = mysqli_real_escape_string($con, $_POST['lname']);
    $email = mysqli_real_escape_string($con, $_POST['email']);
    $password = $_POST['password'];
    $contact = mysqli_real_escape_string($con, $_POST['contact']);
    $cnic = $_POST['cnic'];
    $club_college = $_POST['club_college'];

    // Hash the password before storing
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Check if email already exists
    $sql = mysqli_query($con, "SELECT id FROM users WHERE email='$email'");
    $row = mysqli_num_rows($sql);
    if ($row > 0) {
        echo "<script>alert('Email already exists! Try another email.');</script>";
    } else {
        // Insert user into database
        $stmt = $con->prepare("INSERT INTO users (fname, lname, email, password, contactno, cnic, club_college) VALUES (?, ?, ?, ?, ?,?,?)");
        $stmt->bind_param("sssssss", $fname, $lname, $email, $hashed_password, $contact, $cnic, $club_college);

        if ($stmt->execute()) {
            echo "<script>alert('Registered successfully!');</script>";
            echo "<script>window.location.href = 'login.php';</script>";
        } else {
            echo "<script>alert('Registration failed. Try again!');</script>";
        }

        $stmt->close();
    }

    $con->close();
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
    <title>User Signup </title>
    <link rel="icon" type="image/png" href="image/logo.png" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/js/all.min.js" crossorigin="anonymous"></script>
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
            background-color: rgba(0,0,0,0.5);  /* black overlay */
            z-index: -1;
        }
        .error {
            color: red;
            font-size: 14px;
            display: block;
            margin-top: 5px;
        }
        input.invalid {
            border: 1px solid red;
        }
        .card {
            border-radius: 15px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        .card-header {
            background: linear-gradient(45deg, #007bff, #0056b3);
            color: white;
            border-radius: 15px 15px 0 0 !important;
        }
        .btn-primary {
            background: linear-gradient(45deg, #007bff, #0056b3);
            border: none;
            border-radius: 8px;
            padding: 12px;
            font-weight: bold;
        }
        .btn-primary:hover {
            background: linear-gradient(45deg, #0056b3, #004494);
            transform: translateY(-2px);
            transition: all 0.3s;
        }
        .form-floating {
            margin-bottom: 15px;
        }
        .form-control {
            border-radius: 8px;
            padding: 16px;
            border: 1px solid #ced4da;
        }
        .form-control:focus {
            box-shadow: 0 0 0 0.25rem rgba(0, 123, 255, 0.25);
            border-color: #86b7fe;
        }
    </style>
</head>
<body style="background-color:white">
    <div id="layoutAuthentication">
        <div id="layoutAuthentication_content">
            <main>
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-lg-7">
                            <div class="card shadow-lg border-0 rounded-lg mt-5">
                                <div class="card-header">
                                    <h3 class="text-center font-weight-light my-4">Create Account</h3>
                                </div>
                                <div class="card-body">
                                    <form method="post" name="signup" id="signupForm">
                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <div class="form-floating mb-3 mb-md-0">
                                                    <input class="form-control" id="fname" name="fname" type="text"
                                                        placeholder="Enter your first name" required maxlength="11" />
                                                    <label for="fname">First name</label>
                                                    <span id="fnameError" class="error"></span>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-floating">
                                                    <input class="form-control" id="lname" name="lname" type="text"
                                                        placeholder="Enter your last name" required maxlength="11" />
                                                    <label for="lname">Last name</label>
                                                    <span id="lnameError" class="error"></span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-floating mb-3">
                                            <input class="form-control" id="email" name="email" type="email" 
                                                placeholder="example@gmail.com" required />
                                            <label for="inputEmail">Email address</label>
                                            <span id="emailError" class="error"></span>
                                        </div>
                                        
                                        <div class="form-floating mb-3">

                                                <input class="form-control" id="contactno" name="contactno" type="text" 
                                                    placeholder="03XXXXXXXXX" required maxlength="11" onblur="checkUniqueContact()" />

                                            <label for="inputcontact">Contact Number (03XXXXXXXXX)</label>
                                            <span id="contactError" class="error"></span>
                                        </div>
                                    
                                        <div class="form-floating mb-3">
                                            <input class="form-control" id="cnic" name="cnic" type="text" placeholder="CNIC" required/>
                                            <label for="cnic">CNIC (e.g 1234-5678999-8)</label>
                                            <span id="cnicError" class="error"></span>
                                        </div>

                                        <!-- Club/College Field -->
                                        <div class="form-floating mb-3">
                                            <input class="form-control" id="club_college" name="club_college" type="text" placeholder="Club / College Name" required maxlength="20"/>
                                            <label for="club_college">Club / College Name</label>
                                            <span id="clubCollegeError" class="error"></span>
                                        </div>

                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <div class="form-floating mb-3 mb-md-0">
                                                    <input class="form-control" id="password" name="password" type="password" placeholder="Create a password" 
                                                        pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{6,}" title="At least one number, one uppercase letter, one lowercase letter, and minimum 6 characters" required />
                                                    <label for="inputPassword">Password</label>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-floating mb-3 mb-md-0">
                                                    <input class="form-control" id="confirmpassword" name="confirmpassword" type="password"
                                                        placeholder="Confirm password" required />
                                                    <label for="inputPasswordConfirm">Confirm Password</label>
                                                    <span id="confirmPasswordError" class="error"></span>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="mt-4 mb-0">
                                            <div class="d-grid">
                                                <button type="submit" class="btn btn-primary btn-block" name="submit" id="submitBtn">Create Account</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                <div class="card-footer text-center py-3">
                                    <div class="small"><a href="login.php">Have an account? Go to login</a></div>
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
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('signupForm');
            
            // Add event listeners for real-time validation
            document.getElementById('fname').addEventListener('blur', function() {
                validateName('fname', 'fnameError');
            });
            
            document.getElementById('lname').addEventListener('blur', function() {
                validateName('lname', 'lnameError');
            });
            
            document.getElementById('email').addEventListener('blur', function() {
                validateEmail('email', 'emailError');
            });
            
            document.getElementById('contactno').addEventListener('blur', function() {
                validateContact('contactno', 'contactError');
        
            });
            
            document.getElementById('cnic').addEventListener('blur', function() {
                validateCNIC('cnic', 'cnicError');
            });
            
            document.getElementById('club_college').addEventListener('blur', function() {
                validateClubCollege('club_college', 'clubCollegeError');
            });
            
            document.getElementById('confirmpassword').addEventListener('blur', function() {
                validateConfirmPassword();
            });
            
            // Form submission validation
            form.addEventListener('submit', function(event) {
                let isValid = true;
                
                if (!validateName('fname', 'fnameError')) isValid = false;
                if (!validateName('lname', 'lnameError')) isValid = false;
                if (!validateEmail('email', 'emailError')) isValid = false;
                if (!validateContact('contactno', 'contactError')) isValid = false;
                if (!validateCNIC('cnic', 'cnicError')) isValid = false;
                if (!validateClubCollege('club_college', 'clubCollegeError')) isValid = false;
                if (!validateConfirmPassword()) isValid = false;
                
                if (!isValid) {
                    event.preventDefault();
                }
            });
            
            // Validation functions
            function validateName(inputId, errorId) {
                const name = document.getElementById(inputId).value.trim();
                const errorElement = document.getElementById(errorId);
                const regex = /^[A-Za-z]{3,10}$/; // Only letters, at least 3
                
                if (name === '') {
                    errorElement.textContent = 'This field is required.';
                    document.getElementById(inputId).classList.add('invalid');
                    return false;
                } else if (!regex.test(name)) {
                    errorElement.textContent = 'First Name must be 3-10 letters only';
                    document.getElementById(inputId).classList.add('invalid');
                    return false;
                } else {
                    errorElement.textContent = '';
                    document.getElementById(inputId).classList.remove('invalid');
                    return true;
                }
            }
            
            function validateEmail(inputId, errorId) {
                const email = document.getElementById(inputId).value.trim();
                const errorElement = document.getElementById(errorId);
                const regex = /^[a-z0-9._%+-]+@gmail\.com$/; // Must end with @gmail.com
                
                if (email === '') {
                    errorElement.textContent = 'Email is required.';
                    document.getElementById(inputId).classList.add('invalid');
                    return false;
                } else if (!regex.test(email)) {
                    errorElement.textContent = 'Please enter a valid Gmail address (e.g., example@gmail.com).';
                    document.getElementById(inputId).classList.add('invalid');
                    return false;
                } else {
                    errorElement.textContent = '';
                    document.getElementById(inputId).classList.remove('invalid');
                    return true;
                }
            }
            
           function validateContact(inputId, errorId) {
            const contact = document.getElementById(inputId).value.trim();
            const errorElement = document.getElementById(errorId);
            const regex = /^03\d{9}$/; // Must start with 03 and have 11 digits total

            if (contact === '') {
                errorElement.textContent = 'Contact number is required.';
                document.getElementById(inputId).classList.add('invalid');
                return false;
            } else if (!regex.test(contact)) {
                errorElement.textContent = 'Contact number must start with 03 and have 11 digits (e.g., 03123456789).';
                document.getElementById(inputId).classList.add('invalid');
                return false;
            } else {
                errorElement.textContent = '';
                document.getElementById(inputId).classList.remove('invalid');
                return true;
            }
}

            
            function validateCNIC(inputId, errorId) {
                const cnic = document.getElementById(inputId).value.trim();
                const errorElement = document.getElementById(errorId);
                const regex = /^\d{5}-\d{7}-\d{1}$/; // Format: 33333-3333333-3
                
                if (cnic === '') {
                    errorElement.textContent = 'CNIC is required.';
                    document.getElementById(inputId).classList.add('invalid');
                    return false;
                } else if (!regex.test(cnic)) {
                    errorElement.textContent = 'CNIC must be in the format: 33333-3333333-3.';
                    document.getElementById(inputId).classList.add('invalid');
                    return false;
                } else {
                    errorElement.textContent = '';
                    document.getElementById(inputId).classList.remove('invalid');
                    return true;
                }
            }
            
            function validateClubCollege(inputId, errorId) {
                const clubCollege = document.getElementById(inputId).value.trim();
                const errorElement = document.getElementById(errorId);
                const regex = /^[A-Za-z\s]{15,20}$/; // Only letters and spaces, minimum 3 characters
                
                if (clubCollege === '') {
                    errorElement.textContent = 'This field is required.';
                    document.getElementById(inputId).classList.add('invalid');
                    return false;
                } else if (!regex.test(clubCollege)) {
                    errorElement.textContent = 'Must contain only letters and spaces.';
                    document.getElementById(inputId).classList.add('invalid');
                    return false;
                } else {
                    errorElement.textContent = '';
                    document.getElementById(inputId).classList.remove('invalid');
                    return true;
                }
            }
            
            function validateConfirmPassword() {
                const password = document.getElementById('password').value;
                const confirmPassword = document.getElementById('confirmpassword').value;
                const errorElement = document.getElementById('confirmPasswordError');
                
                if (confirmPassword === '') {
                    errorElement.textContent = 'Please confirm your password.';
                    document.getElementById('confirmpassword').classList.add('invalid');
                    return false;
                } else if (password !== confirmPassword) {
                    errorElement.textContent = 'Passwords do not match.';
                    document.getElementById('confirmpassword').classList.add('invalid');
                    return false;
                } else {
                    errorElement.textContent = '';
                    document.getElementById('confirmpassword').classList.remove('invalid');
                    return true;
                }
            }
        });
    </script>
</body>
</html>