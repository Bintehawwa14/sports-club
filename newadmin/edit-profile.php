<?php 
session_start();
include_once('../include/db_connect.php');
if (!isset($_SESSION['adminid']))  {
    header('location:logout.php');
    exit;
} else {
    // Debug line - remove in production
    echo "Session AdminID: " . $_SESSION['adminid']; 

    if (isset($_POST['update'])) {
        $email = $_POST['email'];
        $fname = $_POST['fname'];
        $lname = $_POST['lname'];
        $contactno = $_POST['contact'];
        $adminID = $_SESSION['adminid']; // Corrected variable name

        $update_success = mysqli_query($con, "UPDATE users SET fname='$fname', lname='$lname', email='$email', contactno='$contactno' WHERE id=$adminID");

        if ($update_success) {
            echo "<script>alert('Profile updated successfully!');</script>";
            echo "<script>window.location.href = 'profile.php';</script>";
        } else {
            echo "<script>alert('Profile update failed.');</script>";
            // Add error details for debugging
            echo "<script>console.error('MySQL Error: " . mysqli_error($con) . "');</script>";
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
    <title>Edit Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css" rel="stylesheet" />
    <link href="css/styles.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/js/all.min.js" crossorigin="anonymous"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f7f7f7;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 80%;
            max-width: 900px;
            margin: 50px auto;
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
            color: #333;
            font-size: 24px;
            margin-bottom: 30px;
        }

        .card {
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .card-body {
            padding: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th, td {
            padding: 10px;
            text-align: left;
        }

        th {
            font-weight: bold;
            color: #333;
        }

        td input, td select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 14px;
            box-sizing: border-box;
        }

        td input:focus, td select:focus {
            border-color: #007bff;
            outline: none;
        }

        button {
            background-color: #007bff;
            color: #fff;
            border: none;
            padding: 10px 20px;
            font-size: 16px;
            cursor: pointer;
            width: 100%;
            border-radius: 5px;
        }

        .alert {
            padding: 10px;
            background-color: #f44336;
            color: white;
            margin-bottom: 20px;
            border-radius: 5px;
            text-align: center;
        }

        .alert.success {
            background-color: #4CAF50;
        }
        
        /* Additional styling for the admin interface */
        .sb-nav-fixed {
            padding-top: 56px;
        }
        
        #layoutSidenav_content {
            padding: 20px;
        }
    </style>
</head>
<body class="sb-nav-fixed">
    <?php include_once('includes/navbar.php');?>
    <div id="layoutSidenav">
        <?php include_once('includes/sidebar.php');?>
        <div id="layoutSidenav_content">
            <main>
                <div class="container-fluid px-4">
                    <?php 
                    $adminid = $_SESSION['adminid'];
                    $query = mysqli_query($con, "SELECT * FROM users WHERE id='$adminid'");
                    while($result = mysqli_fetch_array($query)) {
                    ?>
                    <h1 class="mt-4"><?php echo htmlspecialchars($result['fname']); ?>'s Profile</h1>
                    <div class="card mb-4">
                        <form method="post">
                            <div class="card-body">
                                <table class="table table-bordered">
                                    <tr>
                                        <th>First Name</th>
                                        <td>
                                            <input class="form-control" id="fname" name="fname" type="text" 
                                                value="<?php echo htmlspecialchars($result['fname']); ?>" required />
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Last Name</th>
                                        <td>
                                            <input class="form-control" id="lname" name="lname" type="text" 
                                                value="<?php echo htmlspecialchars($result['lname']); ?>" required />
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Contact No.</th>
                                        <td colspan="3">
                                            <input class="form-control" id="contact" name="contact" type="text" 
                                                value="<?php echo htmlspecialchars($result['contactno']); ?>"  
                                                pattern="[0-9]{11}" title="11 numeric characters starting with 03"  
                                                maxlength="11" required />
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Email</th>
                                        <td colspan="3">
                                            <input class="form-control" id="email" name="email" type="email" 
                                                value="<?php echo htmlspecialchars($result['email']); ?>" required />
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="4" style="text-align:center;">
                                            <button type="submit" class="btn btn-primary btn-block" name="update">Update</button>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </form>
                    </div>
                    <?php } ?>
                </div>
            </main>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="js/scripts.js"></script>
    <script>
        // Add form validation for contact number to ensure it starts with 03
        document.addEventListener('DOMContentLoaded', function() {
            const contactInput = document.getElementById('contact');
            const form = document.querySelector('form');
            
            form.addEventListener('submit', function(e) {
                if (contactInput.value && !contactInput.value.startsWith('03')) {
                    e.preventDefault();
                    alert('Contact number must start with 03 and be 11 digits long.');
                    contactInput.focus();
                }
            });
        });
    </script>
</body>
</html>
<?php } ?>