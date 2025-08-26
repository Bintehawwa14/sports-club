<?php
include_once('../include/db_connect.php');

// Fetch all games (this replaces $result)
$result = mysqli_query($con, "SELECT * FROM games"); // Make sure your table is correct

if (!$result) {
    die("Query failed: " . mysqli_error($con));
}

// Cricket Teams Count
$q1 = mysqli_query($con, "SELECT COUNT(*) AS total FROM cricket_teams");
$cricket_count = mysqli_fetch_assoc($q1)['total'];

// Badminton Players Count
$q2 = mysqli_query($con, "SELECT COUNT(*) AS total FROM badminton_players");
$badminton_count = mysqli_fetch_assoc($q2)['total'];

// Table Tennis Players Count
$q3 = mysqli_query($con, "SELECT COUNT(*) AS total FROM tabletennis_players");
$tabletennis_count = mysqli_fetch_assoc($q3)['total'];

// Volleyball Teams Count
$q4 = mysqli_query($con, "SELECT COUNT(*) AS total FROM volleyball_teams");
$volleyball_count = mysqli_fetch_assoc($q4)['total'];
?>


<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="" />
        <meta name="author" content="" />
        <title>Match Scheduling</title>
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
                <h1 class="mt-4">Match Scheduling</h1>
                    <div class="row-dashboard">
                        
                        
<?php while ($row = mysqli_fetch_assoc($result)) { 
            
            $game_name = $row['game_name'];
            $registered = 0;

            // Game ka naam dekh kar respective table se count nikal lo
            switch (strtolower($row['game_name'])) {
                case 'cricket':
                    $q = mysqli_query($con, "SELECT COUNT(*) AS total FROM cricket_teams");
                    $registered = mysqli_fetch_assoc($q)['total'];
                    break;
                case 'volleyball':
                    $q = mysqli_query($con, "SELECT COUNT(*) AS total FROM volleyball_teams");
                    $registered = mysqli_fetch_assoc($q)['total'];
                    break;
                case 'tabletennis':
                    $q = mysqli_query($con, "SELECT COUNT(*) AS total FROM tabletennis_players");
                    $registered = mysqli_fetch_assoc($q)['total'];
                    break;
                case 'badminton':
                    $q = mysqli_query($con, "SELECT COUNT(*) AS total FROM badminton_players");
                    $registered = mysqli_fetch_assoc($q)['total'];
                    break;
                default:
                    $registered = 0;
            }
        }
        ?>


    <!-- Cricket Card -->
<div class="col-sm-6 col-lg-4 mb-3 d-flex justify-content-center">
    <div class="card bg-gradient-primary text-white shadow-sm">
        <div class="card-body">
            <h6 class="card-title">Cricket</h6>
            <p class="card-text" style="font-size: 18px;"><?php echo $cricket_count; ?></p>
        </div>
        <div class="card-footer d-flex align-items-center justify-content-between bg-dark text-white">
            <a class="small text-white stretched-link" href="cricket_teams.php">Match scheduling</a>
            <div class="small text-white"><i class="fas fa-angle-right"></i></div>
        </div>
    </div>
</div>

<!-- Badminton Card -->
<div class="col-sm-6 col-lg-4 mb-3 d-flex justify-content-center">
    <div class="card bg-gradient-primary text-white shadow-sm">
        <div class="card-body">
            <h6 class="card-title">Badminton</h6>
            <p class="card-text" style="font-size: 18px;"><?php echo $badminton_count; ?></p>
        </div>
        <div class="card-footer d-flex align-items-center justify-content-between bg-dark text-white">
            <a class="small text-white stretched-link" href="badminton_players.php">Match Scheduling</a>
            <div class="small text-white"><i class="fas fa-angle-right"></i></div>
        </div>
    </div>
</div>

<!-- Table Tennis Card -->
<div class="col-sm-6 col-lg-4 mb-3 d-flex justify-content-center">
    <div class="card bg-gradient-primary text-white shadow-sm">
        <div class="card-body">
            <h6 class="card-title">Table Tennis</h6>
            <p class="card-text" style="font-size: 18px;"><?php echo $tabletennis_count; ?></p>
        </div>
        <div class="card-footer d-flex align-items-center justify-content-between bg-dark text-white">
            <a class="small text-white stretched-link" href="tabletennis_players.php">Match Scheduling</a>
            <div class="small text-white"><i class="fas fa-angle-right"></i></div>
        </div>
    </div>
</div>

<!-- Volleyball Card -->
<div class="col-sm-6 col-lg-4 mb-3 d-flex justify-content-center">
    <div class="card bg-gradient-primary text-white shadow-sm">
        <div class="card-body">
            <h6 class="card-title">Volleyball</h6>
            <p class="card-text" style="font-size: 18px;"><?php echo $volleyball_count; ?></p>
        </div>
        <div class="card-footer d-flex align-items-center justify-content-between bg-dark text-white">
            <a class="small text-white stretched-link" href="volleyball_teams.php">Match Scheduling</a>
            <div class="small text-white"><i class="fas fa-angle-right"></i></div>
        </div>
    </div>
</div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
        <script src="../js/scripts.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/simple-datatables@latest" crossorigin="anonymous"></script>
        <script src="../js/datatables-simple-demo.js"></script>
        <style>
    body {
        background-color: #f0f2f5;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    .container-fluid {
        padding: 40px 20px;
    }

    h1 {
        font-size: 34px;
        color: #2c3e50;
        font-weight: 600;
        margin-bottom: 30px;
        text-align: center;
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