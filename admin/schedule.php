<?php
include_once('../include/db_connect.php');

// Fetch registered events
$sql = "SELECT  *  
        FROM events WHERE status='active' OR is_finished='yes'";
$result = mysqli_query($con, $sql);

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Registered Events</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
 <link href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css" rel="stylesheet" />
    <link href="../css/styles.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/js/all.min.js" crossorigin="anonymous"></script>
  <style>
    body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            margin: 0;
            padding: 0;
            background-image: url('../images/volleyballform.jpg'); /* Updated path */
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
       
    .event-card {
      border-radius: 18px;
      background: #ffffff;
      border: 1px solid #ddd;
      overflow: hidden;
      box-shadow: 0px 4px 10px rgba(0,0,0,0.1);
      transition: transform 0.2s;
    }
    .event-card:hover {
      transform: translateY(-6px);
    }
    .event-title {
      font-size: 1.3rem;
      font-weight: bold;
      color: #1565C0;
    }
    .text-muted {
      color: #555 !important;
    }
    .btn-detail {
      background-color: #42A5F5;
      border: none;
      color: white;
      border-radius: 12px;
      padding: 6px 16px;
      transition: background 0.3s;
    }
    .btn-detail:hover {
      background-color: #0D47A1;
    }
    h1{
      
        font-size: 34px;
        color: #ffffff; 
        font-weight: 700;
        margin-bottom: 30px;
        text-align: right  center;        
        background-color: rgba(0, 0, 0, 0.4); 
        display: inline-block;
        padding: 10px 20px;
        border-radius: 10px;
        text-shadow: 2px 2px 4px rgba(0,0,0,0.7);
        
    
    }
  </style>
</head>
<body class="sb-nav-fixed">
  <?php include_once('includes/navbar.php'); ?>
  <div id="layoutSidenav">
    <?php include_once('includes/sidebar.php'); ?>
    <div id="layoutSidenav_content">
      <main class="container-fluid px-4 mt-4">
        <h1>Schedule Matches</h1>
        <div class="row g-4">
          <?php while($row = mysqli_fetch_assoc($result)) { ?>
            <div class="col-md-4">
              <div class="card event-card">
                <div class="card-body">
                  <h5 class="event-title"><?= htmlspecialchars($row['event_name']); ?></h5>
                   <h5 class="event-status"><?= htmlspecialchars($row['status']); ?></h5>
                
                </div>
                <div class="card-footer text-center bg-light">
                  <a href="scheduling.php "class="btn btn-detail">Schedule Matches</a>
                </div>
              </div>
            </div>
          <?php } ?>
        </div>
      </main>
    </div>
  </div>
</body>
</html>
