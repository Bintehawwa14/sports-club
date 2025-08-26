<?php
include_once('../../include/db_connect.php'); // go up 2 folders


// Agar team_name diya hai to players fetch karo
if (isset($_GET['team_name'])) {
    $teamName = mysqli_real_escape_string($con, $_GET['team_name']);
   $sqlPlayers = "SELECT player_name AS fullName, role, team_name, age, batting_style, bowling_style, height, weight, disability
               FROM cricket_players 
               WHERE team_name = '$teamName'";
    $players = mysqli_query($con, $sqlPlayers);

} else {
    // Teams fetch
    $sqlTeams = "SELECT full_name, team_name, captain_name, vice_captain_name, email, game, is_approved 
                 FROM cricket_teams";
    $teams = mysqli_query($con, $sqlTeams);
   
}


?>
<!DOCTYPE html>
<html>
<head>
    <title>Teams & Players</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-4">

<?php if (!isset($_GET['team_name'])) { ?>
    <!-- Teams Table -->
    <h2 class="mb-4">Registered Teams</h2>
    <a href="../registered.php" class="btn btn-secondary mb-3">⬅ Back</a>
    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <h3>Cricket</h3>
            <tr>
                <th>Full Name</th>
                <th>Team Name</th>
                <th>Captain</th>
                <th>Vice Captain</th>
                <th>Email</th>
                <th>Game</th>
                <th>Approved?</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
        <?php while ($row = mysqli_fetch_assoc($teams)) { ?>
            <tr>
                <td><?php echo htmlspecialchars($row['full_name']); ?></td>
                <td><?php echo htmlspecialchars($row['team_name']); ?></td>
                <td><?php echo htmlspecialchars($row['captain_name']); ?></td>
                <td><?php echo htmlspecialchars($row['vice_captain_name']); ?></td>
                <td><?php echo htmlspecialchars($row['email']); ?></td>
                <td><?php echo htmlspecialchars($row['game']); ?></td>
                <td>
                    <!-- Approve dropdown -->
        <form method="post" action="update_status.php">
        <input type="hidden" name="team_name" value="<?= htmlspecialchars($row['team_name']); ?>">
        <select name="is_approved" onchange="this.form.submit()">
            <option value="pending" <?= ($row['is_approved']=='pending') ? 'selected' : ''; ?>>Pending</option>
            <option value="approved" <?= ($row['is_approved']=='approved') ? 'selected' : ''; ?>>Approved</option>
        </select>
    </form>
<td>         
<a href="cteams_detail.php?team_name=<?php echo urlencode($row['team_name']); ?>" class="btn btn-primary btn-sm">Details</a>

<a href="delete.php?game=cricket&teamName=<?= urlencode($row['team_name']); ?>"
   class="btn btn-danger btn-sm"
   onclick="return confirm('Are you sure you want to delete this team?');">
   🗑
</a>
        </td>
</td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
           

<?php } ?>
</body>
</html>
