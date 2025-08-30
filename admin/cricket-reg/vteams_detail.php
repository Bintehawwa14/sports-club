<?php
include_once('../../include/db_connect.php');

if (!isset($_GET['team_name'])) {
    die("Team not selected.");
}

$teamName = $_GET['team_name'];

// Fetch players of the team using prepared statement
$stmt = $con->prepare("SELECT player_name, age, position, height, handedness, weight, standing_reach, 
                       block_jump, approach_jump, chronic_illness, allergies, medications, surgeries, 
                       previous_injuries, is_approved 
                       FROM volleyball_players 
                       WHERE team_name = ?");
$stmt->bind_param("s", $teamName);
$stmt->execute();
$result = $stmt->get_result();

// Check overall team approval status
$stmt_check = $con->prepare("SELECT COUNT(*) as not_approved_count 
                             FROM volleyball_players 
                             WHERE team_name = ? AND is_approved != 'approved'");
$stmt_check->bind_param("s", $teamName);
$stmt_check->execute();
$checkResult = $stmt_check->get_result();
$checkRow = $checkResult->fetch_assoc();
$teamStatus = ($checkRow['not_approved_count'] == 0) ? 'approved' : 'pending';
?>

<!DOCTYPE html>
<html>
<head>
    <title>Volleyball Team Details - <?php echo htmlspecialchars($teamName); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-4">

    <h2>
        Volleyball Team: <?php echo htmlspecialchars($teamName); ?>
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
        <?php if ($result->num_rows > 0) { 
            while ($row = $result->fetch_assoc()) { ?>
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
                        <select onchange="updatePlayerStatus('<?php echo htmlspecialchars($row['player_name']); ?>', '<?php echo htmlspecialchars($teamName); ?>', this.value)">
                            <option value="pending" <?php if ($row['is_approved'] == 'pending') echo 'selected'; ?>>Pending</option>
                            <option value="approved" <?php if ($row['is_approved'] == 'approved') echo 'selected'; ?>>Approved</option>
                        </select>
                    </td>
                </tr>
            <?php } 
        } else { ?>
            <tr><td colspan="15" class="text-center">No players found for this team</td></tr>
        <?php } ?>
        </tbody>
    </table>

    <script>
    function updatePlayerStatus(playerName, teamName, status) {
        let formData = new FormData();
        formData.append('player_name', playerName);
        formData.append('team_name', teamName);
        formData.append('is_approved', status);
        formData.append('game', 'volleyball');

        fetch('update_statusv.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                let statusElement = document.getElementById('team-status');
                statusElement.textContent = data.team_status.toUpperCase();
                statusElement.className = 'badge ' + (data.team_status === 'approved' ? 'bg-success' : 'bg-warning');
            } else {
                alert('Error: ' + data.error);
            }
        })
        .catch(err => alert('Request failed: ' + err));
    }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>