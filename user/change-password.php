<?php
session_start();
include_once('../include/db_connect.php');

if (isset($_POST['update'])) {
    $oldpassword = $_POST['currentpassword'];  
    $newpassword = $_POST['newpassword'];

    $userid = $_SESSION['userid'];

    $sql = mysqli_query($con, "SELECT password FROM users WHERE id='$userid'");
    $user = mysqli_fetch_assoc($sql);

    if (password_verify($oldpassword, $user['password'])) {  
        $newpassword_hash = password_hash($newpassword, PASSWORD_DEFAULT);
        $ret = mysqli_query($con, "UPDATE users SET password='$newpassword_hash' WHERE id='$userid'");

        echo "<script>alert('Password Changed Successfully!');</script>";
        echo "<script type='text/javascript'>window.location.href='change-password.php';</script>";
    } else {
        echo "<script>alert('Old Password does not match!');</script>";
        echo "<script type='text/javascript'>window.location.href='change-password.php';</script>";
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
        <title>Change password</title>
        <link href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css" rel="stylesheet" />
        <link href="css/styles.css" rel="stylesheet" />
        <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/js/all.min.js" crossorigin="anonymous"></script>
<script language="javascript" type="text/javascript">
function valid()
{
if(document.changepassword.newpassword.value!= document.changepassword.confirmpassword.value)
{
alert("Password and Confirm Password Field do not match  !!");
document.changepassword.confirmpassword.focus();
  return false;
 }
  return true;
 }
 </script>

    </head>
    <body class="sb-nav-fixed">
      <?php include_once('includes/navbar.php');?>
        <div id="layoutSidenav">
          <?php include_once('includes/sidebar.php');?>
            <div id="layoutSidenav_content">
                <main>
                    <div class="container-fluid px-4">
                        

                        <h1 class="mt-4">Change Password</h1>
                        <div class="card mb-4">
                     <form method="post" name="changepassword" onSubmit="return valid();">
                            <div class="card-body">
                                <table class="table table-bordered">
                                   <tr>
                                    <th>Current Password</th>
                                       <td><input class="form-control" id="currentpassword" name="currentpassword" type="password" value="" required /></td>
                                   </tr>
                                   <tr>
                                       <th>New Password</th>
                                       <td><input class="form-control" id="newpassword" name="newpassword" type="password" value=""  required /></td>
                                   </tr>
                                         <tr>
                                       <th>Confirm Password</th>
                                       <td colspan="3"><input class="form-control" id="confirmpassword" name="confirmpassword" type="password"    required /></td>
                                   </tr>
                  
                                   <tr>
                                       <td colspan="4" style="text-align:center ;"><button type="submit" class="btn btn-primary btn-block" name="update">Change</button></td>

                                   </tr>
                                    </tbody>
                                </table>
                            </div>
                            </form>
                        </div>


                    </div>
                </main>
        
            </div>
        </div>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
        <script src="js/scripts.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js" crossorigin="anonymous"></script>
        <script src="assets/demo/chart-area-demo.js"></script>
        <script src="assets/demo/chart-bar-demo.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/simple-datatables@latest" crossorigin="anonymous"></script>
        <script src="js/datatables-simple-demo.js"></script>
        <style>

    .container-fluid {
        width: 80%;
        max-width: 900px;
        margin: 50px auto;
        padding: 30px;
        background-color: #fff;
        border-radius: 8px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    }

    h1 {
        font-size: 28px;
        margin-bottom: 20px;
        color: #333;
    }

    .card {
        border: none;
        border-radius: 8px;
    }

    .card-body {
        padding: 20px;
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    th, td {
        padding: 12px;
        text-align: left;
        font-size: 16px;
        border: 1px solid #ddd;
    }

    th {
        background-color: #f7f7f7;
        color: #333;
    }

    td {
        background-color: #fff;
        color: #555;
    }

    .btn {
        padding: 10px 20px;
        background-color: #007bff;
        color: #fff;
        font-size: 16px;
        border: none;
        border-radius: 5px;
        width: 100%;
        cursor: pointer;
    }

    .btn:hover {
        background-color: #0056b3;
    }

    /* Responsive Styles */
    @media (max-width: 768px) {
        .container-fluid {
            width: 95%;
            padding: 15px;
        }

        h1 {
            font-size: 24px;
        }

        th, td {
            font-size: 14px;
            padding: 10px;
        }
    }
</style>

</body>
    </html>
   <?php
   ?>
