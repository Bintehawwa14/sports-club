<?php
session_start();
include_once('../include/db_connect.php');
if (!isset($_SESSION['userid'])) {
    header('location:logout.php');
    exit;
}

if (isset($_POST['update'])) {
    $email = $_POST['email'];
    $fname = $_POST['fname'];
    $lname = $_POST['lname'];
    $contactno = $_POST['contact'];
    $userID = $_SESSION['userid'];

    $update_success = mysqli_query($con, "UPDATE users SET fname='$fname', lname='$lname', email='$email', contactno='$contactno' WHERE id=$userID");

    if ($update_success) {
        echo "<script>alert('Profile updated successfully!');</script>";
        echo "<script>window.location.href = 'admin-profile.php';</script>";
    } else {
        echo "<script>alert('Profile update failed.');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="Edit Admin Profile Page" />
    <meta name="author" content="" />
    <title>Edit Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css" rel="stylesheet" />
    <link href="../css/styles.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/js/all.min.js" crossorigin="anonymous"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet" />
    <style>
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            margin: 0;
            padding: 0;
            background-image: url('../images/volleyballform.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            color: #333;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        body::before {
            content: "";
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: -1;
        }

        #layoutSidenav {
            display: flex;
            flex: 1;
        }

        #layoutSidenav_content {
            flex: 1;
            padding: 15px;
        }

        .container-fluid {
            max-width: 1200px;
            margin: 0 auto;
        }

        .profile-container {
            background-color: rgba(255, 255, 255, 0.95);
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
            margin: 20px auto;
            width: 95%;
            max-width: 1200px;
        }

        .profile-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .profile-header h1 {
            font-size: 24px;
            font-weight: 600;
            color: #1a3c6d;
            margin: 0;
            letter-spacing: 0.3px;
        }

        .table-responsive {
            margin-top: 15px;
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
        }

        th, td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #e0e0e0;
            font-size: 14px;
        }

        th {
            background-color: #f0f4f8;
            color: #1a3c6d;
            font-weight: 600;
        }

        td {
            color: #374151;
            background-color: #ffffff;
        }

        .form-control {
            width: 100%;
            padding: 8px;
            border: 1px solid #e0e0e0;
            border-radius: 5px;
            font-size: 14px;
            box-sizing: border-box;
            transition: border-color 0.3s ease;
        }

        .form-control:focus {
            border-color: #007bff;
            outline: none;
        }

        .btn-primary {
            background-color: #007bff;
            border: none;
            padding: 8px 15px;
            font-size: 14px;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        .btn-primary:hover {
            background-color: #0056b3;
        }

        .alert {
            padding: 10px;
            background-color: #fef2f2;
            color: #b91c1c;
            margin: 15px auto;
            border-radius: 5px;
            text-align: center;
            font-size: 13px;
            max-width: 800px;
        }

        @media (max-width: 768px) {
            .profile-container {
                width: 95%;
                padding: 15px;
                margin: 15px auto;
            }

            .profile-header h1 {
                font-size: 20px;
            }

            th, td {
                display: block;
                width: 100%;
                text-align: left;
                padding: 8px;
            }

            th {
                background-color: #e6eef8;
                border-bottom: none;
            }

            td {
                border-bottom: 1px solid #e0e0e0;
            }

            .form-control {
                font-size: 13px;
            }

            .btn-primary {
                font-size: 13px;
                padding: 6px 12px;
            }

            .container-fluid {
                padding: 10px;
            }
        }

        @media (max-width: 576px) {
            .profile-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }
“
        }
    </style>
</head>
<body class="sb-nav-fixed">
    <?php include_once('includes/navbar.php'); ?>
    <div id="layoutSidenav">
        <?php include_once('includes/sidebar.php'); ?>
        <div id="layoutSidenav_content">
            <main>
                <div class="container-fluid px-4">
                    <?php
                    $userid = $_SESSION['userid'];
                    $query = mysqli_query($con, "select * from users where id='$userid'");
                    while ($result = mysqli_fetch_array($query)) {
                    ?>
                    <div class="profile-container">
                        <div class="profile-header">
                            <h1><?php echo htmlspecialchars($result['fname']); ?>'s Profile</h1>
                        </div>
                        <div class="table-responsive">
                            <form method="post">
                                <table>
                                    <tbody>
                                        <tr>
                                            <th>First Name</th>
                                            <td><input class="form-control" id="fname" name="fname" type="text" value="<?php echo htmlspecialchars($result['fname']); ?>" required /></td>
                                        </tr>
                                        <tr>
                                            <th>Last Name</th>
                                            <td><input class="form-control" id="lname" name="lname" type="text" value="<?php echo htmlspecialchars($result['lname']); ?>" required /></td>
                                        </tr>
                                        <tr>
                                            <th>Contact No.</th>
                                            <td><input class="form-control" id="contact" name="contact" type="text" value="<?php echo htmlspecialchars($result['contactno']); ?>" pattern="[0-9]{10}" title="10 numeric characters only" maxlength="10" required /></td>
                                        </tr>
                                        <tr>
                                            <th>Email</th>
                                            <td><input class="form-control" id="email" name="email" type="text" value="<?php echo htmlspecialchars($result['email']); ?>" required /></td>
                                        </tr>
                                        <tr>
                                            <th>Reg. Date</th>
                                            <td><?php echo htmlspecialchars($result['posting_date']); ?></td>
                                        </tr>
                                        <tr>
                                            <td colspan="2" style="text-align:center;">
                                                <button type="submit" class="btn btn-primary btn-block" name="update">Update</button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </form>
                        </div>
                    </div>
                    <?php } ?>
                </div>
            </main>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="../js/scripts.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/simple-datatables@latest" crossorigin="anonymous"></script>
    <script src="../js/datatables-simple-demo.js"></script>
</body>
</html>