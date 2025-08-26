<?php session_start();
include_once('../include/db_connect.php');

// for deleting user
if(isset($_GET['id']))
{
$adminid=$_GET['id'];
$msg=mysqli_query($con,"delete from users where id='$adminid'");
if($msg)
{
echo "<script>alert('Data deleted');</script>";
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
        <title>Registered players/Teams</title>
        <link href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css" rel="stylesheet" />
        <link href="../css/styles.css" rel="stylesheet" />
        <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/js/all.min.js" crossorigin="anonymous"></script>

    </head>
    <body class="sb-nav-fixed">
      <?php include_once('includes/navbar.php');?>
        <div id="layoutSidenav">
         <?php include_once('includes/sidebar.php');?>
            <div id="layoutSidenav_content">
                <main>
                    <div class="container-fluid px-4">
                        <h1 class="mt-4">Registered Badminton players/Teams</h1>
                       
            
                        <div class="card mb-4">
                            <div class="card-header">
                                <i class="fas fa-table me-1"></i>
                                Registered Badminton players/teams Details
                            </div>
                            <div class="card-body">
                                <table id="datatablesSimple">
                                    <thead>
                                        
                                  <tr>
                                  <th>Sno.</th>
                                  <th>Full Name</th>
                                  <th> Email</th>
                                  <th> Game</th>
                                  <th> Role</th>
                                  </tr>
                                    </tfoot>
                                    <tbody>
                                <?php $ret=mysqli_query($con,"select * from badminton_players");
                                $cnt=1;
                                while($row=mysqli_fetch_array($ret))
                              {?>
                              <tr>
                              <td><?php echo $cnt;?></td>
                                  <td><?php echo $row['fullName'];?></td>
                                  <td><?php echo $row['email'];?></td>
                                  <td><?php echo $row['game'];?></td>
                                  <td><?php echo $row['role'];?></td>  
                                
                                  
                              </tr>
                              <?php $cnt=$cnt+1; }?>
                                  
                                </table>
                            <a class="btn btn-sm btn-primary edit-link" href="badminton_scheduling.php">Single Elimination</a>
                            <a class="btn btn-sm btn-primary edit-link" href="badminton_double.php">Double Elimination</a>
                        </div>
                    </div>
                </main>
  
            </div>
        
        </div>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
        <script src="../js/scripts.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/simple-datatables@latest" crossorigin="anonymous"></script>
        <script src="../js/datatables-simple-demo.js"></script>
        <style>
    /* Manage Users Page Styles */
    .container-fluid {
        width: 80%;
        max-width: 1000px;
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
        margin-top: 20px;
    }

    .card-header {
        background-color: #f7f7f7;
        color: #333;
        padding: 15px;
        font-size: 18px;
        border-bottom: 1px solid #ddd;
        display: flex;
        align-items: center;
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
        padding: 12px;
        text-align: left;
        font-size: 16px;
        border: 1px solid #ddd;
    }

    th {
        background-color: #007bff;
        color: #fff;
    }

    td {
        background-color: #fff;
        color: #555;
    }

    tr:hover {
        background-color: #f5f5f5;
    }

    .btn {
        padding: 8px 15px;
        background-color: #28a745;
        color: #fff;
        font-size: 14px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
    }

    .btn-danger {
        background-color: #dc3545;
    }

    .btn:hover {
        background-color: #218838;
    }

    .btn-danger:hover {
        background-color: #c82333;
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

</style>

</body>
</html>
<?php  ?>