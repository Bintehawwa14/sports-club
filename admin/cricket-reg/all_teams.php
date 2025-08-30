<?php
include_once('../../include/db_connect.php');

// Fetch all teams per game
$cricketTeams = mysqli_query($con, "SELECT full_name, team_name, captain_name, vice_captain_name, email, game, is_approved FROM cricket_teams WHERE game='cricket'");
$badmintonTeams = mysqli_query($con, "SELECT fullName,email,role,teamName,player1,dob1,height1,weight1,chronic_illness1,allergies1,medications1,surgeries1,previous_injuries1,height2,weight2,chronic_illness2,allergies2,medications2,surgeries2,previous_injuries2,player2,dob2,category,game, is_approved FROM badminton_players");
$tabletennisTeams = mysqli_query($con, "SELECT fullName,email,role,teamName,hand1,play_style1,player1,dob1,height1,weight1,chronic_illness1,allergies1,medications1,surgeries1,previous_injuries1,player2,dob2,height2,weight2,chronic_illness2,allergies2,medications2,surgeries2,previous_injuries2,hand2,play_style2,created_at, game,is_approved FROM tabletennis_players");
$volleyTeams = mysqli_query($con, "SELECT fullName,email,
team_name,club_team,captain_name,captain_age,captain_height,captain_handed,
captain_position,captain_standing_reach,captain_block_jump,
captain_approach_jump,captain_chronic_illness,captain_allergies,captain_medications,
captain_surgeries,captain_previous_injuries,registration_date,is_approved FROM volleyball_teams");
?>

<!DOCTYPE html>
<html>
<head>
    <title>All Teams</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<a href="../registered.php" class="btn btn-secondary mb-3">⬅ Back</a>
<body class="container mt-4">

<!-- Cricket Teams -->
<h3>Cricket Teams</h3>
<table class="table table-bordered table-striped">
    <thead class="table-dark">
        <tr>
            <th>Full Name</th>
            <th>Team Name</th>
            <th>Captain</th>
            <th>Vice Captain</th>
            <th>Email</th>
            <th>Game</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php while($row = mysqli_fetch_assoc($cricketTeams)) { ?>
            <tr>
                <td><?= htmlspecialchars($row['full_name']); ?></td>
                <td><?= htmlspecialchars($row['team_name']); ?></td>
                <td><?= htmlspecialchars($row['captain_name']); ?></td>
                <td><?= htmlspecialchars($row['vice_captain_name']); ?></td>
                <td><?= htmlspecialchars($row['email']); ?></td>
                <td><?= htmlspecialchars($row['game']); ?></td>
               
                <td>
                    <!-- Approve dropdown -->
                    <form method="post" action="update_status.php">
                        <input type="hidden" name="game" value="cricket">
                        <input type="hidden" name="team_name" value="<?= htmlspecialchars($row['team_name']); ?>">
                        <!-- <select name="is_approved" onchange="this.form.submit()">
                            <option value="pending" <?= ($row['is_approved']=='pending') ? 'selected' : ''; ?>>Pending</option>
                            <option value="approved" <?= ($row['is_approved']=='approved') ? 'selected' : ''; ?>>Approved</option>
                        </select> -->
                        <?= htmlspecialchars($row['is_approved']); ?>
                    </form>
                </td>
                
                <td>         
                    <a href="cteams_detail.php?team_name=<?php echo urlencode($row['team_name']); ?>" class="btn btn-primary btn-sm">Details</a>
                    <a href="delete.php?game=cricket&team_name=<?= urlencode($row['team_name']); ?>"
                       class="btn btn-danger btn-sm"
                       onclick="return confirm('Are you sure you want to delete this team?');">
                       🗑
                    </a>
                </td>
            </tr>
        <?php } ?>
    </tbody>
</table>

<!-- Badminton Teams -->
<h3>Badminton Teams</h3>
<table class="table table-bordered table-striped">
    <thead class="table-dark">
        <tr>
            <th>Full Name</th>
            <th>Team Name</th>
            <th>Email</th>
            <th>Player 1</th>
            <th>Player 2</th>
            <th>Role</th>
            <th>Game</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
          <?php 
        mysqli_data_seek($badmintonTeams, 0);
        while($row = mysqli_fetch_assoc($badmintonTeams)) 
            if(strtolower($row['role']) == 'team') {
                $status = isset($row['is_approved']) ? $row['is_approved'] : 'pending';
        ?>
            <tr>
                <td><?= htmlspecialchars($row['fullName']); ?></td>
                <td><?= htmlspecialchars($row['teamName']); ?></td>
                <td><?= htmlspecialchars($row['email']); ?></td>
                <td><?= htmlspecialchars($row['player1']); ?></td>
                <td><?= htmlspecialchars($row['player2']); ?></td>
                <td><?= htmlspecialchars($row['role']); ?></td>
                <td><?= htmlspecialchars($row['game']); ?></td>
                <td>
                    <!-- Approve dropdown -->
                    <form method="post" action="update_status.php">
                        <input type="hidden" name="game" value="badminton">
                        <input type="hidden" name="team_name" value="<?= htmlspecialchars($row['teamName']); ?>">
                        <select name="is_approved" onchange="this.form.submit()">
                            <option value="pending" <?= ($row['is_approved']=='pending') ? 'selected' : ''; ?>>Pending</option>
                            <option value="approved" <?= ($row['is_approved']=='approved') ? 'selected' : ''; ?>>Approved</option>
                        </select>
                    </form>
                </td>
                
                <td>   
                    <a href="bteams_detail.php?teamName=<?php echo urlencode($row['teamName']); ?>" class="btn btn-primary btn-sm">Details</a>
                    <a href="delete.php?game=badminton&team_name=<?= urlencode($row['teamName']); ?>"
                       class="btn btn-danger btn-sm"
                       onclick="return confirm('Are you sure you want to delete this team?');">
                       🗑
                    </a>
                </td>
            </tr>
        
        <?php } ?>
    </tbody>
</table>
<!-- Badminton Players -->
<h3>Badminton Players</h3>
<table class="table table-bordered table-striped">
    <thead class="table-dark">
        <tr>
            <th>Full Name</th>
            <th>Email</th>
            <th>Player</th>
            <th>Role</th>
            <th>Game</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php 
        mysqli_data_seek($badmintonTeams, 0);
        $hasSingles = false;
        while($row = mysqli_fetch_assoc($badmintonTeams)) {
            if (strtolower($row['role']) === 'single' || strtolower($row['role']) === 'player') { 
                $hasSingles = true;
                $status = isset($row['is_approved']) ? $row['is_approved'] : 'pending';
        ?>
            <tr>
                <td><?= htmlspecialchars($row['fullName'] ?? 'N/A'); ?></td>
                <td><?= htmlspecialchars($row['email'] ?? 'N/A'); ?></td>
                <td><?= htmlspecialchars($row['player1'] ?? 'N/A'); ?></td>
                <td><?= htmlspecialchars($row['role'] ?? 'N/A'); ?></td>
                <td><?= htmlspecialchars($row['game'] ?? 'N/A'); ?></td>
                <td>
                    <form method="post" action="update_status.php">
                        <input type="hidden" name="game" value="badminton">
                        <input type="hidden" name="team_name" value="<?= htmlspecialchars($row['teamName'] ?? $row['player1'] ?? ''); ?>">
                        <select name="is_approved" onchange="this.form.submit()">
                            <option value="pending" <?= ($status === 'pending') ? 'selected' : ''; ?>>Pending</option>
                            <option value="approved" <?= ($status === 'approved') ? 'selected' : ''; ?>>Approved</option>
                            <option value="not_approved" <?= ($status === 'not_approved') ? 'selected' : ''; ?>>Not Approved</option>
                        </select>
                    </form>
                </td>
                <td>
                    <a href="delete.php?game=badminton&team_name=<?= urlencode($row['teamName'] ?? $row['player1'] ?? ''); ?>"
                       class="btn btn-danger btn-sm"
                       onclick="return confirm('Are you sure you want to delete this player?');">
                       🗑
                    </a>
                </td>
            </tr>
        <?php 
            }
        }
        if (!$hasSingles) {
            echo "<tr><td colspan='7' class='text-center text-muted'>No Singles players found.</td></tr>";
        }
        ?>
    </tbody>
</table>


<!-- Table Tennis Teams -->
<h3>Table Tennis Teams</h3>
<table class="table table-bordered table-striped">
    <thead class="table-dark">
        <tr>
            <th>Full Name</th>
            <th>Team Name</th>
            <th>Email</th>
            <th>Player 1</th>
            <th>Player 2</th>
            <th>Game</th>
            <th>Role</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
       <?php 
        mysqli_data_seek($tabletennisTeams, 0);
        while($row = mysqli_fetch_assoc($tabletennisTeams)) 
            if(strtolower($row['role']) == 'double') { 
                $status = isset($row['is_approved']) ? $row['is_approved'] : 'pending';
        ?>
            <tr>
                <td><?= htmlspecialchars($row['fullName']); ?></td>
                <td><?= htmlspecialchars($row['teamName']); ?></td>
                <td><?= htmlspecialchars($row['email']); ?></td>
                <td><?= htmlspecialchars($row['player1']); ?></td>
                <td><?= htmlspecialchars($row['player2']); ?></td>
                <td><?= htmlspecialchars($row['game']); ?></td>
                <td><?= htmlspecialchars($row['role']); ?></td>
                <td>
                    <!-- Approve dropdown -->
                    <form method="post" action="update_status.php">
                        <input type="hidden" name="game" value="tabletennis">
                        <input type="hidden" name="team_name" value="<?= htmlspecialchars($row['teamName']); ?>">
                        <!-- <select name="is_approved" onchange="this.form.submit()">
                            <option value="pending" <?= ($row['is_approved']=='pending') ? 'selected' : ''; ?>>Pending</option>
                            <option value="approved" <?= ($row['is_approved']=='approved') ? 'selected' : ''; ?>>Approved</option>
                        </select> -->
                        <?= htmlspecialchars($row['is_approved']); ?>
                    </form>
                </td>
                <td>         
                    <a href="ttteams_detail.php?teamName=<?php echo urlencode($row['teamName']); ?>" class="btn btn-primary btn-sm">Details</a>
                    <a href="delete.php?game=tabletennis&team_name=<?= urlencode($row['teamName']); ?>"
                       class="btn btn-danger btn-sm"
                       onclick="return confirm('Are you sure you want to delete this team?');">
                       🗑
                    </a>
                </td>
            </tr>
        <?php } ?>
    </tbody>
</table>

<!-- Table Tennis Players -->
<h3>Table Tennis Players</h3>
<table class="table table-bordered table-striped">
    <thead class="table-dark">
        <tr>
            <th>Full Name</th>
            <th>Email</th>
            <th>Player</th>
            <th>Game</th>
            <th>Role</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php 
        mysqli_data_seek($tabletennisTeams, 0);
        $hasSingles = false;
        while($row = mysqli_fetch_assoc($tabletennisTeams)) {
            if (strtolower($row['role']) === 'single' || strtolower($row['role']) === 'player') { 
                $hasSingles = true;
                $status = isset($row['is_approved']) ? $row['is_approved'] : 'pending';
        ?>
            <tr>
                <td><?= htmlspecialchars($row['fullName'] ?? 'N/A'); ?></td>
                <td><?= htmlspecialchars($row['email'] ?? 'N/A'); ?></td>
                <td><?= htmlspecialchars($row['player1'] ?? 'N/A'); ?></td>
                <td><?= htmlspecialchars($row['game'] ?? 'N/A'); ?></td>
                <td><?= htmlspecialchars($row['role'] ?? 'N/A'); ?></td>
                <td>
                    <form method="post" action="update_statust.php">
                        <input type="hidden" name="game" value="tabletennis">
                        <input type="hidden" name="team_name" value="<?= htmlspecialchars($row['teamName'] ?? $row['player1'] ?? ''); ?>">
                        <select name="is_approved" onchange="this.form.submit()">
                            <option value="pending" <?= ($status === 'pending') ? 'selected' : ''; ?>>Pending</option>
                            <option value="approved" <?= ($status === 'approved') ? 'selected' : ''; ?>>Approved</option>
                            <option value="not_approved" <?= ($status === 'not_approved') ? 'selected' : ''; ?>>Not Approved</option>
                        </select>
                    </form>
                </td>
                <td>
                    <a href="delete.php?game=tabletennis&team_name=<?= urlencode($row['teamName'] ?? $row['player1'] ?? ''); ?>"
                       class="btn btn-danger btn-sm"
                       onclick="return confirm('Are you sure you want to delete this player?');">
                       🗑
                    </a>
                </td>
            </tr>
        <?php 
            }
        }
        if (!$hasSingles) {
            echo "<tr><td colspan='7' class='text-center text-muted'>No players found.</td></tr>";
        }
        ?>
    </tbody>
</table>
</table>						
<h3>Volleyball Teams</h3>
<table class="table table-bordered table-striped">
    <thead class="table-dark">
        <tr> 
            <th>Captain Name</th>
            <th>Team name</th>
            <th>fullName</th>
            <th>Email</th>
            <th>Height</th>
            <th>Age</th>
            <th>Handed</th>
            <th>Position</th>
            <th>Standing Reach</th>
            <th>Block Jump</th>
            <th>Approach Jump</th>
            <th>Chronic Illness</th>    
            <th>Status</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php while($row = mysqli_fetch_assoc($volleyTeams)) { ?>
            <tr>
                <td><?= htmlspecialchars($row['captain_name']); ?></td>
                <td><?= htmlspecialchars($row['team_name']); ?></td>
                <td><?= htmlspecialchars($row['fullName']); ?></td>
                <td><?= htmlspecialchars($row['email']); ?></td>
                <td><?= htmlspecialchars($row['captain_height']); ?></td>
                <td><?= htmlspecialchars($row['captain_age']); ?></td>
                <td><?= htmlspecialchars($row['captain_handed']); ?></td>
                <td><?= htmlspecialchars($row['captain_position']); ?></td>
                <td><?= htmlspecialchars($row['captain_standing_reach']); ?></td>
                <td><?= htmlspecialchars($row['captain_block_jump']); ?></td>
                <td><?= htmlspecialchars($row['captain_approach_jump']); ?></td>
                <td><?= htmlspecialchars($row['captain_chronic_illness']); ?></td>
                <td>
                    <!-- Approve dropdown -->
                    <form method="post" action="update_statusv.php">
                        <input type="hidden" name="game" value="volleyball">
                        <input type="hidden" name="team_name" value="<?= htmlspecialchars($row['team_name']); ?>">
                        <!-- <select name="is_approved" onchange="this.form.submit()">
                            <option value="pending" <?= ($row['is_approved']=='pending') ? 'selected' : ''; ?>>Pending</option>
                            <option value="approved" <?= ($row['is_approved']=='approved') ? 'selected' : ''; ?>>Approved</option>
                        </select> -->
                        <?= htmlspecialchars($row['is_approved']); ?>
                    </form>
                </td>
                <td>
                    <a href="vteams_detail.php?team_name=<?= urlencode($row['team_name']); ?>" class="btn btn-primary btn-sm">Details</a>
                    <a href="delete.php?game=volleyball&team_name=<?= urlencode($row['team_name']); ?>"
                       class="btn btn-danger btn-sm"
                       onclick="return confirm('Are you sure you want to delete this team?');">
                       🗑
                    </a>
                </td>
            </tr>
        <?php } ?>
    </tbody>
</table>

</body>
</html>