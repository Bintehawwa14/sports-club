<?php
include_once('../../include/db_connect.php');

// Fetch all teams per game
$cricketTeams = mysqli_query($con, "SELECT full_name, team_name, captain_name, vice_captain_name, email, game, is_approved FROM cricket_teams WHERE game='cricket'");
$badmintonTeams = mysqli_query($con, "SELECT fullName,email,role,teamName,player1,dob1,height1,weight1,chronic_illness1,allergies1,medications1,surgeries1,previous_injuries1,height2,weight2,chronic_illness2,allergies2,medications2,surgeries2,previous_injuries2,player2,dob2,category,game, is_approved FROM badminton_players");
$tabletennisTeams = mysqli_query($con, "SELECT fullName,email,role,teamName,hand1,play_style1,player1,dob1,height1,weight1,chronic_illness1,allergies1,medications1,surgeries1,previous_injuries1,player2,dob2,height2 weight2,chronic_illness2,allergies2,medications2,surgeries2,previous_injuries2,hand2,play_style2,created_at, game,is_approved FROM tabletennis_players");
$volleyTeams = mysqli_query($con, "SELECT fullName,email,team_name,club_team,captain_name,captain_age,captain_height,captain_handed,captain_position,captain_standing_reach,captain_block_jump,captain_approach_jump,captain_chronic_illness,captain_allergies,captain_medications,captain_surgeries,captain_previous_injuries,registration_date,is_approved FROM volleyball_teams");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="All Teams Page" />
    <meta name="author" content="" />
    <title>All Teams</title>
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css" rel="stylesheet" />
    <link href="../css/styles.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/js/all.min.js" crossorigin="anonymous"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet" />
    <style>
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            margin: 0;
            padding: 0;
            background-image: url('../images/volleyballform.jpg');
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

        #layoutSidenav {
            display: flex;
            flex: 1;
        }

        #layoutSidenav_content {
            flex: 1;
            padding: 15px;
        }

        .container-fluid {
            max-width: 1200px;
            margin: 0 auto;
        }

        .teams-container {
            background-color: rgba(255, 255, 255, 0.95);
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
            margin: 20px auto;
            width: 95%;
            max-width: 1200px;
        }

        .teams-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .teams-header h3 {
            font-size: 24px;
            font-weight: 600;
            color: #1a3c6d;
            margin: 0;
            letter-spacing: 0.3px;
        }

        .table-responsive {
            margin-top: 15px;
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
        }

        th, td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #e0e0e0;
            font-size: 14px;
        }

        th {
            background-color: #f0f4f8;
            color: #1a3c6d;
            font-weight: 600;
        }

        td {
            color: #374151;
            background-color: #ffffff;
        }

        .btn-primary {
            background-color: #007bff;
            border: none;
            padding: 6px 12px;
            font-size: 14px;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        .btn-primary:hover {
            background-color: #0056b3;
        }

        .btn-danger {
            background-color: #b91c1c;
            border: none;
            padding: 6px 12px;
            font-size: 14px;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        .btn-danger:hover {
            background-color: #7f1d1d;
        }

        .btn-secondary {
            background-color: #6b7280;
            border: none;
            padding: 6px 12px;
            font-size: 14px;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        .btn-secondary:hover {
            background-color: #4b5563;
        }

        select {
            padding: 6px;
            border: 1px solid #e0e0e0;
            border-radius: 5px;
            font-size: 14px;
            color: #374151;
        }

        select:focus {
            border-color: #007bff;
            outline: none;
        }

        .text-muted {
            color: #6b7280 !important;
        }

        @media (max-width: 768px) {
                                        h3{
                                            text-align : center;
                                        }

            .teams-container {
                width: 95%;
                padding: 15px;
                margin: 15px auto;
            }

            .teams-header h3 {
                font-size: 20px;
            }

            th, td {
                display: block;
                width: 100%;
                text-align: left;
                padding: 8px;
            }

            th {
                background-color: #e6eef8;
                border-bottom: none;
            }

            td {
                border-bottom: 1px solid #e0e0e0;
            }

            .btn-primary, .btn-danger, .btn-secondary {
                font-size: 13px;
                padding: 5px 10px;
            }

            select {
                font-size: 13px;
            }

            .container-fluid {
                padding: 10px;
            }
        }

        @media (max-width: 576px) {
            .teams-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }
        }
    </style>
</head>
<body class="sb-nav-fixed">
   
            <main>
                <div class="container-fluid px-4">
                    <div class="teams-container">
                        <div class="teams-header">
                            <h3>Registered Players & Teams</h3>
                            <a href="../registered.php" class="btn btn-secondary">⬅ Back</a>
                        </div>

                        <!-- Cricket Teams -->
                        <div class="teams-header">
                            <h3>Cricket Teams</h3>
                        </div>
                        <div class="table-responsive">
                            <table>
                                <thead>
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
                                            <td><?= htmlspecialchars($row['is_approved']); ?></td>
                                            <td>
                                                <a href="cteams_detail.php?team_name=<?= urlencode($row['team_name']); ?>" class="btn btn-primary btn-sm">Details</a>
                                                <a href="delete.php?game=cricket&team_name=<?= urlencode($row['team_name']); ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this team?');">🗑</a>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Badminton Teams -->
                        <div class="teams-header">
                            <h3>Badminton Teams</h3>
                        </div>
                        <div class="table-responsive">
                            <table>
                                <thead>
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
                                                <a href="bteams_detail.php?teamName=<?= urlencode($row['teamName']); ?>" class="btn btn-primary btn-sm">Details</a>
                                                <a href="delete.php?game=badminton&team_name=<?= urlencode($row['teamName']); ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this team?');">🗑</a>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Badminton Players -->
                        <div class="teams-header">
                            <h3>Badminton Players</h3>
                        </div>
                        <div class="table-responsive">
                            <table>
                                <thead>
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
                                                <form method="post" action="update_statusb.php">
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
                                                <a href="delete.php?game=badminton&team_name=<?= urlencode($row['teamName'] ?? $row['player1'] ?? ''); ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this player?');">🗑</a>
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
                        </div>

                        <!-- Table Tennis Teams -->
                        <div class="teams-header">
                            <h3>Table Tennis Teams</h3>
                        </div>
                        <div class="table-responsive">
                            <table>
                                <thead>
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
                                            <td><?= htmlspecialchars($row['is_approved']); ?></td>
                                            <td>
                                                <a href="ttteams_detail.php?teamName=<?= urlencode($row['teamName']); ?>" class="btn btn-primary btn-sm">Details</a>
                                                <a href="delete.php?game=tabletennis&team_name=<?= urlencode($row['teamName']); ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this team?');">🗑</a>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>

<!-- Table Tennis Players -->
<div class="teams-header">
<h3>Table Tennis players</h3>
 </div>
 <div class="table-responsive">
    <table>
    <thead>
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
<div class="teams-header">
     <h3>Volleyball teams</h3>
    </div>
      <div class="table-responsive">
        <table>
        <thead>
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