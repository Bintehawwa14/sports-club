    <?php
    include_once('../../include/db_connect.php');

    if (!isset($_GET['team_name'])) {
        die("Team not selected.");
    }

    $teamName = mysqli_real_escape_string($con, $_GET['team_name']);

    // Fetch players of the team
    $sql = "SELECT player_name, age, role, batting_style, bowling_style, height, weight, disability, is_approved 
            FROM cricket_players 
            WHERE team_name = '$teamName'";
    $result = mysqli_query($con, $sql);

    // Check overall team approval status
    $checkPlayers = "SELECT COUNT(*) as not_approved_count 
                    FROM cricket_players 
                    WHERE team_name = '$teamName' AND is_approved != 'approved'";
    $checkResult = mysqli_query($con, $checkPlayers);
    $checkRow = mysqli_fetch_assoc($checkResult);
    $teamStatus = ($checkRow['not_approved_count'] == 0) ? 'approved' : 'pending';
    ?>

    <!DOCTYPE html>
    <html>
    <head>
        <title>Team Details - <?php echo htmlspecialchars($teamName); ?></title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    </head>
    <body class="container mt-4">

        <h2>
            Team: <?php echo htmlspecialchars($teamName); ?>
            <span id="team-status" class="badge <?php echo ($teamStatus == 'approved') ? 'bg-success' : 'bg-warning'; ?>">
                <?php echo strtoupper($teamStatus); ?>
            </span>
        </h2>
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
                    <th>Approval Status</th>
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
                            <select onchange="updatePlayerStatus('<?php echo htmlspecialchars($row['player_name']); ?>', '<?php echo htmlspecialchars($teamName); ?>', this.value)">
                                <option value="pending" <?php if($row['is_approved']=='pending') echo 'selected'; ?>>Pending</option>
                                <option value="approved" <?php if($row['is_approved']=='approved') echo 'selected'; ?>>Approved</option>
                            </select>
                        </td>
                    </tr>
                <?php } 
            } else { ?>
                <tr><td colspan="9" class="text-center">No players found for this team</td></tr>
            <?php } ?>
            </tbody>
        </table>

        <script>
        function updatePlayerStatus(playerName, teamName, status) {
            let formData = new FormData();
            formData.append('player_name', playerName);
            formData.append('team_name', teamName);
            formData.append('is_approved', status);

            fetch('update_status.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    let statusElement = document.getElementById('team-status');
                    statusElement.textContent = data.team_status.toUpperCase();
                    statusElement.className = 'badge ' + (data.team_status === 'approved' ? 'bg-success' : 'bg-warning');
                    location.reload();
                } else {
                    alert('Error: ' + data.error);
                }
            })
            .catch(err => alert('Request failed: ' + err));
        }
        </script>
        <script>
    function updateStatus(playerId, status, teamId) {
        var xhr = new XMLHttpRequest();
        xhr.open("POST", "update_status.php", true);
        xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4 && xhr.status === 200) {
                location.reload(); // Refresh page to see updated team status
            }
        };
        xhr.send("player_id=" + playerId + "&status=" + status + "&team_id=" + teamId);
        location.reload();
    }
    </script>


        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    </body>
    </html>
