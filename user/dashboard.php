<?php
session_start();
require '../include/db_connect.php';

if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit();
}

$email = $_SESSION['email'];

// Teams
$cricketTeams     = mysqli_query($con, "SELECT * FROM cricket_teams WHERE email='$email' AND is_approved='approved'");
$volleyTeams      = mysqli_query($con, "SELECT * FROM volleyball_teams WHERE email='$email' AND is_approved='approved'");

// Individuals
$badmintonPlayers   = mysqli_query($con, "SELECT * FROM badminton_players WHERE email='$email' AND is_approved='approved'");
$tabletennisPlayers = mysqli_query($con, "SELECT * FROM tabletennis_players WHERE email='$email' AND is_approved='approved'");
?>

<!DOCTYPE html>
<html lang="en">
<head>
     <meta charset="utf-8" />
     <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
     <title>My Approved Registrations</title>
     <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
     <link href="../css/styles.css" rel="stylesheet" />
     <style>
        body {
            background: linear-gradient(to right, #e3f2fd, #bbdefb);
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
        }
        h2.page-title {
            font-weight: bold;
            color: #0d47a1;
            text-shadow: 1px 1px 3px rgba(0,0,0,0.1);
        }
        .card-custom {
            border-radius: 12px;
            border: none;
            background: #ffffff;
            box-shadow: 0 6px 15px rgba(0,0,0,0.1);
            transition: transform .2s ease, box-shadow .2s ease;
        }
        .card-custom:hover {
            transform: translateY(-6px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.2);
        }
        .card-title {
            color: #1565c0;
        }
        .badge {
            font-size: 0.8rem;
            padding: 6px 10px;
            border-radius: 10px;
        }
        .list-group-item {
            border: none;
            padding: .5rem 1rem;
            background: #f5f9ff;
        }
        .list-group-item:nth-child(even) {
            background: #e8f1fc;
        }
     </style>
</head>
<body class="sb-nav-fixed">

   <!-- Navbar -->
   <?php include_once('includes/navbar.php');?>

   <div id="layoutSidenav">
       <!-- Sidebar -->
       <?php include_once('includes/sidebar.php');?>

       <div id="layoutSidenav_content">
           <main class="container py-5">
               <h2 class="page-title mb-5 text-center">My Approved Registrations</h2>
               <div class="row g-4">

                <!-- Cricket Teams -->
                <?php while($team = mysqli_fetch_assoc($cricketTeams)) { ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="card card-custom">
                            <div class="card-body">
                                <h5 class="card-title fw-bold"><?= htmlspecialchars($team['team_name']); ?> (Cricket)</h5>
                                <p class="mb-1"><b>Captain:</b> <?= htmlspecialchars($team['captain_name']); ?></p>
                                <p class="mb-1"><b>Vice Captain:</b> <?= htmlspecialchars($team['vice_captain_name']); ?></p>
                                <span class="badge bg-primary mb-2">Approved</span>
                                <h6 class="fw-bold text-primary">Team Members</h6>
                                <ul class="list-group list-group-flush">
                                    <?php
                                    $teamName = mysqli_real_escape_string($con, $team['team_name']);
                                    $members = mysqli_query($con, "SELECT * FROM cricket_players WHERE team_name='$teamName'");
                                    while($m = mysqli_fetch_assoc($members)) {
                                        echo "<li class='list-group-item'>" . htmlspecialchars($m['player_name']) . "</li>";
                                    }
                                    ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                <?php } ?>

                <!-- Volleyball Teams -->
                <?php while($team = mysqli_fetch_assoc($volleyTeams)) { ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="card card-custom">
                            <div class="card-body">
                                <h5 class="card-title fw-bold"><?= htmlspecialchars($team['team_name']); ?> (Volleyball)</h5>
                                <p class="mb-1"><b>Captain:</b> <?= htmlspecialchars($team['captain_name']); ?></p>
                                <span class="badge bg-primary mb-2">Approved</span>
                                <h6 class="fw-bold text-primary">Team Members</h6>
                                <ul class="list-group list-group-flush">
                                    <?php
                                    $teamName = mysqli_real_escape_string($con, $team['team_name']);
                                    $members = mysqli_query($con, "SELECT player_name, position FROM volleyball_players WHERE team_name='$teamName'");
                                    while($m = mysqli_fetch_assoc($members)) {
                                        echo "<li class='list-group-item'>" . htmlspecialchars($m['player_name']) . " - " . htmlspecialchars($m['position']) . "</li>";
                                    }
                                    ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                <?php } ?>

                <!-- Badminton -->
                <?php while($row = mysqli_fetch_assoc($badmintonPlayers)) { ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="card card-custom">
                            <div class="card-body">
                                <h5 class="card-title fw-bold"><?= htmlspecialchars($row['player1']); ?> (Badminton)</h5>
                                <p class="mb-1"><b>Role:</b> <?= htmlspecialchars($row['role']); ?></p>
                                <span class="badge bg-primary mb-2">Approved</span>
                                <h6 class="fw-bold text-primary">Players</h6>
                                <ul class="list-group list-group-flush">
                                    <?php
                                    $teamName = mysqli_real_escape_string($con, $row['teamName']);
                                    $members = mysqli_query($con, "SELECT * FROM badminton_players WHERE teamName='$teamName'");
                                    while($m = mysqli_fetch_assoc($members)) {
                                        echo "<li class='list-group-item'>" . htmlspecialchars($m['player1']) . " & " . htmlspecialchars($m['player2']) . "</li>";
                                    }
                                    ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                <?php } ?>

                <!-- Table Tennis -->
                <?php while($row = mysqli_fetch_assoc($tabletennisPlayers)) { ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="card card-custom">
                            <div class="card-body">
                                <h5 class="card-title fw-bold"><?= htmlspecialchars($row['player1']); ?> (Table Tennis)</h5>
                                <p class="mb-1"><b>Role:</b> <?= htmlspecialchars($row['role']); ?></p>
                                <span class="badge bg-primary">Approved</span>
                            </div>
                        </div>
                    </div>
                <?php } ?>

              </div>
           </main>
       </div>
   </div>

   <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
