<?php 
session_start();
include_once('../include/db_connect.php');
if (!isset($_SESSION['userid']))  {
    header('location:logout.php');
    exit;
  } else{
    echo "Session UserID: " . $_SESSION['userid']; 

 

if (isset($_POST['update'])) {
    $email = $_POST['email'];
    $fname = $_POST['fname'];
    $lname = $_POST['lname'];
    $contactno = $_POST['contact'];
    $cnic = $_POST['cnic'];
    $club_college = $_POST['club_college'];
    $userID = $_SESSION['userid'];

    $update_success = mysqli_query($con, "UPDATE users SET fname='$fname', lname='$lname', email='$email', contactno='$contactno' cnic='$cnic', club_college='$club_college' WHERE id=$userID");

    if ($update_success) {
        echo "<script>alert('Profile updated successfully!');</script>";
        echo "<script>window.location.href = 'profile.php';</script>";
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
        <meta name="description" content="" />
        <meta name="author" content="" />
        <title>Edit Profile </title>
        <link href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css" rel="stylesheet" />
        <link href="css/styles.css" rel="stylesheet" />
        <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/js/all.min.js" crossorigin="anonymous"></script>
    

</head>
    <body class="sb-nav-fixed">
      <?php include_once('includes/navbar.php');?>
        <div id="layoutSidenav">
          <?php include_once('includes/sidebar.php');?>
            <div id="layoutSidenav_content">
                <main>
                    <div class="container-fluid px-4">
                        
<?php 
$userid=$_SESSION['userid'];
$query=mysqli_query($con,"select * from users where id='$userid'");
while($result=mysqli_fetch_array($query))
{?>
                        <h1 class="mt-4"><?php echo $result['fname'];?>'s Profile</h1>
                        <div class="card mb-4">
                     <form method="post">
                            <div class="card-body">
                                <table class="table table-bordered">
                                   <tr>
                                    <th>First Name</th>
                                       <td><input class="form-control" id="fname" name="fname" type="text" value="<?php echo $result['fname'];?>" required /></td>
                                   </tr>
                                   <tr>
                                       <th>Last Name</th>
                                       <td><input class="form-control" id="lname" name="lname" type="text" value="<?php echo $result['lname'];?>"  required /></td>
                                   </tr>
                                         <tr>
                                       <th>Contact No.</th>
                                       <td colspan="3"><input class="form-control" id="contact" name="contact" type="text" value="<?php echo $result['contactno'];?>"  pattern="[0-9]{10}" title="10 numeric characters only"  maxlength="10" required /></td>
                                   </tr>
                                   <tr>
                                       <th>Email</th>
                                       <td colspan="3"><input class="for-control" id="email" name="email" type="text" value="<?php echo $result['email'];?>" required?/></td>
                                   </tr>
                               
                                     
                                        <tr>
                                       <th>Reg. Date</th>
                                       <td colspan="3"><?php echo $result['posting_date'];?></td>
                                   </tr>
                                    <tr>
                                       <th>CNIC</th>
                                       <td colspan="3"><input class="for-control" id="cnic" name="email" type="text" value="<?php echo $result['cnic'];?>" required?/></td>
                                   </tr>
                                    <tr>
                                       <th>Club/college</th>
                                       <td colspan="3"><input class="for-control" id="club_college" name="club_college" type="text" value="<?php echo $result['club_college'];?>" required?/></td>
                                   </tr>
                               
                                   <tr>
                                       <td colspan="4" style="text-align:center ;"><button type="submit" class="btn btn-primary btn-block" name="update">Update</button></td>

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
        <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js" crossorigin="anonymous"></script>
        <script src="assets/demo/chart-area-demo.js"></script>
        <script src="assets/demo/chart-bar-demo.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/simple-datatables@latest" crossorigin="anonymous"></script>
        <script src="js/datatables-simple-demo.js"></script>
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
</style>
</body>
</html>


<?php } ?>
