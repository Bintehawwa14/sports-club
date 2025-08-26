<?php
session_start();
require '../include/db_connect.php';

// ✅ Ensure only admin can access
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// ✅ Fetch registered cricket teams
$sql = "SELECT team_name, captain_name, vice_captain_name
        FROM cricket_teams 
        ORDER BY team_name DESC";
$result = $con->query($sql);
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
                        <h1 class="mt-1">Registered Cricket Teams</h1>
                       
            
                        <div class="card mb-3">
                            <div class="card-header">
                                <i class="fas fa-table me-1"></i>
                                Registered Cricket Teams Details
                            </div>
                            <div class="card-body">
                                <table id="datatablesSimple">

    <?php if ($result->num_rows > 0): ?>
        <div class="table-responsive mt-1">
            <table class="table table-bordered table-hover align-middle">
                <thead>
                    <tr>
                        <th>Team Name</th>
                        <th>Captain Name</th>
                        <th>Vice Captain Name</th>
                       
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = $result->fetch_assoc()): ?>
                        <tr>
                           
                            <td><?= htmlspecialchars($row['team_name']) ?></td>
                            <td><?= htmlspecialchars($row['captain_name']) ?></td>
                             <td><?= htmlspecialchars($row['vice_captain_name']) ?></td>
                            
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
           <a class="btn btn-sm btn-primary edit-link" href="cricket_scheduling.php">Schedule match</a>
    <?php else: ?>
        <div class="alert alert-warning mt-3">No cricket teams registered yet.</div>
    <?php endif; ?>
</div>
<style>
        body {
            background-color: #fff;
        }
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
        margin-left: 20px;
        background-color: #007bff;
        color: #fff;
        font-size: 14px;
        border-radius: 5px;
        cursor: pointer;
    }

    .btn-danger {
        background-color: #dc3545;
    }

    .btn:hover {
        background-color: #007bff;
    }

    .btn-danger:hover {
        background-color: #c82333;
    }
    </style>
</body>
</html>
