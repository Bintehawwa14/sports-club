<?php
session_start();
include_once('../include/db_connect.php');

// Fetch all events for dropdown
$eventQuery = "SELECT id, event_name FROM events";
$eventResult = mysqli_query($con, $eventQuery);

// Event filter
$selectedEvent = isset($_POST['event_id']) ? $_POST['event_id'] : "";

// Fetch matches with event name (JOIN)
$sql = "SELECT 
            m.id, m.event_id, 
            e.event_name AS event_name_from_events, 
            m.event_name AS event_name_from_matches,
            m.game, m.tournament_id,
            m.team1_name, m.team2_name, m.winner_id, m.loser_id, m.round,
            m.match_date, m.bracket_type, m.winner_name, m.loser_name,
            m.match_status, m.result_winner, m.team1_score, m.team2_score,
            m.total_over, m.toss_winner
        FROM matches m
        LEFT JOIN events e ON m.event_id = e.id";

if (!empty($selectedEvent)) {
    $sql .= " WHERE m.event_id = '$selectedEvent'";
}
$sql .= " ORDER BY m.id DESC";

$result = mysqli_query($con, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Event Report</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-4">
    <h2 class="mb-3 text-center">📋 Event Report</h2>

    <!-- Dropdown for event selection -->
    <form method="POST" class="mb-3 text-center">
        <label for="event_id" class="fw-bold">Select Event:</label>
        <select name="event_id" id="event_id" class="form-select d-inline-block w-auto mx-2" required>
            <option value="">-- Choose Event --</option>
            <?php while($ev = mysqli_fetch_assoc($eventResult)): ?>
                <option value="<?php echo $ev['id']; ?>" 
                        <?php if ($selectedEvent == $ev['id']) echo "selected"; ?>>
                    <?php echo htmlspecialchars($ev['event_name']); ?>
                </option>
            <?php endwhile; ?>
        </select>
        <button type="submit" class="btn btn-primary">Show Report</button>
    </form>

    <!-- Matches Table -->
    <table class="table table-bordered table-striped table-hover">
        <thead class="table-dark">
            <tr>
            
                <th>Event Name (Events Table)</th>
                <th>Event Name (Matches Table)</th>
                <th>Game</th>
                <th>Team 1</th>
                <th>Team 2</th>
                <th>Winner</th>
                <th>Loser</th>
                <th>Round</th>
                <th>Match Date</th>
                <th>Match Status</th>
                <th>Result Winner</th>
                <th>Team1 Score</th>
                <th>Team2 Score</th>
                <th>Total Over</th>
                <th>Toss Winner</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result && mysqli_num_rows($result) > 0): ?>
                <?php while($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        
                        <td><?php echo htmlspecialchars($row['event_name_from_events']); ?></td>
                        <td><?php echo htmlspecialchars($row['event_name_from_matches']); ?></td>
                        <td><?php echo htmlspecialchars($row['game']); ?></td>
                        <td><?php echo htmlspecialchars($row['team1_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['team2_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['winner_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['loser_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['round']); ?></td>
                        <td><?php echo date("d M Y", strtotime($row['match_date'])); ?></td>
                        <td><?php echo htmlspecialchars($row['match_status']); ?></td>
                        <td><?php echo htmlspecialchars($row['result_winner']); ?></td>
                        <td><?php echo htmlspecialchars($row['team1_score']); ?></td>
                        <td><?php echo htmlspecialchars($row['team2_score']); ?></td>
                        <td><?php echo htmlspecialchars($row['total_over']); ?></td>
                        <td><?php echo htmlspecialchars($row['toss_winner']); ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="16" class="text-center text-danger">⚠ No matches found for this event!</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
</body>
</html>
