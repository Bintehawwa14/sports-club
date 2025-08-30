<?php
include_once('../../include/db_connect.php');

if (!isset($_GET['teamName'])) {
    die("Team not selected.");
}

$teamName = $_GET['teamName'];

// Fetch team details using prepared statement
$stmt = $con->prepare("SELECT fullName, email, role, hand1, play_style1, player1, dob1, height1, weight1, 
                       chronic_illness1, allergies1, medications1, surgeries1, previous_injuries1, 
                       hand2, play_style2, player2, dob2, height2, weight2, 
                       chronic_illness2, allergies2, medications2, surgeries2, previous_injuries2, 
                       is_approved 
                       FROM tabletennis_players 
                       WHERE teamName = ?");
$stmt->bind_param("s", $teamName);
$stmt->execute();
$result = $stmt->get_result();

// Determine team status
$teamStatus = 'pending'; // Default
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $teamStatus = $row['is_approved'];
    // Reset result pointer to reuse it in the table
    $result->data_seek(0);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Table Tennis Team Details - <?php echo htmlspecialchars($teamName); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-4">

    <h2>
        Table Tennis Team: <?php echo htmlspecialchars($teamName); ?>
        <span id="team-status" class="badge <?php echo ($teamStatus == 'approved') ? 'bg-success' : 'bg-warning'; ?>">
            <?php echo strtoupper($teamStatus); ?>
        </span>
    </h2>
    <a href="all_teams.php" class="btn btn-secondary mb-3">⬅ Back</a>

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
        <?php if ($result->num_rows > 0) { 
            while ($row = $result->fetch_assoc()) { ?>
                <!-- Player 1 -->
                <tr>
                    <td><?php echo htmlspecialchars($row['player1']); ?></td>
                    <td><?php echo htmlspecialchars($row['email']); ?></td>
                    <td><?php echo htmlspecialchars($row['dob1']); ?></td>
                    <td><?php echo htmlspecialchars($row['height1']); ?></td>
                    <td><?php echo htmlspecialchars($row['weight1']); ?></td>
                    <td><?php echo htmlspecialchars($row['hand1']); ?></td>
                    <td><?php echo htmlspecialchars($row['play_style1']); ?></td>
                    <td><?php echo htmlspecialchars($row['chronic_illness1'] ?: 'None'); ?></td>
                    <td><?php echo htmlspecialchars($row['allergies1'] ?: 'None'); ?></td>
                    <td><?php echo htmlspecialchars($row['medications1'] ?: 'None'); ?></td>
                    <td><?php echo htmlspecialchars($row['surgeries1'] ?: 'None'); ?></td>
                    <td><?php echo htmlspecialchars($row['previous_injuries1'] ?: 'None'); ?></td>
                    <td>
                        <select onchange="updateTeamStatus('<?php echo htmlspecialchars($teamName); ?>', this.value)">
                            <option value="pending" <?php if ($row['is_approved'] == 'pending') echo 'selected'; ?>>Pending</option>
                            <option value="approved" <?php if ($row['is_approved'] == 'approved') echo 'selected'; ?>>Approved</option>
                        </select>
                    </td>
                </tr>
                <!-- Player 2 -->
                <?php if ($row['player2']) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['player2']); ?></td>
                        <td><?php echo htmlspecialchars($row['email']); ?></td>
                        <td><?php echo htmlspecialchars($row['dob2']); ?></td>
                        <td><?php echo htmlspecialchars($row['height2']); ?></td>
                        <td><?php echo htmlspecialchars($row['weight2']); ?></td>
                        <td><?php echo htmlspecialchars($row['hand2']); ?></td>
                        <td><?php echo htmlspecialchars($row['play_style2']); ?></td>
                        <td><?php echo htmlspecialchars($row['chronic_illness2'] ?: 'None'); ?></td>
                        <td><?php echo htmlspecialchars($row['allergies2'] ?: 'None'); ?></td>
                        <td><?php echo htmlspecialchars($row['medications2'] ?: 'None'); ?></td>
                        <td><?php echo htmlspecialchars($row['surgeries2'] ?: 'None'); ?></td>
                        <td><?php echo htmlspecialchars($row['previous_injuries2'] ?: 'None'); ?></td>
                         <td >
                        <select onchange="updateTeamStatus('<?php echo htmlspecialchars($teamName); ?>', this.value)">
                            <option value="pending" <?php if ($row['is_approved'] == 'pending') echo 'selected'; ?>>Pending</option>
                            <option value="approved" <?php if ($row['is_approved'] == 'approved') echo 'selected'; ?>>Approved</option>
                        </select>
                    </td>
                    </tr>
                <?php } ?>
            <?php } 
        } else { ?>
            <tr><td colspan="13" class="text-center">No players found for this team</td></tr>
        <?php } ?>
        </tbody>
    </table>

    <script>
    function updateTeamStatus(teamName, status) {
        let formData = new FormData();
        formData.append('team_name', teamName);
        formData.append('is_approved', status);
        formData.append('game', 'tabletennis');

        fetch('update_statust.php', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok: ' + response.statusText);
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                let statusElement = document.getElementById('team-status');
                statusElement.textContent = data.team_status.toUpperCase();
                statusElement.className = 'badge ' + (data.team_status === 'approved' ? 'bg-success' : 'bg-warning');
            } else {
                alert('Error: ' + data.error);
            }
        })
        .catch(err => {
            console.error('Fetch error:', err);
            alert('Request failed: ' + err.message);
        });
    }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>