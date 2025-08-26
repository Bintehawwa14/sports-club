<?php
session_start();
if (!isset($_SESSION['userid']) && isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
 
    header("Location: ../login.php");
    exit();
}

?>
<?php 
//session_start();
include_once('../include/db_connect.php');
//if (strlen($_SESSION['adminid']==0)) {
  //header('location:logout.php');
  //} else{
    
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="" />
        <meta name="author" content="" />
        <title>Admin Dashboard </title>
        <link href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css" rel="stylesheet" />
        <link href="../style.css" rel="stylesheet" />
        <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/js/all.min.js" crossorigin="anonymous"></script>
   
    </head>
    <body class="sb-nav-fixed">
   <?php include ('includes/navbar.php');?>
        <div id="layoutSidenav">
          <?php include ('includes/sidebar.php');?>
            <div id="layoutSidenav_content">
                <main>
                <h1 class="mt-4">Dashboard</h1>
                    <div class="row-dashboard">
                        
                        
                        
<?php
$query=mysqli_query($con,"select id from users");
$totalusers=mysqli_num_rows($query);
?>



    <!-- Total Registered Users Card -->
    <div class="col-sm-6 col-lg-4 mb-3 d-flex justify-content-center">
        <div class="card bg-gradient-primary text-white shadow-sm" >
            <div class="card-body">
                <h6 class="card-title">Total Registered Users</h6>
                <p class="card-text" style="font-size: 18px;"><?php echo $totalusers; ?></p>
            </div>
            <div class="card-footer d-flex align-items-center justify-content-between bg-dark text-white">
                <a class="small text-white stretched-link" href="manage-users.php">View Details</a>
                <div class="small text-white"><i class="fas fa-angle-right"></i></div>
            </div>
</div>
</div>

<?php
$query5=mysqli_query($con,"select id from events");
$lastevents=mysqli_num_rows($query5);
?>
 <div class="col-sm-6 col-lg-4 mb-3 d-flex justify-content-center">
        <div class="card bg-gradient-primary text-white shadow-sm" >
            <div class="card-body">
                <h6 class="card-title"> Events </h6>
                <p class="card-text" style="font-size: 18px;"><?php echo $lastevents; ?></p>
            </div>
            <div class="card-footer d-flex align-items-center justify-content-between bg-dark text-white">
                <a class="small text-white stretched-link" href="manage-events.php">View Details</a>
                <div class="small text-white"><i class="fas fa-angle-right"></i></div>
            </div>
</div>
</div>             
           </main>
           
            </div>
        </div>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
        <script src="../js/scripts.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/simple-datatables@latest" crossorigin="anonymous"></script>
        <script src="../js/datatables-simple-demo.js"></script>
        <style>
    body {
          margin: 0;
        padding: 0;
        background-image: url('../images/volleyballform.jpg');
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

    .container-fluid {
        padding: 40px 20px;
    }

    h1 {
        font-size: 34px;
        color: #ffffff; 
        font-weight: 700;
        margin-bottom: 30px;
        text-align: right center;        
        background-color: rgba(0, 0, 0, 0.4); 
        display: inline-block;
        padding: 10px 20px;
        border-radius: 10px;
        text-shadow: 2px 2px 4px rgba(0,0,0,0.7);
        
    }

  
    .row-dashboard {
        display: flex;
        flex-wrap: wrap;
        justify-content: left;
        gap: 30px;
        margin-top: 20px;}

    .card {
        border: none;
        border-radius: 20px;
        overflow: hidden;
        width: 100%;
        max-width: 320px;
        box-shadow: 0 6px 15px rgba(0, 0, 0, 0.1);
       
    }

    
    .bg-gradient-primary {
        background: linear-gradient(145deg, #007bff, #0056b3);
        color: white;
    }

    .card-body {
        padding: 30px;
        text-align: center;
    }

    .card-title {
        font-size: 20px;
        margin-bottom: 10px;
    }

    .card-text {
        font-size: 28px;
        font-weight: 700;
    }

    .card-footer {
        background-color: rgba(0, 0, 0, 0.1);
        color: white;
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 12px 20px;
    }

    .card-footer a {
        color: #fff;
        text-decoration: underline;
    }

    @media (max-width: 768px) {
        .card {
            max-width: 100%;
        }

        h1 {
            font-size: 100px;
        }
    }
   
</style>
 </body>
</html>

<?php //} ?>
