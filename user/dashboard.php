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
    <meta charset="UTF-8">
    <title>My Approved Registrations</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-5">
    <h2 class="mb-4 text-center text-success">My Approved Registrations</h2>

    <div class="row row-cols-1 g-4">

        <!-- Cricket Teams -->
        <?php while($team = mysqli_fetch_assoc($cricketTeams)) { ?>
            <div class="col">
                <div class="p-3 border rounded shadow-sm bg-white">
                    <h5 class="fw-bold text-primary"><?= htmlspecialchars($team['team_name']); ?> (Cricket)</h5>
                    <p class="mb-1">Captain: <?= htmlspecialchars($team['captain_name']); ?></p>
                    <p class="mb-1">Vice Captain: <?= htmlspecialchars($team['vice_captain_name']); ?></p>
                    <span class="badge bg-success">Approved</span>

                    <!-- Team Members -->
                    <div class="mt-3">
                        <h6 class="fw-bold">Team Members:</h6>
                        <ul class="list-group">
                            <?php
                            $teamName = mysqli_real_escape_string($con, $team['team_name']);
                            $members = mysqli_query($con, "SELECT * FROM cricket_players WHERE team_name='$teamName'");
                            while($m = mysqli_fetch_assoc($members)) {
                                echo "<li class='list-group-item'>" . htmlspecialchars($m['team_name']) . " - " . htmlspecialchars($m['player_name']) . "</li>";
                            }
                            ?>
                        </ul>
                    </div>
                </div>
            </div>
        <?php } ?>

        <!-- Volleyball Teams -->
        <?php while($team = mysqli_fetch_assoc($volleyTeams)) { ?>
            <div class="col">
                <div class="p-3 border rounded shadow-sm bg-white">
                    <h5 class="fw-bold text-primary"><?= htmlspecialchars($team['team_name']); ?> (Volleyball)</h5>
                    <p class="mb-1">Captain: <?= htmlspecialchars($team['captain_name']); ?></p>
                    <p class="mb-1">Vice Captain: <?= htmlspecialchars($team['vice_captain_name']); ?></p>
                    <span class="badge bg-success">Approved</span>

                    <!-- Team Members -->
                    <div class="mt-3">
                        <h6 class="fw-bold">Team Members:</h6>
                        <ul class="list-group">
                            <?php
                            $teamName = mysqli_real_escape_string($con, $team['team_name']);
                            $members = mysqli_query($con, "SELECT player_name,  FROM volleyball_players WHERE team_name='$teamName'");
                            while($m = mysqli_fetch_assoc($members)) {
                                echo "<li class='list-group-item'>" . htmlspecialchars($m['player_name']) . " - " . htmlspecialchars($m['role']) . "</li>";
                            }
                            ?>
                        </ul>
                    </div>
                </div>
            </div>
        <?php } ?>

        <!-- Badminton (individual) -->
        <?php while($row = mysqli_fetch_assoc($badmintonPlayers)) { ?>
            <div class="col">
                <div class="p-3 border rounded shadow-sm bg-white">
                    <h5 class="fw-bold text-primary"><?= htmlspecialchars($row['player1']); ?> (Badminton)</h5>
                    <p class="mb-1">Role: <?= htmlspecialchars($row['role']); ?></p>
                    <span class="badge bg-success">Approved</span>
                </div>
            </div>
        <?php } ?>

        <!-- Table Tennis (individual) -->
        <?php while($row = mysqli_fetch_assoc($tabletennisPlayers)) { ?>
            <div class="col">
                <div class="p-3 border rounded shadow-sm bg-white">
                    <h5 class="fw-bold text-primary"><?= htmlspecialchars($row['player1']); ?> (Table Tennis)</h5>
                    <p class="mb-1">Role: <?= htmlspecialchars($row['role']); ?></p>
                    <span class="badge bg-success">Approved</span>
                </div>
            </div>
        <?php } ?>

    </div>
</div>

</body>
</html>
