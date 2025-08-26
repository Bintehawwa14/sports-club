<?php
include 'db_connection.php';

$tournament_id = $_POST['tournament_id'];
$prev_round = $_POST['current_round'];
$next_round = $prev_round + 1;

$query = "SELECT winner_id FROM matches WHERE tournament_id = ? AND round = ? AND winner_id IS NOT NULL";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $tournament_id, $prev_round);
$stmt->execute();
$res = $stmt->get_result();

$winners = [];
while ($row = $res->fetch_assoc()) {
    $winners[] = $row['winner_id'];
}

shuffle($winners);

for ($i = 0; $i < count($winners); $i += 2) {
    $team1 = $winners[$i];
    $team2 = isset($winners[$i+1]) ? $winners[$i+1] : null;

    if (!$team2) {
        echo "Team ID $team1 gets a bye.<br>";
        continue;
    }

    $insert = "INSERT INTO matches (tournament_id, team1_id, team2_id, round, bracket_type) VALUES (?, ?, ?, ?, 'Single Elimination')";
    $stmt = $conn->prepare($insert);
    $stmt->bind_param("iiii", $tournament_id, $team1, $team2, $next_round);
    $stmt->execute();
}

echo "Next round scheduled.";
?>
