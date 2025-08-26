<?php
include_once('../../include/db_connect.php');

if (!isset($_GET['teamName'])) {
    die("Team not selected.");
}

$teamName = mysqli_real_escape_string($con, $_GET['teamName']);

// Team ke details fetch karo
$sql = "SELECT * FROM tabletennis_players WHERE teamName = '$teamName'";
$result = mysqli_query($con, $sql);
$team = mysqli_fetch_assoc($result);

if (!$team) {
    die("No team found.");
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Table Tennis Team Details - <?php echo htmlspecialchars($teamName); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-4">

    <h2>Table Tennis Team: <?php echo htmlspecialchars($teamName); ?></h2>
    <a href="all_ttteams.php" class="btn btn-secondary mb-3">⬅ Back</a>

    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>Player Name</th>
                <th>Email</th>
                <th>Date of Birth</th>
                <th>Height</th>
                <th>Weight</th>
                <th>Hand</th>
                <th>Play Style</th>
                <th>Chronic Illness</th>
                <th>Allergies</th>
                <th>Medications</th>
                <th>Surgeries</th>
                <th>Previous Injuries</th>
                <th>Approval Status</th>
            </tr>
        </thead>
        <tbody>
            <!-- ✅ Player 1 -->
            <tr>
                <td><?php echo htmlspecialchars($team['player1']); ?></td>
                <td><?php echo htmlspecialchars($team['email']); ?></td>
                <td><?php echo htmlspecialchars($team['dob1']); ?></td>
                <td><?php echo htmlspecialchars($team['height1']); ?></td>
                <td><?php echo htmlspecialchars($team['weight1']); ?></td>
                <td><?php echo htmlspecialchars($team['hand1']); ?></td>
                <td><?php echo htmlspecialchars($team['play_style1']); ?></td>
                <td><?php echo htmlspecialchars($team['chronic_illness1']); ?></td>
                <td><?php echo htmlspecialchars($team['allergies1']); ?></td>
                <td><?php echo htmlspecialchars($team['medications1']); ?></td>
                <td><?php echo htmlspecialchars($team['surgeries1']); ?></td>
                <td><?php echo htmlspecialchars($team['previous_injuries1']); ?></td>
                <td>
                    <form method="post" action="update_status.php">
                        <input type="hidden" name="player_name" value="<?php echo $team['player1']; ?>">
                        <input type="hidden" name="team_name" value="<?php echo $teamName; ?>">
                        <input type="hidden" name="game" value="tabletennis">
                        <input type="hidden" name="redirect_back" value="<?php echo $_SERVER['REQUEST_URI']; ?>">
                        <select name="is_approved" onchange="this.form.submit()">
                            <option value="pending" <?php if($team['is_approved']=='pending') echo 'selected'; ?>>Pending</option>
                            <option value="approved" <?php if($team['is_approved']=='approved') echo 'selected'; ?>>Approved</option>
                        </select>
                    </form>
                </td>
            </tr>

            <!-- ✅ Player 2 -->
            <tr>
                <td><?php echo htmlspecialchars($team['player2']); ?></td>
                <td><?php echo htmlspecialchars($team['email']); ?></td>
                <td><?php echo htmlspecialchars($team['dob2']); ?></td>
                <td><?php echo htmlspecialchars($team['height2']); ?></td>
                <td><?php echo htmlspecialchars($team['weight2']); ?></td>
                <td><?php echo htmlspecialchars($team['hand2']); ?></td>
                <td><?php echo htmlspecialchars($team['play_style2']); ?></td>
                <td><?php echo htmlspecialchars($team['chronic_illness2']); ?></td>
                <td><?php echo htmlspecialchars($team['allergies2']); ?></td>
                <td><?php echo htmlspecialchars($team['medications2']); ?></td>
                <td><?php echo htmlspecialchars($team['surgeries2']); ?></td>
                <td><?php echo htmlspecialchars($team['previous_injuries2']); ?></td>
                <td>
                    <form method="post" action="update_status.php">
                        <input type="hidden" name="player_name" value="<?php echo $team['player2']; ?>">
                        <input type="hidden" name="team_name" value="<?php echo $teamName; ?>">
                        <input type="hidden" name="game" value="tabletennis">
                        <input type="hidden" name="redirect_back" value="<?php echo $_SERVER['REQUEST_URI']; ?>">
                        <select name="is_approved" onchange="this.form.submit()">
                            <option value="pending" <?php if($team['is_approved']=='pending') echo 'selected'; ?>>Pending</option>
                            <option value="approved" <?php if($team['is_approved']=='approved') echo 'selected'; ?>>Approved</option>
                        </select>
                    </form>
                </td>
            </tr>
        </tbody>
    </table>

</body>
</html>
