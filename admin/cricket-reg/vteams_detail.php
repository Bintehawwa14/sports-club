<?php
include_once('../../include/db_connect.php');

if (!isset($_GET['team_name'])) {
    die("Team not selected.");
}

$teamName = mysqli_real_escape_string($con, $_GET['team_name']);

// Team ke players fetch
$sql = "SELECT player_name, age, position, height, handedness, weight, standing_reach, 
               block_jump, approach_jump, chronic_illness, allergies, medications, surgeries, previous_injuries, 
                is_approved 
        FROM volleyball_players 
        WHERE team_name = '$teamName'";
$result = mysqli_query($con, $sql);

// Player approval check
$checkPlayers = "SELECT COUNT(*) as not_approved_count 
                 FROM volleyball_players 
                 WHERE team_name = '$teamName' AND is_approved != 'approved'";
$checkResult = mysqli_query($con, $checkPlayers);
$checkRow = mysqli_fetch_assoc($checkResult);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Volleyball Team Details - <?php echo htmlspecialchars($teamName); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-4">

    <h2>Volleyball Team: <?php echo htmlspecialchars($teamName); ?></h2>
    <a href="all_teams.php" class="btn btn-secondary mb-3">⬅ Back</a>

<table class="table table-bordered table-striped">
    <thead class="table-dark">
        <tr>
            <th>Player Name</th>
            <th>Age</th>
            <th>Position</th>
            <th>Height</th>
            <th>Handedness</th>
            <th>Weight</th>
            <th>Standing Reach</th>
            <th>Block Jump</th>
            <th>Approach Jump</th>
            <th>Chronic Illness</th>
            <th>Allergies</th>
            <th>Medications</th>
            <th>Surgeries</th>
            <th>Previous Injuries</th>
            <th>Approval Status</th>
        </tr>
    </thead>
    <tbody>
    <?php if (mysqli_num_rows($result) > 0) { 
        while ($row = mysqli_fetch_assoc($result)) { ?>
            <tr>
                <td><?php echo htmlspecialchars($row['player_name']); ?></td>
                <td><?php echo htmlspecialchars($row['age']); ?></td>
                <td><?php echo htmlspecialchars($row['position']); ?></td>
                <td><?php echo htmlspecialchars($row['height']); ?></td>
                <td><?php echo htmlspecialchars($row['handedness']); ?></td>
                <td><?php echo htmlspecialchars($row['weight']); ?></td>
                <td><?php echo htmlspecialchars($row['standing_reach']); ?></td>
                <td><?php echo htmlspecialchars($row['block_jump']); ?></td>
                <td><?php echo htmlspecialchars($row['approach_jump']); ?></td>
                <td><?php echo htmlspecialchars($row['chronic_illness']); ?></td>
                <td><?php echo htmlspecialchars($row['allergies']); ?></td>
                <td><?php echo htmlspecialchars($row['medications']); ?></td>
                <td><?php echo htmlspecialchars($row['surgeries']); ?></td>
                <td><?php echo htmlspecialchars($row['previous_injuries']); ?></td>
                
                <td>
                    <form method="post" action="update_statusv.php">
                        <input type="hidden" name="player_name" value="<?php echo $row['player_name']; ?>">
                        <input type="hidden" name="team_name" value="<?php echo $teamName; ?>">
                        <input type="hidden" name="game" value="volleyball"> <!-- 👈 zaroori -->
                        <input type="hidden" name="redirect_back" value="<?php echo $_SERVER['REQUEST_URI']; ?>">
                        <select name="is_approved" onchange="this.form.submit()">
                            <option value="pending" <?php if($row['is_approved']=='pending') echo 'selected'; ?>>Pending</option>
                            <option value="approved" <?php if($row['is_approved']=='approved') echo 'selected'; ?>>Approved</option>
                        </select>
                    </form>
                </td>
            </tr>
        <?php } 
    } else { ?>
        <tr><td colspan="16" class="text-center">No players found for this team</td></tr>
    <?php } ?>
    </tbody>
</table>

</body>
</html>
