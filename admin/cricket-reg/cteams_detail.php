<?php
include_once('../../include/db_connect.php');

if (!isset($_GET['team_name'])) {
    die("Team not selected.");
}

$teamName = mysqli_real_escape_string($con, $_GET['team_name']);

// Team ke players fetch
$sql = "SELECT player_name, age, role, batting_style, bowling_style, height, weight, disability, is_approved 
        FROM cricket_players 
        WHERE team_name = '$teamName'";
$result = mysqli_query($con, $sql);

// Player update ke baad team ka status check karo
$checkPlayers = "SELECT COUNT(*) as not_approved_count 
                 FROM cricket_players 
                 WHERE team_name = '$teamName' AND is_approved != 'approved'";
$checkResult = mysqli_query($con, $checkPlayers);
$checkRow = mysqli_fetch_assoc($checkResult);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Team Details - <?php echo htmlspecialchars($teamName); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-4">

    <h2>Team: <?php echo htmlspecialchars($teamName); ?></h2>
    <a href="all_teams.php" class="btn btn-secondary mb-3">⬅ Back</a>
<table class="table table-bordered table-striped">
    <thead class="table-dark">
        <tr>
            <th>Player Name</th>
            <th>Age</th>
            <th>Role</th>
            <th>Batting Style</th>
            <th>Bowling Style</th>
            <th>Height</th>
            <th>Weight</th>
            <th>Disability</th>
            <th>Approval Status</th> <!-- 👈 New Column -->
        </tr>
    </thead>
    <tbody>
    <?php if (mysqli_num_rows($result) > 0) { 
        while ($row = mysqli_fetch_assoc($result)) { ?>
            <tr>
                <td><?php echo htmlspecialchars($row['player_name']); ?></td>
                <td><?php echo htmlspecialchars($row['age']); ?></td>
                <td><?php echo htmlspecialchars($row['role']); ?></td>
                <td><?php echo htmlspecialchars($row['batting_style']); ?></td>
                <td><?php echo htmlspecialchars($row['bowling_style']); ?></td>
                <td><?php echo htmlspecialchars($row['height']); ?></td>
                <td><?php echo htmlspecialchars($row['weight']); ?></td>
                <td><?php echo htmlspecialchars($row['disability']); ?></td>
                <td>
              <form method="post" action="update_statust.php">
    <input type="hidden" name="player_name" value="<?php echo $row['player_name']; ?>">
    <input type="hidden" name="team_name" value="<?php echo $teamName; ?>">
    <input type="hidden" name="game" value="cricket"> <!-- 👈 zaroori -->
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
        <tr><td colspan="9" class="text-center">No players found for this team</td></tr>
    <?php } ?>
    </tbody>
</table>

</body>
</html>
