
<?php
session_start();

// Check if user is admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: index.php');
    exit();
}

include_once('../include/db_connect.php');

// Get admin userid from session
$adminId = $_SESSION['userid'];

// Query admin details from users table
$query = mysqli_query($con, "SELECT * FROM users WHERE id = '$adminId'");

if (!$query) {
    die("Database query failed: " . mysqli_error($con));
}

$admin = mysqli_fetch_assoc($query);

if (!$admin) {
    echo "Admin details not found!";
    exit();
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
        <title>Profile </title>
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
$adminid = $_SESSION['userid']; 

$query = mysqli_query($con, "SELECT * FROM users WHERE id='$adminid'");
if (!$query) {
    die("Query failed: " . mysqli_error($con));
}

if ($result = mysqli_fetch_array($query)) {
?>
                        <div class="profile-container">
    <div class="profile-header mb-4 d-flex justify-content-between align-items-center">
        <h1><?php echo htmlspecialchars($result['fname']); ?>'s Profile</h1>
        <a class="btn btn-sm btn-primary edit-link" href="edit-profile.php">Edit Profile</a>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered align-middle">
            <tr>
                <th>First Name</th>
                <td><?php echo htmlspecialchars($result['fname']); ?></td>
            </tr>
            <tr>
                <th>Last Name</th>
                <td><?php echo htmlspecialchars($result['lname']); ?></td>
            </tr>
            <tr>
                <th>Email</th>
                <td><?php echo htmlspecialchars($result['email']); ?></td>
            </tr>
            <tr>
                <th>Contact No.</th>
                <td><?php echo htmlspecialchars($result['contactno']); ?></td>
            </tr>
            <tr>
                <th>Reg. Date</th>
                <td><?php echo htmlspecialchars($result['posting_date']); ?></td>
            </tr>
        </table>
    </div>
</div>

<?php
} else {
    echo "<div class='alert alert-danger text-center mt-5'>User not found!</div>";
}
?>
       
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
        <script src="js/scripts.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js" crossorigin="anonymous"></script>
        <script src="assets/demo/chart-area-demo.js"></script>
        <script src="assets/demo/chart-bar-demo.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/simple-datatables@latest" crossorigin="anonymous"></script>
        <script src="js/datatables-simple-demo.js"></script>
    </body>
</html>
<?php  ?>
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
    
  
    .profile-container {
        width: 80%;
        max-width: 900px;
        margin: 50px auto;
        background-color: #fff;
        padding: 30px;
        border-radius: 8px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    }

    .profile-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }

    .profile-header h1 {
        font-size: 24px;
        color: #333;
    }

    .edit-link {
        font-size: 14px;
        color: #fff;
        background-color: #007bff;
        padding: 8px 15px;
        text-decoration: none;
        border-radius: 5px;
    }

    .edit-link:hover {
        background-color: #0056b3;
    }

    .table-responsive {
        margin-top: 20px;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 20px;
    }

    th, td {
        padding: 12px;
        text-align: left;
        font-size: 16px;
        border: 1px solid #ddd;
    }

    th {
        background-color: #f7f7f7;
        font-weight: bold;
        color: #333;
    }

    td {
        background-color: #fff;
        color: #555;
    }

    /* For better mobile view */
    @media (max-width: 768px) {
        .profile-container {
            width: 95%;
            padding: 15px;
        }

        .profile-header h1 {
            font-size: 20px;
        }

        .table-responsive {
            margin-top: 10px;
        }

        table th, table td {
            font-size: 14px;
            padding: 8px;
        }
    }
</style>

